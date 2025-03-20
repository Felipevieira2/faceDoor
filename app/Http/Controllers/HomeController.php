<?php

namespace App\Http\Controllers;

use App\Models\Condominio;
use App\Models\Dispositivo;
use App\Models\Visitante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Mostra o dashboard apropriado com base no papel do usuário
     */
    public function index()
    {
        
        return view('dashboard.administrador', [
            'condominios_count' => \App\Models\Condominio::count(),
            'dispositivos_count' => \App\Models\Dispositivo::count(),
            'visitantes_count' => \App\Models\Visitante::count()
        ]);
    

        // Fallback para qualquer outro tipo de usuário
        return redirect()->route('login');
    }
}
