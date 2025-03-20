<x-layout.admin.app title="Dashboard">
    <div class="mb-6">
        <h1 class="text-2xl font-bold ">Painel do Administrador</h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Nome do condomínio.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-ui.card title="Dispositivos" class="dark:bg-gradient-to-br dark:from-green-700 dark:to-green-950">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                    <svg class="h-8 w-8 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                    </svg>
                </div>
                <div class="ml-5">
                    <div class="text-3xl font-semibold ">{{ $dispositivos }}</div>
                    <div class="text-sm ">Total de dispositivos</div>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.dispositivos.index') }}" class="text-green-600 hover:text-green-800 text-sm font-medium">
                    Ver todos os dispositivos →
                </a>
            </div>
        </x-ui.card>

        <x-ui.card title="Moradores" class="bg-gradient-to-br from-yellow-700 to-yellow-950">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                    <svg class="h-8 w-8 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div class="ml-5">
                    <div class="text-3xl font-semibold ">{{ $moradores ?? 0 }}</div>
                    <div class="text-sm ">Total de moradores</div>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.moradores.index') }}" class="text-yellow-600 hover:text-yellow-800 text-sm font-medium">
                    Ver todos os moradores →
                </a>
            </div>
        </x-ui.card>

        <x-ui.card title="Visitantes" class="bg-gradient-to-br from-blue-700 to-blue-950 ">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                   {{-- svg de visitante --}}
                  <svg class="h-8 w-8 text-white"  version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="-5.51 -5.51 66.10 66.10" xml:space="preserve" fill="#ffffff" stroke="#ffffff" stroke-width="0.0005507999999999999"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="0.11016"></g><g id="SVGRepo_iconCarrier"> <g> <g> <path style="fill:#ffffff;" d="M47.748,23.991H31.651c-0.475,0-0.858,0.384-0.858,0.857v10.66c0,0.473,0.384,0.858,0.858,0.858 h16.097c0.474,0,0.858-0.386,0.858-0.858v-10.66C48.606,24.375,48.221,23.991,47.748,23.991z M46.889,34.649H32.51v-8.941h14.379 C46.889,25.708,46.889,34.649,46.889,34.649z"></path> <path style="fill:#ffffff;" d="M27.386,26.644c-0.246-0.004-0.441,0.163-0.458,0.399c-0.059,0.881-1.164,1.404-1.817,1.639 c-3.116,1.116-8.7,0.528-10.963-1.875c-0.959-1.021-2.054-1.522-3.272-1.479c-2.623,0.086-4.792,2.677-4.883,2.786 c-0.151,0.184-0.125,0.452,0.057,0.604c0.183,0.15,0.455,0.125,0.605-0.057c0.02-0.024,2.01-2.401,4.25-2.475 c0.949-0.029,1.835,0.376,2.618,1.208c1.705,1.811,4.975,2.693,7.973,2.692c1.43,0,2.798-0.202,3.905-0.598 c1.883-0.674,2.336-1.671,2.384-2.389C27.801,26.864,27.623,26.659,27.386,26.644z"></path> <path style="fill:#ffffff;" d="M27.386,32.923c-0.246-0.011-0.441,0.164-0.458,0.4c-0.059,0.881-1.164,1.404-1.817,1.638 c-3.116,1.118-8.7,0.528-10.963-1.875c-0.959-1.019-2.054-1.521-3.272-1.476c-2.623,0.084-4.792,2.674-4.883,2.783 c-0.151,0.184-0.125,0.453,0.057,0.605c0.183,0.15,0.455,0.125,0.605-0.059c0.02-0.023,2.01-2.4,4.25-2.474 c0.949-0.031,1.835,0.375,2.618,1.207c1.705,1.812,4.975,2.693,7.973,2.693c1.43,0,2.798-0.202,3.905-0.598 c1.883-0.674,2.336-1.671,2.384-2.39C27.801,33.144,27.623,32.939,27.386,32.923z"></path> <path style="fill:#ffffff;" d="M2.378,49.118h50.324c1.311,0,2.378-1.066,2.378-2.377V15.728c0-1.312-1.067-2.378-2.378-2.378 H32.734V8.339c0-1.399-0.769-2.377-1.869-2.377h-6.649c-1.101,0-1.869,0.977-1.869,2.377v5.011H2.378 C1.067,13.35,0,14.416,0,15.728v31.014C0,48.052,1.067,49.118,2.378,49.118z M25.352,8.962h4.381l-0.005,4.388h-4.381 L25.352,8.962z M3,16.35h21.215h6.649H52.08v29.768H3V16.35z"></path> </g> </g> </g></svg>
                </div>
                <div class="ml-5">
                    <div class="text-3xl font-semibold ">{{ $condominios }}</div>
                    <div class="text-sm ">Total de visitantes</div>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.condominios.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    Ver todos os visitantes →
                </a>
            </div>
        </x-ui.card>

        <x-ui.card title="Acessos" class="bg-gradient-to-br from-red-700 to-red-950">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-red-500 rounded-md p-3">
                    {{-- svg de acesso --}}
                    <svg class="h-8 w-8 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                </div>
                <div class="ml-5">
                    <div class="text-3xl font-semibold ">{{ $ocorrencias ?? 0 }}</div>
                    <div class="text-sm ">Últimos acessos</div>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.ocorrencias.index') }}" class="text-red-600 hover:text-red-800 text-sm font-medium">
                    {{-- Ver todas as ocorrências → --}}
                </a>
            </div>
        </x-ui.card>
    </div>
    {{-- <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-6">
        <!-- Card de Estatísticas - Usuários -->
        <x-ui.card class="bg-gradient-to-br from-indigo-500 to-indigo-600 dark:from-indigo-600 dark:to-indigo-800">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-white dark:bg-gray-800 rounded-md p-3">
                    <i class="fas fa-users text-indigo-600 dark:text-indigo-400 text-xl"></i>
                </div>
                <div class="ml-5">
                    <p class="text-sm font-medium text-white truncate">Total de Usuários</p>
                    <p class="mt-1 text-3xl font-semibold text-white">{{ 0 }}</p>
                </div>
            </div>
        </x-ui.card>
        
        <!-- Card de Estatísticas - Consultas -->
        <x-ui.card class="bg-gradient-to-br from-green-500 to-green-600 dark:from-green-600 dark:to-green-800">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-white dark:bg-gray-800 rounded-md p-3">
                    <i class="fas fa-search text-green-600 dark:text-green-400 text-xl"></i>
                </div>
                <div class="ml-5">
                    <p class="text-sm font-medium text-white truncate">Consultas Realizadas</p>
                    <p class="mt-1 text-3xl font-semibold text-white">{{ 0 }}</p>
                </div>
            </div>
        </x-ui.card>
        
        <!-- Card de Estatísticas - Planos -->
        <x-ui.card class="bg-gradient-to-br from-purple-500 to-purple-600 dark:from-purple-600 dark:to-purple-800">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-white dark:bg-gray-800 rounded-md p-3">
                    <i class="fas fa-tags text-purple-600 dark:text-purple-400 text-xl"></i>
                </div>
                <div class="ml-5">
                    <p class="text-sm font-medium text-white truncate">Planos Ativos</p>
                    <p class="mt-1 text-3xl font-semibold text-white">{{ 0 }}</p>
                </div>
            </div>
        </x-ui.card>
        
        <!-- Card de Estatísticas - Receita -->
        <x-ui.card class="bg-gradient-to-br from-blue-500 to-blue-600 dark:from-blue-600 dark:to-blue-800">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-white dark:bg-gray-800 rounded-md p-3">
                    <i class="fas fa-dollar-sign text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
                <div class="ml-5">
                    <p class="text-sm font-medium text-white truncate">Receita Mensal</p>
                    <p class="mt-1 text-3xl font-semibold text-white">0</p>
                </div>
            </div>
        </x-ui.card>
    </div> --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <x-ui.card title="Usuários aguardando aprovação" class="bg-white">
                <div class="space-y-4">
                    @forelse($atividades ?? [] as $atividade)
                        <div class="flex items-start p-3 border-b border-gray-100">
                            <div class="flex-shrink-0 mr-3">
                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                                    <span class="text-blue-600 font-semibold">{{ substr($atividade->usuario ?? 'U', 0, 1) }}</span>
                                </div>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $atividade->descricao ?? 'Ação realizada' }}</p>
                                <p class="text-xs text-gray-500">{{ $atividade->created_at ? $atividade->created_at->diffForHumans() : 'Recentemente' }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="py-4 text-center text-gray-500">
                            <p>Nenhuma atividade registrada recentemente.</p>
                        </div>
                    @endforelse
                </div>
                <div class="mt-4 text-right">
                    <a href="{{ route('admin.atividades.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        Ver todos os usuários aguardando aprovação →
                    </a>
                </div>
            </x-ui.card>

            <x-ui.card title="Últimos Acessos" class="bg-white">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Morador</th>
                                <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Local</th>
                                <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data/Hora</th>
                                <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($acessos ?? [] as $acesso)
                                <tr>
                                    <td class="px-4 py-3 whitespace-nowrap">{{ $acesso->morador ?? 'Nome do morador' }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap">{{ $acesso->local ?? 'Local de acesso' }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap">{{ $acesso->data_hora ?? now()->format('d/m/Y H:i') }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ ($acesso->status ?? 'Permitido') === 'Permitido' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $acesso->status ?? 'Permitido' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-3 text-center text-gray-500">Nenhum acesso registrado recentemente.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 text-right">
                    <a href="{{ route('admin.acessos.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        Ver todos os acessos →
                    </a>
                </div>
            </x-ui.card>
        </div>

        <div class="space-y-6">
            <x-ui.card title="Ações Rápidas" class="bg-gradient-to-br from-gray-700 to-gray-950">
                <div class="space-y-3">
                    <a href="{{ route('admin.condominios.create') }}" class="block px-4 py-2 bg-white rounded-md shadow-sm hover:bg-gray-50 transition-colors duration-150">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 text-purple-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            <span class="ml-2 text-sm font-medium ">Adicionar Condomínio</span>
                        </div>
                    </a>
                    <a href="{{ route('admin.dispositivos.create') }}" class="block px-4 py-2 bg-white rounded-md shadow-sm hover:bg-gray-50 transition-colors duration-150">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 text-purple-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            <span class="ml-2 text-sm font-medium ">Adicionar Dispositivo</span>
                        </div>
                    </a>
                    <a href="{{ route('admin.moradores.create') }}" class="block px-4 py-2 bg-white rounded-md shadow-sm hover:bg-gray-50 transition-colors duration-150">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 text-purple-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            <span class="ml-2 text-sm font-medium ">Cadastrar Morador</span>
                        </div>
                    </a>
                    <a href="{{ route('admin.ocorrencias.create') }}" class="block px-4 py-2 bg-white rounded-md shadow-sm hover:bg-gray-50 transition-colors duration-150">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 text-purple-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            <span class="ml-2 text-sm font-medium ">Registrar Ocorrência</span>
                        </div>
                    </a>
                </div>
            </x-ui.card>

            <x-ui.card title="Comunicados" class="bg-white">
                <div class="space-y-3">
                    @forelse($comunicados ?? [] as $comunicado)
                        <div class="p-3 bg-yellow-50 rounded-lg">
                            <h4 class="font-medium text-gray-900">{{ $comunicado->titulo ?? 'Título do comunicado' }}</h4>
                            <p class="text-sm text-gray-600 mt-1">{{ $comunicado->mensagem ?? 'Detalhes do comunicado...' }}</p>
                            <div class="flex justify-between items-center mt-2 text-xs text-gray-500">
                                <span>{{ $comunicado->data ?? now()->format('d/m/Y') }}</span>
                                <span>Por: {{ $comunicado->autor ?? 'Admin' }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="py-4 text-center text-gray-500">
                            <p>Nenhum comunicado publicado recentemente.</p>
                        </div>
                    @endforelse
                </div>
                <div class="mt-4 text-right">
                    <a href="{{ route('admin.comunicados.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        Ver todos os comunicados →
                    </a>
                </div>
            </x-ui.card>

            <x-ui.card title="Agenda" class="bg-white">
                <div class="space-y-2">
                    @forelse($eventos ?? [] as $evento)
                        <div class="flex items-center p-2 border-l-4 {{ $evento->prioridade == 'alta' ? 'border-red-500' : 'border-blue-500' }} bg-gray-50 rounded-r-lg">
                            <div class="flex-1">
                                <div class="font-medium text-gray-900">{{ $evento->titulo ?? 'Título do evento' }}</div>
                                <div class="text-xs text-gray-500">{{ $evento->data ?? now()->format('d/m/Y') }} às {{ $evento->hora ?? '09:00' }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="py-4 text-center text-gray-500">
                            <p>Nenhum evento agendado.</p>
                        </div>
                    @endforelse
                </div>
                <div class="mt-4 text-right">
                    <a href="{{ route('admin.eventos.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        Ver agenda completa →
                    </a>
                </div>
            </x-ui.card>
        </div>
    </div>
</x-layout.admin.app>