<?php

namespace App\Http\Controllers;

use App\Models\Morador;
use App\Models\Visitante;
use App\Models\Autorizacao;
use App\Models\Dispositivo;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\AutorizacaoDispositivo;
use App\Factories\ControleAcessoFactory;
use App\Factories\AutorizacaoRequestFactoryCreator;
use App\Factories\AutorizacaoStrategyFactory;

class AutorizacaoController extends Controller
{
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
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function autorizar(Request $request): JsonResponse
    {
        $request->validate([
            'dispositivo_id' => 'required|string',
            'visitante_id' => 'nullable|numeric',
            'morador_id' => 'nullable|numeric',
            'data_inicio_visitante' => 'nullable|date',
            'data_fim_visitante' => 'nullable|date',
        ]);
       
        // Encontra o dispositivo e seu tipo
        $dispositivo = Dispositivo::findOrFail($request->dispositivo_id);
        
        // Cria a estratégia apropriada
        $strategy = AutorizacaoStrategyFactory::criarStrategy($dispositivo->fabricante);
        
        if ($request->visitante_id) {
            $model_selected = Visitante::findOrFail($request->visitante_id);
        } elseif ($request->morador_id) {
            $model_selected = Morador::findOrFail($request->morador_id);
        } else {
            return response()->json([
                'message' => 'Nenhum visitante ou morador informado'
            ], 400);
        }

        // Executar a autorização
        $response = $strategy->validar($dispositivo, $model_selected);

        if($response->getStatusCode() != 200){
            return $response;
        }
  
        // Executar a autorização
        return $strategy->createJobByEndpoint($dispositivo, $model_selected, 'create_user_morador');

       
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

    // Você pode adicionar endpoints específicos para cada tipo de dispositivo
    public function autorizarBiometria(Request $request)
    {
        $strategy = AutorizacaoStrategyFactory::criarStrategy('biometria');
        $strategy->validarRequest($request);
        return $strategy->executar($request);
    }

    public function autorizarControleAcesso(Request $request)
    {
        $strategy = AutorizacaoStrategyFactory::criarStrategy('controle_acesso');
        $strategy->validarRequest($request);
        return $strategy->executar($request);
    }
}
