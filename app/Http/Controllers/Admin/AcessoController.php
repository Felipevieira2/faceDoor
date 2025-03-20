<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Acesso;
use Illuminate\Http\Request;

class AcessoController extends Controller
{
    public function index(Request $request)
    {
        $query = Acesso::with(['morador', 'dispositivo.condominio']);
        
        // Filtros
        if ($request->filled('data_inicio') && $request->filled('data_fim')) {
            $query->whereBetween('data_hora', [
                $request->data_inicio . ' 00:00:00', 
                $request->data_fim . ' 23:59:59'
            ]);
        }
        
        if ($request->filled('condominio_id')) {
            $query->whereHas('dispositivo', function($q) use ($request) {
                $q->where('condominio_id', $request->condominio_id);
            });
        }
        
        if ($request->filled('dispositivo_id')) {
            $query->where('dispositivo_id', $request->dispositivo_id);
        }
        
        if ($request->filled('morador_id')) {
            $query->where('morador_id', $request->morador_id);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $acessos = $query->latest()->paginate(15);
        
        return view('admin.acessos.index', compact('acessos'));
    }
} 