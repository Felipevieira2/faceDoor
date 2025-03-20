<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::componentNamespace('App\\View\\Components', 'x');
        Blade::componentNamespace('App\\View\\Components\\Layout\\Admin', 'x');
        
        Blade::component('forms.input', \App\View\Components\Forms\Input::class);
        Blade::component('forms.button', \App\View\Components\Forms\Button::class);

        // Registre seus componentes aqui se necessário
        // Mas eles normalmente são detectados automaticamente da pasta /resources/views/components/
    }
}
