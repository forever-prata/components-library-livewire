<?php

namespace GovbrComponentsLivewire\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeScaffoldCommand extends Command
{
    protected $signature = 'make:scaffold {migration : The name of the migration file (e.g., 2025_09_03_142650_create_produtos_table)} {--belongs-to=* : Related models for belongsTo relationships (e.g., --belongs-to=User --belongs-to=Category)} {--belongs-to-many=* : Related models for BelongsToMany relationships (e.g., --belongs-to-many=Tag --belongs-to-many=Category)} {--has-many=* : Related models for hasMany relationships (e.g., --has-many=Post)} {--has-one=* : Related models for hasOne relationships (e.g., --has-one=Profile)}';
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

        if ($this->isPivotTable($content)) {
            $models = $this->getModelsFromPivotMigration($content);

            if (count($models) < 2) {
                $this->error("Could not determine the two models from the pivot table migration.");
                return 1;
            }

            $this->info("Pivot table detected, connecting models: " . implode(', ', $models));
            $primaryModel = $this->choice("Which model would you like to scaffold the CRUD for?", $models);

            $otherModel = collect($models)->first(fn($model) => $model !== $primaryModel);

            $this->info("Scaffolding for '{$primaryModel}' with Many-to-Many relationship to '{$otherModel}'.");

            $primaryModelTable = Str::plural(Str::snake($primaryModel));
            $migrationFileName = "create_{$primaryModelTable}_table";
            $migrationPath = $this->findMigrationFile($migrationFileName);

            if (!$migrationPath) {
                $this->error("Could not find the migration file for the selected model: '{$migrationFileName}.php'");
                return 1;
            }

            $content = File::get($migrationPath);

            $this->input->setOption('belongs-to-many', array_merge($this->option('belongs-to-many'), [$otherModel]));
        }

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

        $belongsToModels = $this->option('belongs-to') ?? [];
        $belongsToManyModels = $this->option('belongs-to-many') ?? [];
        $hasManyModels = $this->option('has-many') ?? [];
        $hasOneModels = $this->option('has-one') ?? [];
        $relationships = $this->processRelationships($belongsToModels, $belongsToManyModels, $hasManyModels, $hasOneModels, $columns, $content);

        $this->info("Scaffolding resource for table: {$tableName}");
        $this->info("Model: {$modelName}, Controller: {$modelName}Controller, Route: {$resourceName}");

        if (!empty($relationships['belongsTo'])) {
            $this->info("BelongsTo relationships detected: " . implode(', ', array_keys($relationships['belongsTo'])));
        }

        if (!empty($relationships['belongsToMany'])) {
            $this->info("BelongsToMany relationships detected: " . implode(', ', array_keys($relationships['belongsToMany'])));
        }

        if (!empty($relationships['hasMany'])) {
            $this->info("HasMany relationships detected: " . implode(', ', array_keys($relationships['hasMany'])));
        }

        if (!empty($relationships['hasOne'])) {
            $this->info("HasOne relationships detected: " . implode(', ', array_keys($relationships['hasOne'])));
        }

        $this->generateModel($modelName, $tableName, $columns, $relationships);
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

    protected function isPivotTable($content)
    {
        $foreignKeyCount = 0;
        $foreignKeyCount += preg_match_all('/\$table->foreignId\(\'([a-zA-Z0-9_]+)\'\)/', $content);
        $foreignKeyCount += preg_match_all('/\$table->foreign\(\'([a-zA-Z0-9_]+)\'\)/', $content);

        $columnCount = count($this->getColumnsFromMigration($content));

        return $foreignKeyCount >= 2 && $columnCount <= 2;
    }

    protected function getModelsFromPivotMigration($content)
    {
        $models = [];
        // Padrão 1: foreignId('post_id')
        if (preg_match_all('/\$table->foreignId\(\'([a-zA-Z0-9_]+_id)\'\)/', $content, $matches)) {
            foreach ($matches[1] as $foreignKey) {
                $models[] = Str::studly(str_replace('_id', '', $foreignKey));
            }
        }

        // Padrão 2: unsignedBigInteger('post_id') -> foreign('post_id')
        if (preg_match_all('/\$table->foreign\(\'([a-zA-Z0-9_]+_id)\'\)/', $content, $matches)) {
            foreach ($matches[1] as $foreignKey) {
                $modelName = Str::studly(str_replace('_id', '', $foreignKey));
                if (!in_array($modelName, $models)) {
                    $models[] = $modelName;
                }
            }
        }

        return array_unique($models);
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

    protected function processRelationships($belongsToModels, $belongsToManyModels, $hasManyModels, $hasOneModels, &$columns, $migrationContent)
    {
        $relationships = [
            'belongsTo' => [],
            'belongsToMany' => [],
            'hasMany' => [],
            'hasOne' => [],
        ];

        // 1. Processa relações belongsTo explícitas
        foreach ($belongsToModels as $relatedModel) {
            $relatedModel = Str::studly($relatedModel);
            $foreignKey = Str::snake($relatedModel) . '_id';

            if (isset($columns[$foreignKey])) {
                unset($columns[$foreignKey]);
            }

            $relationships['belongsTo'][$relatedModel] = [
                'foreign_key' => $foreignKey,
                'method_name' => Str::camel($relatedModel)
            ];
        }

        // 2. Processa relações belongsToMany explícitas
        foreach ($belongsToManyModels as $relatedModel) {
            $relatedModel = Str::studly($relatedModel);

            $relationships['belongsToMany'][$relatedModel] = [
                'method_name' => Str::camel(Str::plural($relatedModel)),
                'pivot_table' => $this->generatePivotTableName($migrationContent, $relatedModel)
            ];
        }

        // 3. Processa relações hasMany explícitas
        foreach ($hasManyModels as $relatedModel) {
            $relatedModel = Str::studly($relatedModel);
            $relationships['hasMany'][$relatedModel] = [
                'method_name' => Str::camel(Str::plural($relatedModel)),
            ];
        }

        // 4. Processa relações hasOne explícitas
        foreach ($hasOneModels as $relatedModel) {
            $relatedModel = Str::studly($relatedModel);
            $relationships['hasOne'][$relatedModel] = [
                'method_name' => Str::camel($relatedModel),
            ];
        }

        // 5. Detecta FKs automáticas da migration (para belongsTo)
        $this->detectForeignKeysFromMigration($migrationContent, $columns, $relationships);

        return $relationships;
    }

    protected function generatePivotTableName($content, $relatedModel)
    {
        // Tenta extrair o nome da tabela principal
        if (preg_match("/Schema::create\('([a-zA-Z0-9_]+)'/", $content, $matches)) {
            $mainTable = $matches[1];
            $tables = [Str::singular($mainTable), Str::singular(Str::snake($relatedModel))];
            sort($tables);
            return implode('_', $tables);
        }

        return null;
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
                if (strpos($content, "\$table->foreign('{$foreignKey}')") !== false) {
                    $this->addDetectedRelationship($foreignKey, $columns, $relationships);
                }
            }
        }

        // Padrão 3: Colunas que terminam com _id
        foreach ($columns as $columnName => $columnType) {
            if (str_ends_with($columnName, '_id') && in_array($columnType, ['number'])) {
                $this->addDetectedRelationship($columnName, $columns, $relationships);
            }
        }
    }

    protected function addDetectedRelationship($foreignKey, &$columns, &$relationships)
    {
        $relatedModel = Str::studly(str_replace('_id', '', $foreignKey));

        if (!isset($relationships['belongsTo'][$relatedModel])) {
            if (isset($columns[$foreignKey])) {
                unset($columns[$foreignKey]);
            }

            $relationships['belongsTo'][$relatedModel] = [
                'foreign_key' => $foreignKey,
                'method_name' => Str::camel($relatedModel)
            ];

            $this->info("Detected belongsTo relationship: {$relatedModel} via {$foreignKey}");
        }
    }

    protected function generateModel($modelName, $tableName, $columns, $relationships)
    {
        $fillable = array_keys($columns);

        // Add foreign keys to fillable
        foreach ($relationships['belongsTo'] as $config) {
            $fillable[] = $config['foreign_key'];
        }

        $fillable_string = implode("',\n        '", $fillable);

        // Add relationship methods
        $relationshipMethods = '';

        // BelongsTo relationships
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

        // BelongsToMany relationships
        if (!empty($relationships['belongsToMany'])) {
            foreach ($relationships['belongsToMany'] as $relatedModel => $config) {
                $methodName = $config['method_name'];
                $singularRelatedModel = Str::singular($relatedModel);
                $relationshipMethods .= <<<EOT

    public function {$methodName}()
    {
        return \$this->belongsToMany(\\App\\Models\\{$singularRelatedModel}::class, '{$config['pivot_table']}');
    }
EOT;
            }
        }

        // HasMany relationships
        if (!empty($relationships['hasMany'])) {
            foreach ($relationships['hasMany'] as $relatedModel => $config) {
                $methodName = $config['method_name'];
                $singularRelatedModel = Str::singular($relatedModel);
                $relationshipMethods .= <<<EOT

    public function {$methodName}()
    {
        return \$this->hasMany(\\App\\Models\\{$singularRelatedModel}::class);
    }
EOT;
            }
        }

        // HasOne relationships
        if (!empty($relationships['hasOne'])) {
            foreach ($relationships['hasOne'] as $relatedModel => $config) {
                $methodName = $config['method_name'];
                $singularRelatedModel = Str::singular($relatedModel);
                $relationshipMethods .= <<<EOT

    public function {$methodName}()
    {
        return \$this->hasOne(\\App\\Models\\{$singularRelatedModel}::class);
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
        $relationshipImports = [];
        $relationshipVars = [];
        $belongsToManyVars = [];

        // BelongsTo relationships
        if (!empty($relationships['belongsTo'])) {
            foreach ($relationships['belongsTo'] as $relatedModel => $config) {
                $relationshipImports[] = "use App\\Models\\{$relatedModel};";
                $varName = Str::plural($config['method_name']);
                $relationshipVars[] = "'{$varName}' => {$relatedModel}::all()";
            }
        }

        // BelongsToMany relationships
        if (!empty($relationships['belongsToMany'])) {
            foreach ($relationships['belongsToMany'] as $relatedModel => $config) {
                $relationshipImports[] = "use App\\Models\\{$relatedModel};";
                $varName = Str::plural(Str::camel($relatedModel));
                $belongsToManyVars[] = "'{$varName}' => {$relatedModel}::all()";
            }
        }

        $relationshipData = implode("\n", array_unique($relationshipImports)) . "\n";

        // Combine all relationship variables
        $allRelationshipVars = array_merge($relationshipVars, $belongsToManyVars);
        $createWithData = !empty($allRelationshipVars) ? ", " . implode(', ', $allRelationshipVars) : '';
        $editWithData = !empty($allRelationshipVars) ? ", " . implode(', ', $allRelationshipVars) : '';

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

        // BelongsToMany handling
        $belongsToManySync = '';
        if (!empty($relationships['belongsToMany'])) {
            foreach ($relationships['belongsToMany'] as $relatedModel => $config) {
                $methodName = $config['method_name'];
                $belongsToManySync .= "        \${$modelVariable}->{$methodName}()->sync(\$request->input('{$methodName}', []));\n";
            }
        }

        $createCall = $hasCheckboxes ? "{$modelName}::create(\$data);" : "{$modelName}::create(\$request->all());";
        $updateCall = $hasCheckboxes ? "\${$modelVariable}->update(\$data);" : "\${$modelVariable}->update(\$request->all());";

        // Add belongsToMany sync to store and update
        if (!empty($belongsToManySync)) {
            $createCall = "\${$modelVariable} = " . $createCall . "\n        " . $belongsToManySync;
            $updateCall = $updateCall . "\n        " . $belongsToManySync;
        }

        $relationshipLoadArray = [];
        foreach ($relationships['belongsTo'] as $config) {
            $relationshipLoadArray[] = "'{$config['method_name']}'";
        }
        foreach ($relationships['belongsToMany'] as $config) {
            $relationshipLoadArray[] = "'{$config['method_name']}'";
        }

        $relationshipLoadIndex = !empty($relationshipLoadArray) ? "::with([" . implode(', ', $relationshipLoadArray) . "])->get()" : '::all()';
        $relationshipLoadEdit = !empty($relationshipLoadArray) ? "        \${$modelVariable}->load([" . implode(', ', $relationshipLoadArray) . "]);" : '';

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
            \$collection = {$modelName}{$relationshipLoadIndex};
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
                ->with('success', '{$modelName} criado com sucesso.');
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
                ->with('success', '{$modelName} atualizado com sucesso.');
        }

        public function destroy({$modelName} \${$modelVariable})
        {
            \${$modelVariable}->delete();

            return redirect()->route('{$tableName}.index')
                ->with('success', '{$modelName} excluido com sucesso.');
        }
    }
    EOT;

        return $template;
    }

    protected function getRelationshipVariables($relationships)
    {
        $vars = [];

        // BelongsTo relationships
        if (!empty($relationships['belongsTo'])) {
            foreach ($relationships['belongsTo'] as $relatedModel => $config) {
                $varName = Str::plural($config['method_name']);
                $vars[] = "\${$varName} = {$relatedModel}::all();";
            }
        }

        // BelongsToMany relationships
        if (!empty($relationships['belongsToMany'])) {
            foreach ($relationships['belongsToMany'] as $relatedModel => $config) {
                $varName = Str::plural(Str::camel($relatedModel));
                $vars[] = "\${$varName} = {$relatedModel}::all();";
            }
        }

        return implode("\n        ", $vars);
    }

    protected function getCompactVariables($relationships, $includeQuotes = true)
    {
        $vars = [];

        // BelongsTo relationships
        if (!empty($relationships['belongsTo'])) {
            foreach ($relationships['belongsTo'] as $config) {
                $varName = Str::plural($config['method_name']);
                $vars[] = $includeQuotes ? "'{$varName}'" : "'{$varName}'";
            }
        }

        // BelongsToMany relationships
        if (!empty($relationships['belongsToMany'])) {
            foreach ($relationships['belongsToMany'] as $config) {
                $varName = Str::plural($config['method_name']);
                $vars[] = $includeQuotes ? "'{$varName}'" : "'{$varName}'";
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
        $indentation = '            ';

        // BelongsTo relationships - Select fields
        foreach ($relationships['belongsTo'] as $relatedModel => $config) {
            $label = Str::ucfirst(str_replace('_', ' ', $config['method_name']));
            $varName = Str::plural($config['method_name']);
            $formFields .= $indentation . "@livewire('select', [\n";
            $formFields .= $indentation . "    'name' => '{$config['foreign_key']}',\n";
            $formFields .= $indentation . "    'label' => '{$label}',\n";
            $formFields .= $indentation . "    'id' => '{$config['foreign_key']}',\n";
            $formFields .= $indentation . "    'options' => \${$varName}->pluck('name', 'id')->toArray(),\n";
            $formFields .= $indentation . "    'placeholder' => 'Selecione uma {$label}'\n";
            $formFields .= $indentation . "])\n\n";
        }

        // BelongsToMany relationships - Multi-select fields
        foreach ($relationships['belongsToMany'] as $relatedModel => $config) {
            $label = Str::ucfirst(str_replace('_', ' ', $config['method_name']));
            $varName = Str::plural($config['method_name']);
            $formFields .= $indentation . "@livewire('select', [\n";
            $formFields .= $indentation . "    'name' => '{$config['method_name']}',\n";
            $formFields .= $indentation . "    'label' => '{$label}',\n";
            $formFields .= $indentation . "    'id' => '{$config['method_name']}',\n";
            $formFields .= $indentation . "    'options' => \${$varName}->pluck('name', 'id')->toArray(),\n";
            $formFields .= $indentation . "    'multiple' => true,\n";
            $formFields .= $indentation . "    'placeholder' => 'Selecione as {$label}'\n";
            $formFields .= $indentation . "])\n\n";
        }

        // Regular columns
        foreach ($columns as $name => $type) {
            $label = Str::ucfirst(str_replace('_', ' ', $name));
            if ($type === 'checkbox') {
                $formFields .= $indentation . "<livewire:checkbox name=\"{$name}\" label=\"{$label}\" id=\"{$name}\" :checked=\"old('{$name}', true)\" />\n";
            } else {
                $formFields .= $indentation . "<livewire:input type=\"{$type}\" name=\"{$name}\" label=\"{$label}\" id=\"{$name}\" value=\"{{ old('{$name}') }}\" />\n";
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
        $content = str_replace('FORMFIELDS', rtrim($formFields), $content);

        File::put("{$path}/create.blade.php", $content);
    }

    protected function generateEditView($path, $tableName, $columns, $relationships)
    {
        $title = Str::ucfirst(Str::singular($tableName));
        $modelVariable = Str::singular($tableName);
        $formFields = '';
        $indentation = '            ';

        // BelongsTo relationships
        foreach ($relationships['belongsTo'] as $relatedModel => $config) {
            $label = Str::ucfirst(str_replace('_', ' ', $config['method_name']));
            $varName = Str::plural($config['method_name']);
            $formFields .= $indentation . "@livewire('select', [\n";
            $formFields .= $indentation . "    'name' => '{$config['foreign_key']}',\n";
            $formFields .= $indentation . "    'label' => '{$label}',\n";
            $formFields .= $indentation . "    'id' => '{$config['foreign_key']}',\n";
            $formFields .= $indentation . "    'options' => \${$varName}->pluck('name', 'id')->toArray(),\n";
            $formFields .= $indentation . "    'placeholder' => 'Selecione uma {$label}',\n";
            $formFields .= $indentation . "    'selected' => old('{$config['foreign_key']}', \${$modelVariable}->{$config['foreign_key']})\n";
            $formFields .= $indentation . "])\n\n";
        }

        // BelongsToMany relationships
        foreach ($relationships['belongsToMany'] as $relatedModel => $config) {
            $label = Str::ucfirst(str_replace('_', ' ', $config['method_name']));
            $varName = Str::plural($config['method_name']);
            $formFields .= $indentation . "@livewire('select', [\n";
            $formFields .= $indentation . "    'name' => '{$config['method_name']}',\n";
            $formFields .= $indentation . "    'label' => '{$label}',\n";
            $formFields .= $indentation . "    'id' => '{$config['method_name']}',\n";
            $formFields .= $indentation . "    'options' => \${$varName}->pluck('name', 'id')->toArray(),\n";
            $formFields .= $indentation . "    'multiple' => true,\n";
            $formFields .= $indentation . "    'placeholder' => 'Selecione as {$label}',\n";
            $formFields .= $indentation . "    'selected' => old('{$config['method_name']}', \${$modelVariable}->{$config['method_name']}->pluck('id')->toArray())\n";
            $formFields .= $indentation . "])\n\n";
        }

        // Regular columns
        foreach ($columns as $name => $type) {
            $label = Str::ucfirst(str_replace('_', ' ', $name));
            if ($type === 'checkbox') {
                $formFields .= $indentation . "<livewire:checkbox name=\"{$name}\" label=\"{$label}\" id=\"{$name}\" :checked=\"old('{$name}', \${$modelVariable}->{$name})\" />\n";
            } else {
                $formFields .= $indentation . "<livewire:input type=\"{$type}\" name=\"{$name}\" label=\"{$label}\" id=\"{$name}\" value=\"{{ old('{$name}', \${$modelVariable}->{$name}) }}\" />\n";
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
        $content = str_replace('FORMFIELDS', rtrim($formFields), $content);

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
