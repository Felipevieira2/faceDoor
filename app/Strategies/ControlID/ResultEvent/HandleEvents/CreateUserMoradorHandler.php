<?php

namespace App\Strategies\ControlID\ResultEvent\HandleEvents;

use App\Models\Job;
use App\Models\ControlIdJob;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Models\AutorizacaoDispositivo;
use App\Strategies\ControlID\ControlIdStrategy;
use App\Strategies\ControlID\ResultEvent\Interfaces\ControlIdJobsHandlerStrategyInterface;

class CreateUserMoradorHandler implements ControlIdJobsHandlerStrategyInterface
{
    /**
     * Manipula jobs relacionados a controle de acesso
     *
     * @param ControlIdJob $job
     * @param Request $request
     * @return JsonResponse
     */
    public function handle(ControlIdJob $job, Request $request): JsonResponse
    {
        Log::info("RESULT - handle create_user_morador");   
        Log::info($request->all());

        try {            
            $id = json_decode($request->response, true)['ids'][0];
            
            $autorizacao = $job->user_able->autorizacoeDispositivoByDispositivo($job->dispositivo);

            if(!$autorizacao){
                //gravar no autorizacao_dispositivo o status autorizado                
                $autorizacao = new \App\Models\AutorizacaoDispositivo(); // Ajuste o namespace conforme necessário
                $autorizacao->authorizable_type = get_class($job->user_able);
                $autorizacao->authorizable_id = $job->user_able->id;
                $autorizacao->identificador_dispositivo = $job->dispositivo->identificador_unico;
                $autorizacao->status = 'processando';
                $autorizacao->user_id_externo = $id;
                $autorizacao->save();
            }else{

                if($autorizacao->status == 'autorizado'){
                    \Log::info("autorizacao: {$autorizacao->id} create_user_morador");
                    $job->status = 1;
                    $job->response = "Já existe uma autorização para esse dispositivo";
                    $job->save();
                    
                    return response()->json([], 200);
                }

                $autorizacao->user_id_externo = $id;
                $autorizacao->status = 'processando';
                $autorizacao->save();
            }
            // Log::info("autorizacao: create_user_morador");
            // Log::info('deu certo por aqui ');

            //processa evento de result success no job atual
            $job->status = 1;
            $job->response = $request->response;
            $job->save();

            //cria um novo job para adicionar o grupo ao morador      
            $strategy = new ControlIdStrategy();
            $strategy->createJobByEndpoint($job->dispositivo, $job->user_able, 'add_group_user');

            return response()->json([], 200);
        } catch (\Exception $e) {
            Log::error("Erro ao processar jobId: {$job->id} message: {$e->getMessage()} create_user_morador");
            
            $job->status = 3;
            $job->log = $e->getMessage();
            $job->save();
                        
            return response()->json([
                'message' => 'Erro ao processar jobId: ' . $job->id . ' message: ' . $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }
} 