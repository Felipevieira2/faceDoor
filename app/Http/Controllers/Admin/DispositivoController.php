<?php

namespace App\Http\Controllers\Admin;

use App\Models\Torre;
use App\Models\Atividade;
use App\Models\Condominio;
use App\Models\Dispositivo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DispositivoController extends Controller
{
    public function index()
    {
        $dispositivos = Dispositivo::with('condominio')->latest()->paginate(10);
        return view('admin.dispositivos.index', compact('dispositivos'));
    }

    public function create()
    {
        $condominios = Condominio::all();
        $torres = Torre::all();
        return view('admin.dispositivos.create', compact('condominios', 'torres'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'tipo' => 'required|string',
            'identificador_unico' => 'required|string|max:255|unique:dispositivos',
            'fabricante' => 'required|string|in:intelbras,controlid',
            'username' => 'required_if:fabricante,intelbras|nullable|string|max:255',
            'password' => 'required_if:fabricante,intelbras|nullable|string|min:6',
            'ip' => 'required_if:fabricante,intelbras|nullable|string|max:255',
            'condominio_id' => 'required|exists:condominios,id',
            'torre_id' => 'nullable|exists:torres,id',
            'localizacao' => 'required|string|max:255',
            'ativo' => 'required|boolean',
        ]);

        // Limpar campos não utilizados para fabricante ControlID
        if ($validated['fabricante'] === 'controlid') {
            $validated['username'] = null;
            $validated['password'] = null;
            $validated['ip'] = null;
        }

        $dispositivo = Dispositivo::create($validated);

        return redirect()->route('admin.dispositivos.index')
            ->with('success', 'Dispositivo criado com sucesso!');
    }

    public function show(Dispositivo $dispositivo)
    {
        return view('admin.dispositivos.show', compact('dispositivo'));
    }

    public function edit(Dispositivo $dispositivo)
    {
        $condominios = Condominio::all();
        $torres = Torre::all();
        return view('admin.dispositivos.edit', compact('dispositivo', 'condominios', 'torres'));
    }

    public function update(Request $request, Dispositivo $dispositivo)
    {
        dd($request->all());
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'tipo' => 'required|string',
            'identificador_unico' => 'required|string|max:255|unique:dispositivos,identificador_unico,' . $dispositivo->id,
            'fabricante' => 'required|string|in:intelbras,controlid',
            'username' => 'required_if:fabricante,intelbras|nullable|string|max:255',
            'password' => 'nullable|string|min:6',
            'ip' => 'required_if:fabricante,intelbras|nullable|string|max:255',
            'condominio_id' => 'required|exists:condominios,id',
            'torre_id' => 'nullable|exists:torres,id',
            'localizacao' => 'required|string|max:255',
            'ativo' => 'required|boolean',
        ]);

        // Se a senha não for fornecida, remova-a dos dados validados
        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        // Limpar campos não utilizados para fabricante ControlID
        if ($validated['fabricante'] === 'controlid') {
            $validated['username'] = null;
            $validated['password'] = null;
            $validated['ip'] = null;
        }

        $dispositivo->update($validated);

        return redirect()->route('admin.dispositivos.index')
            ->with('success', 'Dispositivo atualizado com sucesso!');
    }

    public function destroy(Dispositivo $dispositivo)
    {
        $dispositivo->delete();

        return redirect()->route('admin.dispositivos.index')
            ->with('success', 'Dispositivo excluído com sucesso!');
    }
}