<?php

namespace App\Http\Controllers\Admin;

use App\Models\Torre;
use App\Models\Condominio;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class TorreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $torres = Torre::with('condominio')->paginate(10);
        return view('admin.torres.index', compact('torres'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $condominios = Condominio::all();
        return view('admin.torres.create', compact('condominios'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'condominio_id' => 'required|exists:condominios,id',
            'numero_andares' => 'required|integer|min:1',
            'descricao' => 'nullable|string',
        ]);

        Torre::create($validated);

        Session::flash('success', 'Torre criada com sucesso!');
        return redirect()->route('admin.torres.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Torre $torre)
    {
        $torre->load(['condominio', 'apartamentos']);
        return view('admin.torres.show', compact('torre'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Torre $torre)
    {
        $condominios = Condominio::all();
        return view('admin.torres.edit', compact('torre', 'condominios'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Torre $torre)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'condominio_id' => 'required|exists:condominios,id',
            'numero_andares' => 'required|integer|min:1',
            'descricao' => 'nullable|string',
        ]);

        $torre->update($validated);

        Session::flash('success', 'Torre atualizada com sucesso!');
        return redirect()->route('admin.torres.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Torre $torre)
    {
        try {
            $torre->delete();
            Session::flash('success', 'Torre excluída com sucesso!');
        } catch (\Exception $e) {
            Session::flash('error', 'Não foi possível excluir a torre. Verifique se existem apartamentos associados.');
        }

        return redirect()->route('admin.torres.index');
    }
} 