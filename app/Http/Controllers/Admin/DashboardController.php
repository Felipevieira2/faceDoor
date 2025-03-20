<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Torre;
use App\Models\Morador;
use App\Models\Visitante;
use App\Models\Condominio;
use App\Models\Apartamento;
use App\Models\Dispositivo;
use App\Models\Ocorrencia;
use App\Models\Atividade;
use App\Models\Acesso;
use App\Models\Comunicado;
use App\Models\Evento;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        

        $data = [
            'condominios' => Condominio::count(),
            'torres' => Torre::count(),
            'dispositivos' => Dispositivo::count(),
            'visitantes' => Visitante::count(),
            'moradores' => Morador::count(),
            'usuarios' => User::count(),
        
        ];

        

        return view('admin.dashboard.index', $data);
    }
}
