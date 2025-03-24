<x-layout.admin.app title="Detalhes do Visitante">
    <div class="mb-2 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold">Detalhes do Visitante</h1>
            <p class="mt-1 text-sm">Informações detalhadas sobre o visitante.</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('admin.visitantes.edit', $visitante) }}" class="px-4 py-2 bg-blue-600 text-white rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
                <span class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Editar
                </span>
            </a>
            <a href="{{ route('admin.visitantes.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-md shadow-sm hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition">
                <span class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Voltar
                </span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
        <!-- Informações Pessoais -->
        <x-ui.card title="Informações Pessoais" class="h-fit">
            <div class="flex flex-col items-center mb-4">
                @if ($visitante->user && $visitante->user->foto)
                    <img src="{{ Storage::url($visitante->user->foto) }}" alt="{{ $visitante->user->name }}" class="h-32 w-32 rounded-full object-cover mb-4">
                @else
                    <div class="h-32 w-32 rounded-full bg-gray-200 flex items-center justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                @endif
                
                <h2 class="text-xl font-semibold text-center">{{ $visitante->user->name ?? 'N/A' }}</h2>
                <p class="text-gray-500">
                    @if($visitante->ativo)
                        @if($visitante->podeEntrar())
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Permitido
                            </span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                Fora do Período
                            </span>
                        @endif
                    @else
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                            Inativo
                        </span>
                    @endif
                </p>
            </div>
            
            <div class="space-y-4">
                <div>
                    <h3 class="text-sm font-medium text-gray-500">CPF</h3>
                    <p class="mt-1">{{ $visitante->user->cpf ?? 'Não informado' }}</p>
                </div>
                
                <div>
                    <h3 class="text-sm font-medium text-gray-500">E-mail</h3>
                    <p class="mt-1">{{ $visitante->user->email ?? 'Não informado' }}</p>
                </div>
                
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Telefone</h3>
                    <p class="mt-1">{{ $visitante->user->telefone ?? 'Não informado' }}</p>
                </div>
                
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Data de Nascimento</h3>
                    <p class="mt-1">{{ $visitante->user->data_nascimento ? date('d/m/Y', strtotime($visitante->user->data_nascimento)) : 'Não informado' }}</p>
                </div>
            </div>
        </x-ui.card>

        <!-- Informações da Visita -->
        <x-ui.card title="Informações da Visita" class="h-fit">
            <div class="space-y-4">
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Local de Visita</h3>
                    <p class="mt-1">
                        Torre: <strong>{{ $visitante->apartamento->torre->nome ?? 'N/A' }}</strong><br>
                        Apartamento: <strong>{{ $visitante->apartamento->numero ?? 'N/A' }}</strong>
                    </p>
                </div>
                
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Morador Responsável</h3>
                    <p class="mt-1">{{ $visitante->moradorResponsavel->user->name ?? 'Não informado' }}</p>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500">Período de Validade</h3>
                    <p class="mt-1">
                        Início: <strong>{{ $visitante->data_validade_inicio ? date('d/m/Y', strtotime($visitante->data_validade_inicio)) : 'Não informado' }}</strong><br>
                        Fim: <strong>{{ $visitante->data_validade_fim ? date('d/m/Y', strtotime($visitante->data_validade_fim)) : 'Indeterminado' }}</strong>
                    </p>
                </div>
            </div>
        </x-ui.card>

        <!-- Permissões de Acesso -->
        <x-ui.card title="Permissões de Acesso" class="h-fit">
            <div class="space-y-4">
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Tipo de Acesso</h3>
                    <p class="mt-1">
                        @if($visitante->recorrente)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                Visita Recorrente
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                Visita Específica
                            </span>
                        @endif
                    </p>
                </div>
                
                @if($visitante->recorrente)
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Dias da Semana</h3>
                        <div class="mt-1 flex flex-wrap gap-1">
                            @php
                                $diasSemanaTexto = [
                                    '0' => 'Domingo',
                                    '1' => 'Segunda',
                                    '2' => 'Terça',
                                    '3' => 'Quarta',
                                    '4' => 'Quinta',
                                    '5' => 'Sexta',
                                    '6' => 'Sábado'
                                ];
                                $diasPermitidos = $visitante->dias_semana ? explode(',', $visitante->dias_semana) : [];
                            @endphp
                            
                            @foreach($diasPermitidos as $dia)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ $diasSemanaTexto[$dia] ?? $dia }}
                                </span>
                            @endforeach
                            
                            @if(empty($diasPermitidos))
                                <span class="text-gray-500">Nenhum dia selecionado</span>
                            @endif
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Horários</h3>
                        <p class="mt-1">
                            Início: <strong>{{ $visitante->horario_inicio ? date('H:i', strtotime($visitante->horario_inicio)) : 'Não definido' }}</strong><br>
                            Fim: <strong>{{ $visitante->horario_fim ? date('H:i', strtotime($visitante->horario_fim)) : 'Não definido' }}</strong>
                        </p>
                    </div>
                @endif
                
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Histórico de Acesso</h3>
                    <p class="mt-1">
                        @if($visitante->data_hora_entrada)
                            Último acesso: <strong>{{ date('d/m/Y H:i', strtotime($visitante->data_hora_entrada)) }}</strong>
                        @else
                            Nenhum registro de acesso
                        @endif
                    </p>
                </div>
            </div>
        </x-ui.card>
    </div>
</x-layout.admin.app> 