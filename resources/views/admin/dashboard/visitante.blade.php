<x-layout.admin.app title="Dashboard">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Painel do Visitante</h1>
        <p class="mt-1 text-sm text-gray-600">Bem-vindo ao sistema de gestão de acesso de condomínio.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <x-ui.card title="Informações da Visita" class="bg-gradient-to-br from-blue-50 to-blue-100">
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-500">Condomínio:</span>
                    <span class="text-sm text-gray-900">{{ $apartamento->torre->condominio->nome }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-500">Torre:</span>
                    <span class="text-sm text-gray-900">{{ $apartamento->torre->nome }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-500">Apartamento:</span>
                    <span class="text-sm text-gray-900">{{ $apartamento->numero }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-500">Morador Responsável:</span>
                    <span class="text-sm text-gray-900">{{ $moradorResponsavel->user->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-500">Status:</span>
                    <span class="text-sm">
                        @if($visitante->ativo)
                            <x-ui.badge variant="success" size="sm">Ativo</x-ui.badge>
                        @else
                            <x-ui.badge variant="danger" size="sm">Inativo</x-ui.badge>
                        @endif
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-500">Validade:</span>
                    <span class="text-sm text-gray-900">
                        {{ \Carbon\Carbon::parse($visitante->data_validade_inicio)->format('d/m/Y') }}
                        @if($visitante->data_validade_fim)
                            até {{ \Carbon\Carbon::parse($visitante->data_validade_fim)->format('d/m/Y') }}
                        @endif
                    </span>
                </div>
                @if($visitante->recorrente)
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-500">Recorrência:</span>
                    <span class="text-sm text-gray-900">
                        @php
                            $diasSemana = [
                                '0' => 'Domingo',
                                '1' => 'Segunda',
                                '2' => 'Terça',
                                '3' => 'Quarta',
                                '4' => 'Quinta',
                                '5' => 'Sexta',
                                '6' => 'Sábado'
                            ];
                            $diasPermitidos = explode(',', $visitante->dias_semana);
                            $diasFormatados = [];
                            foreach ($diasPermitidos as $dia) {
                                $diasFormatados[] = $diasSemana[$dia] ?? '';
                            }
                        @endphp
                        {{ implode(', ', $diasFormatados) }}
                    </span>
                </div>
                @if($visitante->horario_inicio && $visitante->horario_fim)
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-500">Horário:</span>
                    <span class="text-sm text-gray-900">
                        {{ \Carbon\Carbon::parse($visitante->horario_inicio)->format('H:i') }} - 
                        {{ \Carbon\Carbon::parse($visitante->horario_fim)->format('H:i') }}
                    </span>
                </div>
                @endif
                @endif
            </div>
        </x-ui.card>

        <x-ui.card title="Status de Acesso" class="bg-gradient-to-br from-green-50 to-green-100">
            <div class="text-center py-4">
                @if($visitante->podeEntrar())
                    <div class="inline-flex items-center justify-center p-4 bg-green-100 rounded-full mb-4">
                        <svg class="h-12 w-12 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-medium text-gray-900 mb-2">Acesso Permitido</h3>
                    <p class="text-sm text-gray-600">Você tem permissão para entrar no condomínio neste momento.</p>
                @else
                    <div class="inline-flex items-center justify-center p-4 bg-red-100 rounded-full mb-4">
                        <svg class="h-12 w-12 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-medium text-gray-900 mb-2">Acesso Negado</h3>
                    <p class="text-sm text-gray-600">Você não tem permissão para entrar no condomínio neste momento.</p>
                @endif
            </div>
            
            <div class="mt-4 border-t border-gray-200 pt-4">
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-500">Última entrada:</span>
                    <span class="text-sm text-gray-900">
                        @if($visitante->data_hora_entrada)
                            {{ \Carbon\Carbon::parse($visitante->data_hora_entrada)->format('d/m/Y H:i') }}
                        @else
                            Não registrada
                        @endif
                    </span>
                </div>
                <div class="flex justify-between mt-2">
                    <span class="text-sm font-medium text-gray-500">Última saída:</span>
                    <span class="text-sm text-gray-900">
                        @if($visitante->data_hora_saida)
                            {{ \Carbon\Carbon::parse($visitante->data_hora_saida)->format('d/m/Y H:i') }}
                        @else
                            Não registrada
                        @endif
                    </span>
                </div>
            </div>
        </x-ui.card>
    </div>
</x-layout.app> 