<?php

namespace JustIversen\Performant;

use JustIversen\Performant\AnalyzeCode;
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
