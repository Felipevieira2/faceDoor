<?php

namespace App\Repositories\Eloquent;

use App\Models\ControlIdJob;

use Illuminate\Support\Facades\Log;
use App\Repositories\Interfaces\JobRepositoryInterface;
use App\Repositories\Interfaces\ControlIdJobRepositoryInterface;

class ControlIdJobRepository implements ControlIdJobRepositoryInterface
{
    /**
     * @var Job
     */
    protected $model;

    /**
     * JobRepository constructor.
     *
     * @param Job $model
     */
    public function __construct(ControlIdJob $model)
    {
        $this->model = $model;
    }

    /**
     * Obtém um job pendente pelo ID do dispositivo
     *
     * @param string $deviceId
     * @return Job|null
     */
    public function getPendingJobByDeviceId(string $deviceId)
    {
        Log::info('getPendingJobByDeviceId');
        Log::info($deviceId);
        
        return $this->model
            ->where('identificador_dispositivo', $deviceId)
            ->where('status', 0)
            ->orderBy('created_at')
            ->first();
    }

    /**
     * Cria um novo job
     *
     * @param array $data
     * @return Job
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * Atualiza o status de um job
     *
     * @param int $jobId
     * @param string $status
     * @return bool
     */
    public function updateStatus(int $jobId, string $status)
    {
        $job = $this->find($jobId);
        
        if (!$job) {
            return false;
        }
        
        $job->status = $status;
        return $job->save();
    }

    /**
     * Busca um job pelo ID
     *
     * @param int $jobId
     * @return Job|null
     */
    public function find(int $jobId)
    {
        return $this->model->find($jobId);
    }

    /**
     * Lista todos os jobs para um dispositivo específico
     *
     * @param string $deviceId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllByDeviceId(string $deviceId)
    {
        return $this->model
            ->where('identificador_dispositivo', $deviceId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getJobByUuid(string $uuid) : ControlIdJob | null
    {

        return $this->model
            ->where('uuid', $uuid)
            ->first();
    }
} 