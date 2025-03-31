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

class UploadImageUserHandler implements ControlIdJobsHandlerStrategyInterface
{
    /**
     * Manipula jobs relacionados à adição de imagem do morador/visitante
     *
     * @param ControlIdJob $job
     * @param Request $request
     * @return JsonResponse
     */
    public function handle(ControlIdJob $job, Request $request): JsonResponse
    {
        Log::info("RESULT - handle upload_image_user"); 
        Log::info($request->all());

        try {            
            // Processar a resposta do ControlID
         
            // // Atualizar o status da autorização, se necessário
            $autorizacao = $job->user_able->autorizacoeDispositivoByDispositivo($job->dispositivo);
                
            // Log::info("autorizacao: {$autorizacao} upload_image_user");
                

            //'response' => '{"scores":{"bounds_width":118,"horizontal_center_offset":-160,"vertical_center_offset":32,"center_pose_quality":915,"sharpness_quality":750},"success":false,"errors":[{"code":3,"message":"Face exists","info":{"match_user_id":1003088,"match_confidence":1048}}]}',
            $response = json_decode($request->response, true);


          
            if ($response['success']) {
                $autorizacao->status = 'autorizado'; 
                $autorizacao->messagem_erro = '';
                              
            }else if(!empty($response['errors'])){
                $autorizacao->status = 'erro';  
                $autorizacao->messagem_erro = $response['errors'][0]['message'];    
                
                if ($response['errors'][0]['code'] == 3) {
                    $autorizacao->match_user_id_externo = $response['errors'][0]['info']['match_user_id'];
          
                }
            }
         
            $autorizacao->save();

            // Marcar o job como concluído
            $job->status = 1;
            $job->log = $request->response;
            $job->save();


            return response()->json([], 200);

           
        } catch (\Exception $e) {
            Log::error("Erro ao processar jobId: {$job->id} message: {$e->getMessage()}  upload_image_user");
            
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