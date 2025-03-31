<?php

namespace App\Repositories\Interfaces;

use App\Models\ControlIdJob;

interface ControlIdJobRepositoryInterface
{
    /**
     * Obtém um job pendente pelo ID do dispositivo
     *
     * @param string $deviceId
     * @return ControlIdJob|null
     */
    public function getPendingJobByDeviceId(string $deviceId);
    
    /**
     * Cria um novo job
     *
     * @param array $data
     * @return ControlIdJob
     */
    public function create(array $data);
    
    /**
     * Atualiza o status de um job
     *
     * @param int $jobId
     * @param string $status
     * @return bool
     */
    public function updateStatus(int $jobId, string $status);
    
    /**
     * Busca um job pelo ID
     *
     * @param int $jobId
     * @return ControlIdJob|null
     */
    public function find(int $jobId);
    
    /**
     * Lista todos os jobs para um dispositivo específico
     *
     * @param string $deviceId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllByDeviceId(string $deviceId);

    /**
     * Busca um job pelo UUID
     *
     * @param string $uuid
     * @return ControlIdJob|null
     */
    public function getJobByUuid(string $uuid) : ControlIdJob | null;
} 