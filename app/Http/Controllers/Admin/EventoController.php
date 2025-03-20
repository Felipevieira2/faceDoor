<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Evento;
use App\Models\Condominio;
use App\Models\Atividade;
use Illuminate\Http\Request;

class EventoController extends Controller
{
    public function index()
    {
        $eventos = Evento::with('condominio')->orderBy('data')->orderBy('hora')->paginate(10);
        return view('admin.eventos.index', compact('eventos'));
    }

    public function create()
    {
        $condominios = Condominio::all();
        return view('admin.eventos.create', compact('condominios'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'condominio_id' => 'required|exists:condominios,id',
            'titulo' => 'required|string|max:255',
            'descricao' => 'required|string',
            'data' => 'required|date',
            'hora' => 'required',
            'duracao' => 'nullable|integer',
            'local' => 'required|string|max:255',
            'tipo' => 'required|string|in:reunião,manutenção,evento,assembleia,outros',
            'prioridade' => 'required|string|in:baixa,média,alta',
            'status' => 'required|string|in:agendado,confirmado,cancelado,concluído',
        ]);

        $evento = Evento::create([
            ...$validated,
            'organizador_id' => auth()->id(),
        ]);

        // Registrar atividade
        Atividade::create([
            'usuario_id' => auth()->id(),
            'descricao' => 'Agendou o evento: ' . $evento->titulo,
        ]);

        return redirect()->route('admin.eventos.index')
            ->with('success', 'Evento agendado com sucesso!');
    }

    public function show(Evento $evento)
    {
        $evento->load(['condominio', 'organizador']);
        return view('admin.eventos.show', compact('evento'));
    }

    public function edit(Evento $evento)
    {
        $condominios = Condominio::all();
        return view('admin.eventos.edit', compact('evento', 'condominios'));
    }
} 