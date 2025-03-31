<?php

namespace App\Strategies\ControlID\ResultEvent\Interfaces;

use App\Models\ControlIdJob;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

interface ControlIdJobsHandlerStrategyInterface
{
    /**
     * Manipula um job específico
     *
     * @param ControlIdJob $job
     * @param Request $request
     * @return JsonResponse Resposta do processamento
     */
    public function handle(ControlIdJob $job, Request $request): JsonResponse;
} 