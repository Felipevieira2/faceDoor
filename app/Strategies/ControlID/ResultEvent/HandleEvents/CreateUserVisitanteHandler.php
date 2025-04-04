<?php

namespace App\Strategies\ControlID\ResultEvent\HandleEvents;

use Carbon\Carbon;
use App\Models\Job;
use App\Models\ControlIdJob;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Models\AutorizacaoDispositivo;
use App\Strategies\ControlID\ControlIdStrategy;
use App\Strategies\ControlID\ResultEvent\Interfaces\ControlIdJobsHandlerStrategyInterface;

class CreateUserVisitanteHandler implements ControlIdJobsHandlerStrategyInterface
{
    /**
     * Manipula jobs relacionados ao cadastro de visitantes no ControlID
     *
     * @param ControlIdJob $job
     * @param Request $request
     * @return JsonResponse
     */
    public function handle(ControlIdJob $job, Request $request): JsonResponse
    {
        Log::info("RESULT - handle create_user_visitante: {$job->id}");
        Log::info($request->all());
        
        try {
            // Obter o ID externo do usuário criado no ControlID
            $id = json_decode($request->response, true)['ids'][0] ?? null;
            
            if (!$id) {
                throw new \Exception("Não foi possível obter o ID externo do usuário no ControlID");
            }
            
            Log::info("ID externo do visitante no ControlID: {$id}");
            
            // Verifica se já existe uma autorização para este visitante neste dispositivo
            $autorizacao = $job->user_able->autorizacoeDispositivoByDispositivo($job->dispositivo);
            
            if (!$autorizacao) {
                // Cria uma nova autorização para o visitante
                $autorizacao = new AutorizacaoDispositivo();
                $autorizacao->authorizable_type = get_class($job->user_able);
                $autorizacao->authorizable_id = $job->user_able->id;
                $autorizacao->identificador_dispositivo = $job->dispositivo->identificador_unico;
                $autorizacao->status = 'processando';
                $autorizacao->user_id_externo = $id;
                $autorizacao->save();
                
                Log::info("Nova autorização criada para o visitante ID {$job->user_able->id} no dispositivo {$job->dispositivo->identificador_unico}");
            } else {
                // Atualiza a autorização existente
                $autorizacao->status = 'processando';
                $autorizacao->user_id_externo = $id;
                $autorizacao->save();
                
                Log::info("Autorização existente atualizada para o visitante ID {$job->user_able->id}");
            }
            
            // Cria próximo job para adicionar o visitante ao grupo apropriado
            $strategy = new ControlIdStrategy();
            $strategy->createJobByEndpoint($job->dispositivo, $job->user_able, 'add_group_user');
            
            // Marca este job como concluído
            $job->status = 1; // Concluído
            $job->response = $request->response;
            $job->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Visitante cadastrado com sucesso no ControlID',
                'jobId' => $job->id
            ], 200);
            
        } catch (\Exception $e) {
            Log::error("Erro ao processar resultado do cadastro de visitante: {$e->getMessage()}");
            
            $job->status = 3; // Erro
            $job->log = $e->getMessage();
            $job->save();
            
            return response()->json([
                'success' => false,
                'message' => 'Falha ao processar cadastro de visitante: ' . $e->getMessage(),
                'jobId' => $job->id
            ], 500);
        }
    }
} 