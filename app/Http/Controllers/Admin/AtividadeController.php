<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Atividade;
use Illuminate\Http\Request;

class AtividadeController extends Controller
{
    public function index()
    {
        $atividades = [
            [
                'id' => 1,
                'nome' => 'Atividade 1',
                'descricao' => 'Descrição da atividade 1',
                'status' => 'Em andamento',
                'data_inicio' => '2021-01-01',
                'data_termino' => '2021-01-05',
                'usuario' => [
                    'id' => 1,
                    'nome' => 'Usuário 1',
                    'email' => 'usuario1@example.com',
                ],
            ],
            [
                'id' => 2,
                'nome' => 'Atividade 2',
                'descricao' => 'Descrição da atividade 2',
                'status' => 'Concluído',
            ],
        ];

        return view('admin.atividades.index', compact('atividades'));
    }
} 