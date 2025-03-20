<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Condominio;
use Illuminate\Http\Request;

class CondominioController extends Controller
{
    public function index()
    {
        $condominios = Condominio::latest()->paginate(10);
        return view('admin.condominios.index', compact('condominios'));
    }

    public function create()
    {
        return view('admin.condominios.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'endereco' => 'required|string|max:255',
            'cidade' => 'required|string|max:100',
            'estado' => 'required|string|max:2',
            'cep' => 'required|string|max:10',
            'telefone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        $condominio = Condominio::create($validated);

        // Registrar atividade
        Atividade::create([
            'usuario_id' => auth()->id(),
            'descricao' => 'Cadastrou o condomínio: ' . $condominio->nome,
        ]);

        return redirect()->route('admin.condominios.index')
            ->with('success', 'Condomínio cadastrado com sucesso!');
    }

    public function show(Condominio $condominio)
    {
        $condominio->load(['moradores', 'dispositivos']);
        return view('admin.condominios.show', compact('condominio'));
    }

    public function edit(Condominio $condominio)
    {
        return view('admin.condominios.edit', compact('condominio'));
    }

    public function update(Request $request, Condominio $condominio)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'endereco' => 'required|string|max:255',
            'cidade' => 'required|string|max:100',
            'estado' => 'required|string|max:2',
            'cep' => 'required|string|max:10',
            'telefone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        $condominio->update($validated);

        // Registrar atividade
        Atividade::create([
            'usuario_id' => auth()->id(),
            'descricao' => 'Atualizou o condomínio: ' . $condominio->nome,
        ]);

        return redirect()->route('admin.condominios.index')
            ->with('success', 'Condomínio atualizado com sucesso!');
    }

    public function destroy(Condominio $condominio)
    {
        $nome = $condominio->nome;
        $condominio->delete();

        // Registrar atividade
        Atividade::create([
            'usuario_id' => auth()->id(),
            'descricao' => 'Removeu o condomínio: ' . $nome,
        ]);

        return redirect()->route('admin.condominios.index')
            ->with('success', 'Condomínio removido com sucesso!');
    }
} 