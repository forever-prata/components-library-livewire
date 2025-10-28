<?php

namespace GovbrComponentsLivewire;

use Illuminate\Support\ServiceProvider;

class GovbrComponentsLivewireServiceProvider extends ServiceProvider
{
    public function boot()
    {
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
                \GovbrComponentsLivewire\Commands\MakeScaffoldCommand::class,
            ]);
        }
    }

    public function register()
    {
        //
    }
}
