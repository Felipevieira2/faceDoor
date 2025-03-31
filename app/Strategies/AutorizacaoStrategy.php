<?php

namespace App\Strategies;

use App\Models\Morador;
use App\Models\Visitante;
use App\Models\Dispositivo;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

interface AutorizacaoStrategy
{
    public function validar(Dispositivo $identificador_dispositivo, Visitante | Morador $model_selected): JsonResponse;
    public function createJobByEndpoint(Dispositivo $identificador_dispositivo, Visitante | Morador $model_selected, $endpoint): JsonResponse;
    public function validarRequest(Request $request): void;
}