<?php

namespace App\Http\Controllers;

use App\Models\Condominio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CondominioController extends Controller
{
    public function __construct()
    {
      
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
       
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function select()
    {
        $condominios = Condominio::all();
        return view('condominios.select', compact('condominios'));
    }

    public function setTenant(Request $request)
    {
        $request->validate([
            'condominio_id' => 'required|exists:condominios,id'
        ]);

        $user = Auth::user();
        $user->tenant_id = $request->condominio_id;
        $user->save();

        session(['tenant_id' => $request->condominio_id]);

        return redirect()->intended('/dashboard');
    }
}
