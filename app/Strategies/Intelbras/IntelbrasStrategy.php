<?php

namespace App\Strategies\Intelbras;

use App\Models\Dispositivo;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\AutorizacaoDispositivo;
use App\Strategies\AutorizacaoStrategy;

class IntelbrasStrategy implements AutorizacaoStrategy
{
    public function validarRequest(Request $request): void
    {
        $request->validate([
            'dispositivo_id' => 'required|string',
            'visitante_id' => 'nullable|numeric',
            'morador_id' => 'nullable|numeric',
            'data_inicio_visitante' => 'nullable|date',
            'data_fim_visitante' => 'nullable|date',
        ]);
    }

    public function autorizar(Request $request, $model_selected): JsonResponse
    {
        $dispositivo = Dispositivo::findOrFail($request->dispositivo_id);
        
        // Lógica específica para controle de acesso
        $autorizacao = new AutorizacaoDispositivo();
        $autorizacao->dispositivo_id = $request->dispositivo_id;
        $autorizacao->authorizable_id = $request->authorizable_id;
        $autorizacao->authorizable_type = $request->authorizable_type;
        $autorizacao->status = 'processando';
        $autorizacao->autorizado_por = auth()->id();
        $autorizacao->save();

        // Aqui você pode adicionar chamadas para APIs externas específicas
        // do controle de acesso se necessário
        
        return response()->json([
            'message' => 'Autorização de controle de acesso processada',
            'autorizacao' => $autorizacao
        ], 201);
    }
} 