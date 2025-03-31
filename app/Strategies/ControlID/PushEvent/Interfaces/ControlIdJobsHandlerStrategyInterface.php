<?php

namespace App\Strategies\ControlID\PushEvent\Interfaces;

use App\Models\ControlIdJob;

interface ControlIdJobsHandlerStrategyInterface
{
    /**
     * Manipula um job específico
     *
     * @param ControlIdJob $job
     * @return array Resposta do processamento
     */
    public function handle(ControlIdJob $job): array;
} 