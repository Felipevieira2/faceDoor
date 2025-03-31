<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Strategies\ControlID\ControlIdStrategy;
use App\Strategies\Intelbras\IntelbrasStrategy;

class AutorizacaoDispostivosProvider extends ServiceProvider
{
    /**
     * Register services.
     * O método singleton() garante que apenas uma instância da classe ControlIdJobService seja criada durante todo o ciclo de vida da aplicação. Isso significa que:
     * bind(): Criaria uma nova instância cada vez que o serviço fosse solicitado
     * instance(): Registraria uma instância já existente
     */
    public function register(): void
    {
        // ... existing code ...

        $this->app->bind('autorizacao.controlid', ControlIdStrategy::class);
        $this->app->bind('autorizacao.intelbras', IntelbrasStrategy::class);

    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
