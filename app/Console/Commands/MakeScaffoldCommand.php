<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeScaffoldCommand extends Command
{
    protected $signature = 'make:scaffold {migration : The name of the migration file (e.g., 2025_09_03_142650_create_produtos_table)} {--belongs-to=* : Related models for belongsTo relationships (e.g., --belongs-to=User --belongs-to=Category)}';
    protected $description = 'Scaffold a full CRUD resource from a migration file, using Livewire components for views.';

    public function handle()
    {
        $migrationFileName = $this->argument('migration');
        $migrationPath = $this->findMigrationFile($migrationFileName);

        if (!$migrationPath) {
            $this->error("Migration file '{$migrationFileName}.php' not found in database/migrations.");
            return 1;
        }

        $content = File::get($migrationPath);

        $tableName = $this->getTableNameFromMigration($content);
        if (!$tableName) {
            $this->error("Could not determine table name from migration: {$migrationFileName}");
            return 1;
        }

        $columns = $this->getColumnsFromMigration($content);
        if (empty($columns)) {
            $this->warn("No columns found in migration. Form views will be generated without fields.");
        }

        $modelName = Str::studly(Str::singular($tableName));
        $resourceName = Str::kebab($tableName);

        // Process belongsTo relationships - AGORA DETECTA AUTOMATICAMENTE
        $belongsToModels = $this->option('belongs-to') ?? [];
        $relationships = $this->processRelationships($belongsToModels, $columns, $content);

        $this->info("Scaffolding resource for table: {$tableName}");
        $this->info("Model: {$modelName}, Controller: {$modelName}Controller, Route: {$resourceName}");

        if (!empty($relationships['belongsTo'])) {
            $this->info("Relationships detected: " . implode(', ', array_keys($relationships['belongsTo'])));
        }

        $this->generateModel($modelName, $columns, $relationships);
        $this->generateController($modelName, $tableName, $columns, $relationships);
        $this->addRoute($tableName, $modelName);
        $this->generateViews($tableName, $columns, $relationships);

        $this->info("CRUD for '{$modelName}' scaffolded successfully!");
        $this->info("Remember to run 'php artisan migrate' if you haven't already.");
        $this->comment("Route added: Route::resource('{$tableName}', App\\Http\\Controllers\\{$modelName}Controller::class);");

        return 0;
    }

    protected function findMigrationFile($fileName)
    {
        $migrationPath = database_path('migrations');
        $files = File::glob("{$migrationPath}/*_{$fileName}.php");

        if (empty($files)) {
            $files = File::glob("{$migrationPath}/{$fileName}.php");
        }

        return $files[0] ?? null;
    }

    protected function getTableNameFromMigration($content)
    {
        if (preg_match("/Schema::create\('([a-zA-Z0-9_]+)'/", $content, $matches)) {
            return $matches[1];
        }
        return null;
    }

    protected function getColumnsFromMigration($content)
    {
        $columns = [];
        if (preg_match_all('/\$table->(\w+)\(\'([a-zA-Z0-9_]+)\'/i', $content, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $columnName = $match[2];
                $columnType = $match[1];

                // Skip system columns
                if (!in_array($columnName, ['id', 'timestamps', 'remember_token', 'created_at', 'updated_at'])) {
                    $columns[$columnName] = $this->mapColumnTypeToInputType($columnType);
                }
            }
        }
        return $columns;
    }

    protected function mapColumnTypeToInputType($dbType)
    {
        switch (strtolower($dbType)) {
            case 'string':
            case 'char':
                return 'text';
            case 'text':
            case 'longtext':
                return 'textarea';
            case 'integer':
            case 'unsignedinteger':
            case 'biginteger':
            case 'unsignedbiginteger':
            case 'smallinteger':
            case 'tinyinteger':
            case 'decimal':
            case 'double':
            case 'float':
                return 'number';
            case 'date':
                return 'date';
            case 'datetime':
            case 'timestamp':
                return 'datetime-local';
            case 'time':
                return 'time';
            case 'boolean':
                return 'checkbox';
            default:
                return 'text';
        }
    }

    protected function processRelationships($belongsToModels, &$columns, $migrationContent)
    {
        $relationships = [
            'belongsTo' => []
        ];

        // 1. Primeiro processa relações explícitas do usuário (--belongs-to)
        foreach ($belongsToModels as $relatedModel) {
            $relatedModel = Str::studly($relatedModel);
            $foreignKey = Str::snake($relatedModel) . '_id';

            // Remove a FK das colunas regulares
            if (isset($columns[$foreignKey])) {
                unset($columns[$foreignKey]);
            }

            $relationships['belongsTo'][$relatedModel] = [
                'foreign_key' => $foreignKey,
                'method_name' => Str::camel($relatedModel)
            ];
        }

        // 2. Detecta FKs automáticas da migration
        $this->detectForeignKeysFromMigration($migrationContent, $columns, $relationships);

        return $relationships;
    }

    protected function detectForeignKeysFromMigration($content, &$columns, &$relationships)
    {
        // Padrão 1: foreignId() - Laravel 8+
        if (preg_match_all('/\$table->foreignId\(\'([a-zA-Z0-9_]+)\'\)/', $content, $matches)) {
            foreach ($matches[1] as $foreignKey) {
                $this->addDetectedRelationship($foreignKey, $columns, $relationships);
            }
        }

        // Padrão 2: unsignedBigInteger + foreign
        if (preg_match_all('/\$table->(unsignedBigInteger|unsignedInteger)\(\'([a-zA-Z0-9_]+)\'\)/', $content, $matches)) {
            foreach ($matches[2] as $foreignKey) {
                // Verifica se há uma constraint foreign associada
                if (strpos($content, "\$table->foreign('{$foreignKey}')") !== false) {
                    $this->addDetectedRelationship($foreignKey, $columns, $relationships);
                }
            }
        }

        // Padrão 3: Colunas que terminam com _id e são do tipo inteiro
        foreach ($columns as $columnName => $columnType) {
            if (str_ends_with($columnName, '_id') && in_array($columnType, ['number'])) {
                $this->addDetectedRelationship($columnName, $columns, $relationships);
            }
        }
    }

    protected function addDetectedRelationship($foreignKey, &$columns, &$relationships)
    {
        // Extrai o nome do modelo do foreign key (categoria_id -> Categoria)
        $relatedModel = Str::studly(str_replace('_id', '', $foreignKey));

        // Verifica se já não existe essa relação (para evitar duplicatas)
        if (!isset($relationships['belongsTo'][$relatedModel])) {
            // Remove a FK das colunas regulares
            if (isset($columns[$foreignKey])) {
                unset($columns[$foreignKey]);
            }

            $relationships['belongsTo'][$relatedModel] = [
                'foreign_key' => $foreignKey,
                'method_name' => Str::camel($relatedModel)
            ];

            $this->info("Detected relationship: {$relatedModel} via {$foreignKey}");
        }
    }

    protected function generateModel($modelName, $columns, $relationships)
    {
        $fillable = array_keys($columns);

        // Add foreign keys to fillable
        foreach ($relationships['belongsTo'] as $config) {
            $fillable[] = $config['foreign_key'];
        }

        $fillable_string = implode("',\n        '", $fillable);

        // Add relationship methods
        $relationshipMethods = '';
        if (!empty($relationships['belongsTo'])) {
            foreach ($relationships['belongsTo'] as $relatedModel => $config) {
                $methodName = $config['method_name'];
                $relationshipMethods .= <<<EOT

    public function {$methodName}()
    {
        return \$this->belongsTo(\\App\\Models\\{$relatedModel}::class, '{$config['foreign_key']}');
    }
EOT;
            }
        }

        $template = <<<EOT
<?php

// gerado automaticamente pela biblioteca

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class {$modelName} extends Model
{
    use HasFactory;

    protected \$fillable = [
        '{$fillable_string}'
    ];
{$relationshipMethods}
}
EOT;

        $modelPath = app_path("Models/{$modelName}.php");
        File::put($modelPath, $template);
        $this->line("Model '{$modelName}' created successfully with HasFactory, fillable property, and relationships.");
    }

    protected function generateController($modelName, $tableName, $columns, $relationships)
    {
        $controllerPath = app_path("Http/Controllers/{$modelName}Controller.php");
        $controllerContent = $this->getControllerContent($modelName, $tableName, $columns, $relationships);

        File::put($controllerPath, $controllerContent);
        $this->line("Controller '{$modelName}Controller' created with resource methods and relationships.");
    }

    protected function getControllerContent($modelName, $tableName, $columns, $relationships)
    {
        $modelVariable = Str::camel($modelName);
        $modelNamespace = "App\\Models\\{$modelName}";

        // Add relationship data for views
        $relationshipData = '';
        $createWithData = '';
        $editWithData = '';

        if (!empty($relationships['belongsTo'])) {
            $relationshipImports = [];
            $relationshipVars = [];

            foreach ($relationships['belongsTo'] as $relatedModel => $config) {
                $relationshipImports[] = "use App\\Models\\{$relatedModel};";
                $varName = Str::plural($config['method_name']);
                $relationshipVars[] = "'{$varName}' => {$relatedModel}::all()";
            }

            $relationshipData = implode("\n", $relationshipImports) . "\n";

            // Corrigido: usar compact() corretamente
            if (!empty($relationshipVars)) {
                $relationshipVarsString = implode(', ', $relationshipVars);
                $createWithData = ", {$relationshipVarsString}";
                $editWithData = ", {$relationshipVarsString}";
            }
        }

        $validationRules = [];
        foreach ($columns as $column => $type) {
            if ($type !== 'checkbox') {
                $validationRules[] = "            '{$column}' => 'required',";
            }
        }

        // Add validation for foreign keys
        foreach ($relationships['belongsTo'] as $relatedModel => $config) {
            $validationRules[] = "            '{$config['foreign_key']}' => 'required|exists:" . Str::plural(Str::snake($relatedModel)) . ",id',";
        }

        $validationRulesString = implode("\n", $validationRules);

        $checkboxHandling = '';
        $hasCheckboxes = false;
        foreach ($columns as $column => $type) {
            if ($type === 'checkbox') {
                $hasCheckboxes = true;
                break;
            }
        }

        if ($hasCheckboxes) {
            $checkboxHandling = "        \$data = \$request->all();\n";
            foreach ($columns as $column => $type) {
                if ($type === 'checkbox') {
                    $checkboxHandling .= "        \$data['{$column}'] = \$request->has('{$column}');\n";
                }
            }
        }

        $createCall = $hasCheckboxes ? "{$modelName}::create(\$data);" : "{$modelName}::create(\$request->all());";
        $updateCall = $hasCheckboxes ? "\${$modelVariable}->update(\$data);" : "\${$modelVariable}->update(\$request->all());";

        // Handle relationship loading - CORRIGIDO
        $relationshipLoad = '';
        $relationshipLoadEdit = '';

        if (!empty($relationships['belongsTo'])) {
            $relationshipLoadArray = [];
            foreach ($relationships['belongsTo'] as $config) {
                $relationshipLoadArray[] = "'{$config['method_name']}'";
            }
            $relationshipLoadString = implode(', ', $relationshipLoadArray);
            $relationshipLoad = "->with([{$relationshipLoadString}])";
            $relationshipLoadEdit = "        \${$modelVariable}->load([{$relationshipLoadString}]);";
        }

        // CORREÇÃO PRINCIPAL: Gerar o controller corretamente
        $template = <<<EOT
<?php

// gerado automaticamente pela biblioteca

namespace App\Http\Controllers;

use {$modelNamespace};{$relationshipData}
use Illuminate\Http\Request;

class {$modelName}Controller extends Controller
{
    public function index()
    {
        \$collection = {$modelName}::with([{$this->getRelationshipArray($relationships)}])->get();
        return view('{$tableName}.index', compact('collection'));
    }

    public function create()
    {
        {$this->getRelationshipVariables($relationships)}
        return view('{$tableName}.create', compact({$this->getCompactVariables($relationships)}));
    }

    public function store(Request \$request)
    {
        \$request->validate([
{$validationRulesString}
        ]);

{$checkboxHandling}
        {$createCall}

        return redirect()->route('{$tableName}.index')
            ->with('success', '{$modelName} created successfully.');
    }

    public function show({$modelName} \${$modelVariable})
    {
{$relationshipLoadEdit}
        return view('{$tableName}.show', compact('{$modelVariable}'));
    }

    public function edit({$modelName} \${$modelVariable})
    {
{$relationshipLoadEdit}
        {$this->getRelationshipVariables($relationships)}
        return view('{$tableName}.edit', compact('{$modelVariable}', {$this->getCompactVariables($relationships, false)}));
    }

    public function update(Request \$request, {$modelName} \${$modelVariable})
    {
        \$request->validate([
{$validationRulesString}
        ]);

{$checkboxHandling}
        {$updateCall}

        return redirect()->route('{$tableName}.index')
            ->with('success', '{$modelName} updated successfully.');
    }

    public function destroy({$modelName} \${$modelVariable})
    {
        \${$modelVariable}->delete();

        return redirect()->route('{$tableName}.index')
            ->with('success', '{$modelName} deleted successfully.');
    }
}
EOT;

        return $template;
    }

    // Métodos auxiliares para gerar o código corretamente
    protected function getRelationshipArray($relationships)
    {
        if (empty($relationships['belongsTo'])) {
            return '';
        }

        $relations = [];
        foreach ($relationships['belongsTo'] as $config) {
            $relations[] = "'{$config['method_name']}'";
        }
        return implode(', ', $relations);
    }

    protected function getRelationshipVariables($relationships)
    {
        if (empty($relationships['belongsTo'])) {
            return '';
        }

        $vars = [];
        foreach ($relationships['belongsTo'] as $relatedModel => $config) {
            $varName = Str::plural($config['method_name']);
            $vars[] = "\${$varName} = {$relatedModel}::all();";
        }
        return implode("\n        ", $vars);
    }

    protected function getCompactVariables($relationships, $includeQuotes = true)
    {
        if (empty($relationships['belongsTo'])) {
            return '';
        }

        $vars = [];
        foreach ($relationships['belongsTo'] as $config) {
            $varName = Str::plural($config['method_name']);
            if ($includeQuotes) {
                $vars[] = "'{$varName}'";
            } else {
                $vars[] = "'{$varName}'";
            }
        }
        return implode(', ', $vars);
    }

    protected function addRoute($tableName, $modelName)
    {
        $route = "\nRoute::resource('{$tableName}', App\\Http\\Controllers\\{$modelName}Controller::class); // gerado automaticamente pela biblioteca\n";
        File::append(base_path('routes/web.php'), $route);
        $this->line("Route for '{$tableName}' added to web.php.");
    }

    protected function generateViews($tableName, $columns, $relationships)
    {
        $viewPath = resource_path("views/{$tableName}");
        File::makeDirectory($viewPath, 0755, true, true);

        $this->generateIndexView($viewPath, $tableName, $columns, $relationships);
        $this->generateCreateView($viewPath, $tableName, $columns, $relationships);
        $this->generateEditView($viewPath, $tableName, $columns, $relationships);
        $this->generateShowView($viewPath, $tableName, $columns, $relationships);

        $this->line("Views created in resources/views/{$tableName}/");
    }

    protected function generateIndexView($path, $tableName, $columns, $relationships)
    {
        $title = Str::ucfirst($tableName);

        $content = <<<'EOT'
        {{-- gerado automaticamente pela biblioteca --}}
        @extends('layouts.scaffold')

        @section('content')
            <div class="container mt-5">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1>TITLE</h1>
                    <livewire:botao tipo="primary" label="Novo" href="{{ route('TABLENAME.create') }}" />
                </div>

                <livewire:table
                    :collection="$collection"
                    :busca="true"
                    :selecionavel="false"
                    titulo="TITLE"
                />
            </div>
        @endsection
        EOT;

        $content = str_replace('TITLE', $title, $content);
        $content = str_replace('TABLENAME', $tableName, $content);

        File::put("{$path}/index.blade.php", $content);
    }

    protected function generateCreateView($path, $tableName, $columns, $relationships)
    {
        $title = Str::ucfirst(Str::singular($tableName));
        $formFields = '';

        // Add relationship selects FIRST
        foreach ($relationships['belongsTo'] as $relatedModel => $config) {
            $label = Str::ucfirst(str_replace('_', ' ', $config['method_name']));
            $varName = Str::plural($config['method_name']);
            $formFields .= <<<EOT
            @livewire('select', [
                'name' => '{$config['foreign_key']}',
                'label' => '{$label}',
                'id' => '{$config['foreign_key']}',
                'options' => \${$varName}->pluck('name', 'id')->toArray(),
                'placeholder' => 'Selecione uma {$label}'
            ])

EOT;
        }

        // Then add regular columns
        foreach ($columns as $name => $type) {
            $label = Str::ucfirst(str_replace('_', ' ', $name));
            if ($type === 'checkbox') {
                 $formFields .= "<livewire:checkbox name=\"{$name}\" label=\"{$label}\" id=\"{$name}\" :checked=\"old('{$name}', true)\" />\n                ";
            } else {
                 $formFields .= "<livewire:input type=\"{$type}\" name=\"{$name}\" label=\"{$label}\" id=\"{$name}\" value=\"{{ old('{$name}') }}\" />\n                ";
            }
        }

        $content = <<<'EOT'
        {{-- gerado automaticamente pela biblioteca --}}
        @extends('layouts.scaffold')

        @section('content')
            <div class="container mt-5">
        <h1 class="mb-4">Adicionar Novo TITLE</h1>
                <form action="{{ route('TABLENAME.store') }}" method="POST">
                    @csrf
                    FORMFIELDS
                    <div class="mt-4">
                        <livewire:botao tipo="primary" label="Salvar" tipoBotao="submit" />
                        <livewire:botao tipo="secondary" label="Voltar" href="{{ route('TABLENAME.index') }}" />
                    </div>
                </form>
            </div>
        @endsection
        EOT;

        $content = str_replace('TITLE', $title, $content);
        $content = str_replace('TABLENAME', $tableName, $content);
        $content = str_replace('FORMFIELDS', $formFields, $content);

        File::put("{$path}/create.blade.php", $content);
    }

    protected function generateEditView($path, $tableName, $columns, $relationships)
    {
        $title = Str::ucfirst(Str::singular($tableName));
        $modelVariable = Str::singular($tableName);
        $formFields = '';

        // Add relationship selects FIRST
        foreach ($relationships['belongsTo'] as $relatedModel => $config) {
            $label = Str::ucfirst(str_replace('_', ' ', $config['method_name']));
            $varName = Str::plural($config['method_name']);
            $formFields .= <<<EOT
            @livewire('select', [
                'name' => '{$config['foreign_key']}',
                'label' => '{$label}',
                'id' => '{$config['foreign_key']}',
                'options' => \${$varName}->pluck('name', 'id')->toArray(),
                'placeholder' => 'Selecione uma {$label}',
                'selected' => old('{$config['foreign_key']}', \${$modelVariable}->{$config['foreign_key']})
            ])

EOT;
        }

        // Then add regular columns
        foreach ($columns as $name => $type) {
            $label = Str::ucfirst(str_replace('_', ' ', $name));
            if ($type === 'checkbox') {
                $formFields .= "<livewire:checkbox name=\"{$name}\" label=\"{$label}\" id=\"{$name}\" :checked=\"old('{$name}', \${$modelVariable}->{$name})\" />\n                    ";
            } else {
                $formFields .= "<livewire:input type=\"{$type}\" name=\"{$name}\" label=\"{$label}\" id=\"{$name}\" value=\"{{ old('{$name}', \${$modelVariable}->{$name}) }}\" />\n                    ";
            }
        }

        $content = <<<'EOT'
        {{-- gerado automaticamente pela biblioteca --}}
        @extends('layouts.scaffold')

        @section('content')
            <div class="container mt-5">
                <h1 class="mb-4">Edit TITLE</h1>
                <form action="{{ route('TABLENAME.update', $MODELVARIABLE->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    FORMFIELDS
                    <div class="mt-4">
                        <livewire:botao tipo="primary" label="Atualizar" tipoBotao="submit" />
                        <livewire:botao tipo="secondary" label="Voltar" href="{{ route('TABLENAME.index') }}" />
                    </div>
                </form>
            </div>
        @endsection
        EOT;

        $content = str_replace('TITLE', $title, $content);
        $content = str_replace('TABLENAME', $tableName, $content);
        $content = str_replace('MODELVARIABLE', $modelVariable, $content);
        $content = str_replace('FORMFIELDS', $formFields, $content);

        File::put("{$path}/edit.blade.php", $content);
    }

    protected function generateShowView($path, $tableName, $columns, $relationships)
    {
        $title = Str::ucfirst(Str::singular($tableName));
        $modelVariable = Str::singular($tableName);

        $content = <<<'EOT'
        {{-- gerado automaticamente pela biblioteca --}}
        @extends('layouts.scaffold')

        @section('content')
            <div class="container mt-5">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <livewire:card
                            :data="$MODELVARIABLE"
                            titulo="Detalhes do TITLE"
                            :routeBase="'TABLENAME'"
                        />
                    </div>
                </div>
            </div>
        @endsection
        EOT;

        $content = str_replace('TITLE', $title, $content);
        $content = str_replace('TABLENAME', $tableName, $content);
        $content = str_replace('MODELVARIABLE', $modelVariable, $content);

        File::put("{$path}/show.blade.php", $content);
    }
}
