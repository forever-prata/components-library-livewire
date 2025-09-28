<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeScaffoldCommand extends Command
{
    protected $signature = 'make:scaffold {migration : The name of the migration file (e.g., 2025_09_03_142650_create_produtos_table)}';
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

        $this->info("Scaffolding resource for table: {$tableName}");
        $this->info("Model: {$modelName}, Controller: {$modelName}Controller, Route: {$resourceName}");

        $this->generateModel($modelName, $columns);
        $this->generateController($modelName, $tableName, $columns);
        $this->addRoute($tableName, $modelName);
        $this->generateViews($tableName, $columns);

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
                if (!in_array($match[2], ['id', 'timestamps', 'remember_token', 'created_at', 'updated_at'])) {
                    $columns[$match[2]] = $this->mapColumnTypeToInputType($match[1]);
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

    protected function generateModel($modelName, $columns)
    {
        $fillable_string = implode("',\n        '", array_keys($columns));

        $template = <<<'EOT'
        <?php

        // gerado automaticamente pela biblioteca

        namespace App\Models;

        use Illuminate\Database\Eloquent\Factories\HasFactory;
        use Illuminate\Database\Eloquent\Model;

        class MODELNAME extends Model
        {
            use HasFactory;

            protected $fillable = [
                'FILLABLES'
            ];
        }
        EOT;

        $template = str_replace('MODELNAME', $modelName, $template);
        $template = str_replace('FILLABLES', $fillable_string, $template);

        $modelPath = app_path("Models/{$modelName}.php");
        File::put($modelPath, $template);
        $this->line("Model '{$modelName}' created successfully with HasFactory and fillable property.");
    }

    protected function generateController($modelName, $tableName, $columns)
    {
        $controllerPath = app_path("Http/Controllers/{$modelName}Controller.php");
        $controllerContent = $this->getControllerContent($modelName, $tableName, $columns);

        File::put($controllerPath, $controllerContent);
        $this->line("Controller '{$modelName}Controller' created with resource methods.");
    }

    protected function getControllerContent($modelName, $tableName, $columns)
    {

    $modelVariable = Str::camel($modelName);
    $modelNamespace = "App\\Models\\{$modelName}";

    $validationRules = [];
    foreach ($columns as $column => $type) {
        if ($type !== 'checkbox') {
            $validationRules[] = "            '{$column}' => 'required',";
        }
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

    $template = <<<'EOT'
<?php

// gerado automaticamente pela biblioteca

namespace App\Http\Controllers;

use MODELNAMESPACE;
use Illuminate\Http\Request;

class MODELNAMEController extends Controller
{
    public function index()
    {
        $collection = MODELNAME::all();
        return view('TABLENAME.index', compact('collection'));
    }

    public function create()
    {
        return view('TABLENAME.create');
    }

    public function store(Request $request)
    {
        $request->validate([
VALIDATIONRULES
        ]);

CHECKBOXHANDLING
        CREATECALL

        return redirect()->route('TABLENAME.index')
            ->with('success', 'MODELNAME created successfully.');
    }

    public function show(MODELNAME $MODELVARIABLE)
    {
        return view('TABLENAME.show', compact('MODELVARIABLE'));
    }

    public function edit(MODELNAME $MODELVARIABLE)
    {
        return view('TABLENAME.edit', compact('MODELVARIABLE'));
    }

    public function update(Request $request, MODELNAME $MODELVARIABLE)
    {
        $request->validate([
VALIDATIONRULES
        ]);

CHECKBOXHANDLING
        UPDATECALL

        return redirect()->route('TABLENAME.index')
            ->with('success', 'MODELNAME updated successfully.');
    }

    public function destroy(MODELNAME $MODELVARIABLE)
    {
        $MODELVARIABLE->delete();

        return redirect()->route('TABLENAME.index')
            ->with('success', 'MODELNAME deleted successfully.');
    }
}
EOT;

    $template = str_replace('MODELNAMESPACE', $modelNamespace, $template);
    $template = str_replace('MODELNAME', $modelName, $template);
    $template = str_replace('MODELVARIABLE', $modelVariable, $template);
    $template = str_replace('TABLENAME', $tableName, $template);
    $template = str_replace('VALIDATIONRULES', $validationRulesString, $template);
    $template = str_replace('CHECKBOXHANDLING', $checkboxHandling, $template);
    $template = str_replace('CREATECALL', $createCall, $template);
    $template = str_replace('UPDATECALL', $updateCall, $template);

    return $template;
}


    protected function addRoute($tableName, $modelName)
    {
        $route = "\nRoute::resource('{$tableName}', App\\Http\\Controllers\\{$modelName}Controller::class); // gerado automaticamente pela biblioteca\n";
        File::append(base_path('routes/web.php'), $route);
        $this->line("Route for '{$tableName}' added to web.php.");
    }

    protected function generateViews($tableName, $columns)
    {
        $viewPath = resource_path("views/{$tableName}");
        File::makeDirectory($viewPath, 0755, true, true);

        $this->generateIndexView($viewPath, $tableName, $columns);
        $this->generateCreateView($viewPath, $tableName, $columns);
        $this->generateEditView($viewPath, $tableName, $columns);
        $this->generateShowView($viewPath, $tableName, $columns);

        $this->line("Views created in resources/views/{$tableName}/");
    }

    protected function generateIndexView($path, $tableName, $columns)
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

    protected function generateCreateView($path, $tableName, $columns)
    {
        $title = Str::ucfirst(Str::singular($tableName));
        $formFields = '';
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

    protected function generateEditView($path, $tableName, $columns)
    {
        $title = Str::ucfirst(Str::singular($tableName));
        $modelVariable = Str::singular($tableName);
        $formFields = '';
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

    protected function generateShowView($path, $tableName, $columns)
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
        $content = str_replace('VIEWFIELDS', $viewFields, $content);

        File::put("{$path}/show.blade.php", $content);
    }
}
