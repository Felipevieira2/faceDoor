<?php

namespace App\Strategies\ControlID;

use App\Models\Dispositivo;
use App\Models\ControlIdJob;
use App\Models\Morador;
use App\Models\Visitante;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Facades\ControlIdJobFacada;
use App\Models\AutorizacaoDispositivo;
use App\Strategies\AutorizacaoStrategy;
use Illuminate\Support\Facades\Log;

class ControlIdStrategy implements AutorizacaoStrategy
{
    public function validarRequest(Request $request): void
    {
        $request->validate([
            'dispositivo_id' => 'required|string',
            'visitante_id' => 'nullable|numeric',
            'morador_id' => 'nullable|numeric',
        ]);
    }
    
    public function validar(Dispositivo $dispositivo, Visitante | Morador $model_selected): JsonResponse
    {
        
        if(!$dispositivo->ativo){
            return response()->json([
                'message' => 'Dispositivo não está ativo'
            ], 404);
        }
        
        if($dispositivo->fabricante != 'controlid'){
            return response()->json([
                'message' => 'Dispositivo não é do fabricante ControlID'
            ], 404);
        }
            
        
        return response()->json([
            'message' => 'Dispositivo validado com sucesso'
        ], 200);
    }

    /*
        Autorização de controle de acesso
        
    */
    public function createJobByEndpoint(Dispositivo $dispositivo, Visitante | Morador $model_selected, $endpoint): JsonResponse
    {
        //validar antes se o dispositivo existe

        try {            
            $data = [
                'endpoint' => $endpoint,
                'identificador_dispositivo' => $dispositivo->identificador_unico,              
            ];
          
            $response = ControlIdJobFacada::createJob([...$data ], $model_selected);

            return $response;

        } catch (\Exception $e) {

            Log::error('Erro ao processar autorização de controle de acesso: ' . $e->getMessage(). ' - Linha: ' . $e->getLine());

            return response()->json([
                'message' => 'Erro ao processar autorização de controle de acesso'
            ], 500);
        }
    }
    
    public function processarJob(ControlIdJob $job)
    {
        return ControlIdJobFacada::process($job->id);
    }    

    
}
