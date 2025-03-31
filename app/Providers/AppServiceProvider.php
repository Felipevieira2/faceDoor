<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use App\Repositories\Eloquent\ControlIdJobRepository;
use App\Repositories\Interfaces\ControlIdJobRepositoryInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ControlIdJobRepositoryInterface::class, ControlIdJobRepository::class);

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
