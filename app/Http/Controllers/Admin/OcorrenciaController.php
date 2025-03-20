<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ocorrencia;
use App\Models\Condominio;
use App\Models\Morador;
use App\Models\Atividade;
use Illuminate\Http\Request;

class OcorrenciaController extends Controller
{
    public function index()
    {
        $ocorrencias = Ocorrencia::with(['condominio', 'morador'])->latest()->paginate(10);
        return view('admin.ocorrencias.index', compact('ocorrencias'));
    }

    public function create()
    {
        $condominios = Condominio::all();
        $moradores = Morador::all();
        return view('admin.ocorrencias.create', compact('condominios', 'moradores'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'condominio_id' => 'required|exists:condominios,id',
            'morador_id' => 'nullable|exists:moradores,id',
            'titulo' => 'required|string|max:255',
            'descricao' => 'required|string',
            'tipo' => 'required|string|in:reclamação,manutenção,segurança,barulho,outros',
            'prioridade' => 'required|string|in:baixa,média,alta,urgente',
            'status' => 'required|string|in:aberta,em andamento,resolvida,cancelada',
            'data_ocorrencia' => 'required|date',
            'fotos.*' => 'nullable|image|max:2048',
        ]);

        $ocorrencia = Ocorrencia::create($validated);

        if ($request->hasFile('fotos')) {
            $fotos = [];
            foreach ($request->file('fotos') as $foto) {
                $fotos[] = $foto->store('ocorrencias', 'public');
            }
            $ocorrencia->update(['fotos' => $fotos]);
        }

        // Registrar atividade
        Atividade::create([
            'usuario_id' => auth()->id(),
            'descricao' => 'Registrou a ocorrência: ' . $ocorrencia->titulo,
        ]);

        return redirect()->route('admin.ocorrencias.index')
            ->with('success', 'Ocorrência registrada com sucesso!');
    }

    public function show(Ocorrencia $ocorrencia)
    {
        $ocorrencia->load(['condominio', 'morador']);
        return view('admin.ocorrencias.show', compact('ocorrencia'));
    }

    public function edit(Ocorrencia $ocorrencia)
    {
        $condominios = Condominio::all();
        $moradores = Morador::all();
        return view('admin.ocorrencias.edit', compact('ocorrencia', 'condominios', 'moradores'));
    }

    public function update(Request $request, Ocorrencia $ocorrencia)
    {
        $validated = $request->validate([
            'condominio_id' => 'required|exists:condominios,id',
            'morador_id' => 'nullable|exists:moradores,id',
            'titulo' => 'required|string|max:255',
            'descricao' => 'required|string',
            'tipo' => 'required|string|in:reclamação,manutenção,segurança,barulho,outros',
            'prioridade' => 'required|string|in:baixa,média,alta,urgente',
            'status' => 'required|string|in:aberta,em andamento,resolvida,cancelada',
            'data_ocorrencia' => 'required|date',
            'resolucao' => 'nullable|string',
            'data_resolucao' => 'nullable|date',
            'fotos.*' => 'nullable|image|max:2048',
        ]);

        $ocorrencia->update($validated);

        if ($request->hasFile('fotos')) {
            $fotos = $ocorrencia->fotos ?? [];
            foreach ($request->file('fotos') as $foto) {
                $fotos[] = $foto->store('ocorrencias', 'public');
            }
            $ocorrencia->update(['fotos' => $fotos]);
        }

        // Registrar atividade
        Atividade::create([
            'usuario_id' => auth()->id(),
            'descricao' => 'Atualizou a ocorrência: ' . $ocorrencia->titulo,
        ]);

        return redirect()->route('admin.ocorrencias.index')
            ->with('success', 'Ocorrência atualizada com sucesso!');
    }

    public function destroy(Ocorrencia $ocorrencia)
    {
        $titulo = $ocorrencia->titulo;
        $ocorrencia->delete();

        // Registrar atividade
        Atividade::create([
            'usuario_id' => auth()->id(),
            'descricao' => 'Removeu a ocorrência: ' . $titulo,
        ]);

        return redirect()->route('admin.ocorrencias.index')
            ->with('success', 'Ocorrência removida com sucesso!');
    }
} 