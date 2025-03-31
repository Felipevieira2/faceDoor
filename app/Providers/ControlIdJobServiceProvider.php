<?php

namespace App\Providers;

use App\Services\ControlIdJobService;
use Illuminate\Support\ServiceProvider;
use App\Repositories\Interfaces\ControlIdJobRepositoryInterface;

class ControlIdJobServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('controlid.job', function ($app) {
            return new ControlIdJobService($app->make(ControlIdJobRepositoryInterface::class));
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
} 