<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Condominio;
use Illuminate\Support\Facades\Auth;
use App\Traits\BelongsToTenant;

class TenantMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Se o usuário estiver autenticado
        if (Auth::check()) {
            $user = Auth::user();
            
            // Se o usuário não tiver um tenant_id, redireciona para seleção de condomínio
            if (!$user->tenant_id) {
                return redirect()->route('condominios.select');
            }
            
            // Define o tenant atual na sessão
            $tenant = Condominio::find($user->tenant_id);
            if ($tenant) {
                session(['tenant_id' => $tenant->id]);
            }
        }

        return $next($request);
    }
} 