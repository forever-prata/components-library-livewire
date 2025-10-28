<?php

namespace GovbrComponentsLivewire;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;

class GovbrComponentsLivewireServiceProvider extends ServiceProvider
{
    protected $defer = true;
    public function boot()
    {
        $livewirePath = __DIR__ . '/Livewire';
        $publishable = [];

        foreach (File::allFiles($livewirePath) as $file) {
            // caminho de destino: app/Livewire/<NomeArquivo>.php
            $destination = app_path('Livewire/' . $file->getFilename());
            $publishable[$file->getRealPath()] = $destination;
        }

        $this->publishes($publishable, 'livewire-components');

        // Publicar config
        $this->publishes([
            __DIR__ . '/config/design.php' => config_path('design.php'),
        ], 'config');

        // Publicar views (mantendo estrutura livewire/<ds>)
        $this->publishes([
            __DIR__ . '/resources/views/livewire' => resource_path('views/livewire'),
        ], 'views');

        // Publicar CSS e JS dos temas
        $this->publishes([
            __DIR__ . '/resources/css/themes' => resource_path('css/themes'),
            __DIR__ . '/resources/js/themes' => resource_path('js/themes'),
        ], 'themes');

        $this->loadViewsFrom(__DIR__ . '/resources/views', 'govbr-components-livewire');

        // Registrar comandos
        if ($this->app->runningInConsole()) {
            $this->commands([
                \GovbrComponentsLivewire\Console\Commands\MakeScaffoldCommand::class,
            ]);
        }
    }

    public function register()
    {
        //
    }
}
