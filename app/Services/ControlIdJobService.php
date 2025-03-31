<?php

namespace App\Services;

use App\Models\ControlIdJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Factories\ControlIdPushHandlerFactory;
use App\Factories\ControlIdResultHandlerFactory;
use App\Repositories\Interfaces\ControlIdJobRepositoryInterface;

class ControlIdJobService
{

    private ControlIdJobRepositoryInterface $jobRepository;

    public function __construct(ControlIdJobRepositoryInterface $jobRepository)
    {
        $this->jobRepository = $jobRepository;
    }

    public function processPush(string $deviceId, string $uuid)
    {

        $job = $this->jobRepository->getPendingJobByDeviceId($deviceId);
       
        if (!$job) {
            Log::info('Nenhum job pendente para esse dispositivo');

            
            return response()->json([], 500);
        }

        if (!($job instanceof ControlIdJob)) {
            Log::info('Tipo de job inválido');
            return response()->json([], 200);
        }

        $job->uuid = $uuid;
        $job->status = 2; 
        $job->tentativas++;
        $job->save();

        // Log::info("Processando job de acesso id: {$job->id}");
        $handler = ControlIdPushHandlerFactory::create($job->endpoint);
       
        $response = $handler->handle($job);

        Log::info('response: ');
        Log::info($response);
        
        if( empty($response)){
            return response()->json([], 500);
        }
        
        return $response;
    }

    public function processResult($request)
    {
        $job = $this->jobRepository->getJobByUuid($request->uuid);

        if (!$job) {
            Log::info('Nenhum job para o uuid: ' . $request->uuid . 'foi encontrado');
            return response()->json([], 500);
        }


        $handler = ControlIdResultHandlerFactory::create($job->endpoint);

        return $handler->handle($job,  $request);
    }
    
    
    /**
     * Cria um novo job para o ControlID
     *
     * @param array $data
     * @param mixed $model_selected
     * @return bool
     */
    public function createJob(array $data, $model_selected = null) : JsonResponse
    {
        $pendingJobs = $this->getPending([
            'endpoint' => $data['endpoint'],
            'identificador_dispositivo' => $data['identificador_dispositivo'],
            'user_able_type' => get_class($model_selected),
            'user_able_id' => $model_selected->id
        ]);
       
        Log::info('Jobs desse tipo pendentes: ' . $pendingJobs->count());
        if ($pendingJobs->count() > 0) {


            return response()->json([
                'message' => 'Já existe um job:  ' . $data['endpoint'] . ' pendente para esse idModel: ' . $model_selected->id . ' classModel: ' . get_class($model_selected) . ' e dispositivo: ' . $data['identificador_dispositivo'],
                'status' => 429
            ], 429);
        }
      
        $job = new ControlIdJob();

        $job->endpoint = $data['endpoint'];
        $job->status = 0;
        $job->uuid = $data['uuid'] ?? null;
        $job->response = $data['response'] ?? null;
        $job->log = $data['log'] ?? null;
        $job->identificador_dispositivo = $data['identificador_dispositivo'];

        if ($model_selected) {
            $model_selected->controlid_jobs()->save($job);

            
            return response()->json([
                'message' => 'Job criado com sucesso',
                'status' => 201
                
            ], 201);
        }                
      
        $job->save();

        return response()->json([
            'message' => 'Job criado com sucesso',
            'status' => 201
        ]);
    }        
    
    /**
     * Recupera todos os jobs pendentes ou com filtro
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPending($filter = null)
    {
        $query = ControlIdJob::where('status', 0)->where('tentativas', '<=', 3);
       
        if (isset($filter['endpoint']) && isset($filter['identificador_dispositivo']) && isset($filter['user_able_type']) && isset($filter['user_able_id'])) {
            $query->where('endpoint', $filter['endpoint'])
                ->where('identificador_dispositivo', $filter['identificador_dispositivo'])
                ->where('user_able_type', $filter['user_able_type'])
                ->where('user_able_id', $filter['user_able_id']);
        }
      
        return $query->orderBy('created_at', 'desc')->get();
    }

    public function obterJobPendente($deviceId): ControlIdJob | null
    {
        return ControlIdJob::where('identificador_dispositivo', $deviceId)
            ->where('status', 0)
            ->where('tentativas', '<=', 3)
            ->orderBy('created_at', 'desc')
            ->first();
    }


  
  
    
} 