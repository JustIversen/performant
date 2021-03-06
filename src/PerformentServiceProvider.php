<?php

namespace JustIversen\Performant;

use Illuminate\Support\ServiceProvider;

class PerformantServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(): void
    {
        if($this->app->runningInConsole()) {
            $this->registerArtisanCommands();
        }
        $this->loadTranslationsFrom(__DIR__.'/../../resources/lang/en','performant');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
    public function registerArtisanCommands():void
    {
        $this->commands([
            AnalyzeCode::class,
        ]);
    }
}
