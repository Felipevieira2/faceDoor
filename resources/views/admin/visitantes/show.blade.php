<x-layout.admin.app :title="$visitante->user->name">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $visitante->user->name }}</h1>
            <p class="mt-1 text-sm text-gray-600">
                Visitante {{ $visitante->ativo ? 'ativo' : 'inativo' }} | 
                {{ $visitante->apartamento->torre->condominio->nome }} - 
                {{ $visitante->apartamento->torre->nome }}, Apto {{ $visitante->apartamento->numero }}
            </p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('visitantes.edit', $visitante) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:border-indigo-700 focus:ring focus:ring-indigo-200 active:bg-indigo-700 transition">
                <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Editar
            </a>
            <form method="POST" action="{{ route('visitantes.destroy', $visitante) }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:border-red-700 focus:ring focus:ring-red-200 active:bg-red-700 transition" onclick="return confirm('Tem certeza que deseja excluir este visitante?')">
                    <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Excluir
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <x-ui.card title="Informações Pessoais" class="md:col-span-2">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Nome</h3>
                    <p class="mt-1 text-sm text-gray-900">{{ $visitante->user->name }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">E-mail</h3>
                    <p class="mt-1 text-sm text-gray-900">{{ $visitante->user->email }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Telefone</h3>
                    <p class="mt-1 text-sm text-gray-900">{{ $visitante->telefone ?? 'Não informado' }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Documento</h3>
                    <p class="mt-1 text-sm text-gray-900">{{ $visitante->documento }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Status</h3>
                    <p class="mt-1 text-sm text-gray-900">
                        @if($visitante->ativo)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Ativo
                            </span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                Inativo
                            </span>
                        @endif
                    </p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Tipo de Visita</h3>
                    <p class="mt-1 text-sm text-gray-900">
                        @if($visitante->recorrente)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                Recorrente
                            </span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                Pontual
                            </span>
                        @endif
                    </p>
                </div>
            </div>

            <div class="mt-6">
                <h3 class="text-sm font-medium text-gray-500">Observações</h3>
                <p class="mt-1 text-sm text-gray-900">{{ $visitante->observacoes ?? 'Nenhuma observação registrada.' }}</p>
            </div>
        </x-ui.card>

        <x-ui.card title="Detalhes da Visita">
            <div class="space-y-4">
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Condomínio</h3>
                    <p class="mt-1 text-sm text-gray-900">
                        <a href="{{ route('admin.condominios.show', $visitante->apartamento->torre->condominio) }}" class="text-blue-600 hover:text-blue-900">
                            {{ $visitante->apartamento->torre->condominio->nome }}
                        </a>
                    </p>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500">Torre</h3>
                    <p class="mt-1 text-sm text-gray-900">
                        <a href="{{ route('torres.show', $visitante->apartamento->torre) }}" class="text-blue-600 hover:text-blue-900">
                            {{ $visitante->apartamento->torre->nome }}
                        </a>
                    </p>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500">Apartamento</h3>
                    <p class="mt-1 text-sm text-gray-900">
                        {{ $visitante->apartamento->numero }}
                    </p>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500">Morador Responsável</h3>
                    <p class="mt-1 text-sm text-gray-900">
                        {{ $visitante->moradorResponsavel->user->name }}
                    </p>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500">Validade</h3>
                    <p class="mt-1 text-sm text-gray-900">
                        {{ \Carbon\Carbon::parse($visitante->data_validade_inicio)->format('d/m/Y') }}
                        @if($visitante->data_validade_fim)
                            até {{ \Carbon\Carbon::parse($visitante->data_validade_fim)->format('d/m/Y') }}
                        @endif
                    </p>
                </div>

                @if($visitante->recorrente)
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Dias da Semana</h3>
                        <p class="mt-1 text-sm text-gray-900">
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
                        </p>
                    </div>

                    @if($visitante->horario_inicio && $visitante->horario_fim)
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Horário</h3>
                            <p class="mt-1 text-sm text-gray-900">
                                {{ \Carbon\Carbon::parse($visitante->horario_inicio)->format('H:i') }} - 
                                {{ \Carbon\Carbon::parse($visitante->horario_fim)->format('H:i') }}
                            </p>
                        </div>
                    @endif
                @endif
            </div>
        </x-ui.card>
    </div>

    <div class="mt-6">
        <x-ui.card title="Histórico de Acessos">
            @if(empty($acessos) || count($acessos) === 0)
                <div class="text-center py-4">
                    <p class="text-sm text-gray-500">Nenhum acesso registrado para este visitante.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Data/Hora
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Dispositivo
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tipo
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($acessos as $acesso)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ \Carbon\Carbon::parse($acesso->created_at)->format('d/m/Y H:i:s') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        <a href="{{ route('dispositivos.show', $acesso->dispositivo) }}" class="text-blue-600 hover:text-blue-900">
                                            {{ $acesso->dispositivo->nome }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if($acesso->tipo === 'entrada')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Entrada
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                Saída
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if($acesso->autorizado)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Autorizado
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                Negado
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $acessos->links() }}
                </div>
            @endif
        </x-ui.card>
    </div>

    <div class="mt-6 flex justify-between">
        <x-ui.card title="Registrar Acesso" class="w-full md:w-1/2 mr-3">
            <div class="space-y-4">
                <div class="text-center">
                    <p class="text-sm text-gray-600 mb-4">Registre a entrada ou saída deste visitante manualmente.</p>
                    
                    <div class="flex justify-center space-x-4">
                        <a href="{{ route('visitantes.registrar-entrada', $visitante) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:border-green-700 focus:ring focus:ring-green-200 active:bg-green-700 transition">
                            <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                            </svg>
                            Registrar Entrada
                        </a>
                        
                        <a href="{{ route('visitantes.registrar-saida', $visitante) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-200 active:bg-blue-700 transition">
                            <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Registrar Saída
                        </a>
                    </div>
                </div>
            </div>
        </x-ui.card>

        <x-ui.card title="Status Atual" class="w-full md:w-1/2 ml-3">
            <div class="text-center py-4">
                @if($visitante->podeEntrar())
                    <div class="inline-flex items-center justify-center p-4 bg-green-100 rounded-full mb-4">
                        <svg class="h-12 w-12 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-medium text-gray-900 mb-2">Acesso Permitido</h3>
                    <p class="text-sm text-gray-600">Este visitante tem permissão para entrar no condomínio neste momento.</p>
                @else
                    <div class="inline-flex items-center justify-center p-4 bg-red-100 rounded-full mb-4">
                        <svg class="h-12 w-12 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-medium text-gray-900 mb-2">Acesso Negado</h3>
                    <p class="text-sm text-gray-600">Este visitante não tem permissão para entrar no condomínio neste momento.</p>
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