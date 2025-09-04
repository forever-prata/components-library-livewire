<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeScaffoldCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:scaffold {migration : The name of the migration file (e.g., 2025_09_03_142650_create_produtos_table)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scaffold a full CRUD resource from a migration file, using Livewire components for views.';

    /**
     * Execute the console command.
     */
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
        $this->generateController($modelName);
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
        //  regex $table->string('column_name') type and name.
        if (preg_match_all('/\$table->(\w+)\(\'([a-zA-Z0-9_]+)\'/i', $content, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                // $match[1] type , $match[2] name
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
                return 'number';
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

        $template = <<<EOT
        <?php

        namespace App\Models;

        use Illuminate\Database\Eloquent\Factories\HasFactory;
        use Illuminate\Database\Eloquent\Model;

        class {$modelName} extends Model
        {
            use HasFactory;

            protected \$fillable = [
                '{$fillable_string}'
            ];
        }
        EOT;

        $modelPath = app_path("Models/{$modelName}.php");
        File::put($modelPath, $template);
        $this->line("Model '{$modelName}' created successfully with HasFactory and fillable property.");
    }

    protected function generateController($modelName)
    {
        Artisan::call('make:controller', [
            'name' => "{$modelName}Controller",
            '--resource' => true
        ], $this->getOutput());
        $this->line("Controller '{$modelName}Controller' created.");
    }

    protected function addRoute($tableName, $modelName)
    {
        $route = "Route::resource('{$tableName}', App\\Http\\Controllers\\{$modelName}Controller::class);" . PHP_EOL;
        File::append(base_path('routes/web.php'), $route);
        $this->line("Route for '{$tableName}' added to web.php.");
    }

    protected function generateViews($tableName, $columns)
    {
        $viewPath = resource_path("views/{$tableName}");
        File::makeDirectory($viewPath, 0755, true, true);

        $this->generateIndexView($viewPath, $tableName, $columns);
        //$this->generateCreateView($viewPath, $tableName, $columns);
        //$this->generateEditView($viewPath, $tableName, $columns);
        //$this->generateShowView($viewPath, $tableName, $columns);

        $this->line("Views created in resources/views/{$tableName}/");
    }

    protected function generateIndexView($path, $tableName, $columns)
    {
        $title = Str::ucfirst($tableName);

        $content = <<<EOT
        @extends('layouts.scaffold')

        @section('content')
            <div class="container mt-5">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1>{$title}</h1>
                    <livewire:botao tipo="primary" label="Create New" href="{{ route('{$tableName}.create') }}" />
                </div>

                <livewire:table
                    :headers="\$headers"
                    :rows="\$rows"
                    :busca="true"
                    :selecionavel="true"
                    titulo="{$title}"
                />
            </div>
        @endsection
        EOT;

        File::put("{$path}/index.blade.php", $content);
    }

    protected function generateCreateView($path, $tableName, $columns)
    {

    }

    protected function generateEditView($path, $tableName, $columns)
    {

    }

    protected function generateShowView($path, $tableName, $columns)
    {

    }
}
