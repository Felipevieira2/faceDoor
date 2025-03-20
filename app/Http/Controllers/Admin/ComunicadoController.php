<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comunicado;
use App\Models\Condominio;
use App\Models\Atividade;
use Illuminate\Http\Request;

class ComunicadoController extends Controller
{
    public function index()
    {
        $comunicados = Comunicado::with('condominio')->latest()->paginate(10);
        return view('admin.comunicados.index', compact('comunicados'));
    }

    public function create()
    {
        $condominios = Condominio::all();
        return view('admin.comunicados.create', compact('condominios'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'condominio_id' => 'required|exists:condominios,id',
            'titulo' => 'required|string|max:255',
            'mensagem' => 'required|string',
            'data_inicio' => 'required|date',
            'data_fim' => 'required|date|after_or_equal:data_inicio',
            'tipo' => 'required|string|in:informativo,aviso,alerta,emergência',
            'publico_alvo' => 'nullable|string|in:todos,proprietários,inquilinos,colaboradores',
            'anexos.*' => 'nullable|file|max:5120',
        ]);

        $comunicado = Comunicado::create([
            ...$validated,
            'autor_id' => auth()->id(),
        ]);

        if ($request->hasFile('anexos')) {
            $anexos = [];
            foreach ($request->file('anexos') as $anexo) {
                $anexos[] = $anexo->store('comunicados', 'public');
            }
            $comunicado->update(['anexos' => $anexos]);
        }

        // Registrar atividade
        Atividade::create([
            'usuario_id' => auth()->id(),
            'descricao' => 'Publicou o comunicado: ' . $comunicado->titulo,
        ]);

        return redirect()->route('admin.comunicados.index')
            ->with('success', 'Comunicado publicado com sucesso!');
    }

    public function show(Comunicado $comunicado)
    {
        $comunicado->load(['condominio', 'autor']);
        return view('admin.comunicados.show', compact('comunicado'));
    }

    public function edit(Comunicado $comunicado)
    {
        $condominios = Condominio::all();
        return view('admin.comunicados.edit', compact('comunicado', 'condominios'));
    }

    public function update(Request $request, Comunicado $comunicado)
    {
        $validated = $request->validate([
            'condominio_id' => 'required|exists:condominios,id',
            'titulo' => 'required|string|max:255',
            'mensagem' => 'required|string',
            'data_inicio' => 'required|date',
            'data_fim' => 'required|date|after_or_equal:data_inicio',
            'tipo' => 'required|string|in:informativo,aviso,alerta,emergência',
            'publico_alvo' => 'nullable|string|in:todos,proprietários,inquilinos,colaboradores',
            'anexos.*' => 'nullable|file|max:5120',
        ]);

        $comunicado->update($validated);

        if ($request->hasFile('anexos')) {
            $anexos = $comunicado->anexos ?? [];
            foreach ($request->file('anexos') as $anexo) {
                $anexos[] = $anexo->store('comunicados', 'public');
            }
            $comunicado->update(['anexos' => $anexos]);
        }

        // Registrar atividade
        Atividade::create([
            'usuario_id' => auth()->id(),
            'descricao' => 'Atualizou o comunicado: ' . $comunicado->titulo,
        ]);

        return redirect()->route('admin.comunicados.index')
            ->with('success', 'Comunicado atualizado com sucesso!');
    }

    public function destroy(Comunicado $comunicado)
    {
        $titulo = $comunicado->titulo;
        $comunicado->delete();

        // Registrar atividade
        Atividade::create([
            'usuario_id' => auth()->id(),
            'descricao' => 'Removeu o comunicado: ' . $titulo,
        ]);

        return redirect()->route('admin.comunicados.index')
            ->with('success', 'Comunicado removido com sucesso!');
    }
} 