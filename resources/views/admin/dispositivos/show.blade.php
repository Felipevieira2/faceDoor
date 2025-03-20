<x-layout.admin.app :title="$dispositivo->nome">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $dispositivo->nome }}</h1>
            <p class="mt-1 text-sm text-gray-600">
                {{ $dispositivo->tipo === 'entrada_condominio' ? 'Entrada do Condomínio' : 
                   ($dispositivo->tipo === 'saida_condominio' ? 'Saída do Condomínio' : 'Torre') }} | 
                {{ $dispositivo->condominio->nome }}
            </p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('dispositivos.edit', $dispositivo) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:border-indigo-700 focus:ring focus:ring-indigo-200 active:bg-indigo-700 transition">
                <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Editar
            </a>
            <form method="POST" action="{{ route('dispositivos.destroy', $dispositivo) }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:border-red-700 focus:ring focus:ring-red-200 active:bg-red-700 transition" onclick="return confirm('Tem certeza que deseja excluir este dispositivo?')">
                    <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Excluir
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <x-ui.card title="Informações do Dispositivo" class="md:col-span-2">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Nome</h3>
                    <p class="mt-1 text-sm text-gray-900">{{ $dispositivo->nome }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Tipo</h3>
                    <p class="mt-1 text-sm text-gray-900">
                        @if($dispositivo->tipo === 'entrada_condominio')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                Entrada do Condomínio
                            </span>
                        @elseif($dispositivo->tipo === 'saida_condominio')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                Saída do Condomínio
                            </span>
                        @elseif($dispositivo->tipo === 'torre')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                Torre
                            </span>
                        @endif
                    </p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Condomínio</h3>
                    <p class="mt-1 text-sm text-gray-900">
                        <a href="{{ route('admin.condominios.show', $dispositivo->condominio) }}" class="text-blue-600 hover:text-blue-900">
                            {{ $dispositivo->condominio->nome }}
                        </a>
                    </p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Localização</h3>
                    <p class="mt-1 text-sm text-gray-900">{{ $dispositivo->localizacao }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Status</h3>
                    <p class="mt-1 text-sm text-gray-900">
                        @if($dispositivo->status === 'ativo')
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
                    <h3 class="text-sm font-medium text-gray-500">Identificador</h3>
                    <p class="mt-1 text-sm text-gray-900">{{ $dispositivo->identificador }}</p>
                </div>
                @if($dispositivo->tipo === 'torre' && $dispositivo->torre)
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Torre Associada</h3>
                    <p class="mt-1 text-sm text-gray-900">
                        <a href="{{ route('torres.show', $dispositivo->torre) }}" class="text-blue-600 hover:text-blue-900">
                            {{ $dispositivo->torre->nome }}
                        </a>
                    </p>
                </div>
                @endif
            </div>
        </x-ui.card>

        <x-ui.card title="Estatísticas">
            <div class="space-y-4">
                <div>
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-medium text-gray-500">Total de Acessos</h3>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ $dispositivo->acessos_count ?? 0 }}
                        </span>
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-medium text-gray-500">Último Acesso</h3>
                        <span class="text-sm text-gray-900">
                            {{ $dispositivo->ultimo_acesso ? \Carbon\Carbon::parse($dispositivo->ultimo_acesso)->format('d/m/Y H:i') : 'Nunca' }}
                        </span>
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-medium text-gray-500">Criado em</h3>
                        <span class="text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($dispositivo->created_at)->format('d/m/Y H:i') }}
                        </span>
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-medium text-gray-500">Última Atualização</h3>
                        <span class="text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($dispositivo->updated_at)->format('d/m/Y H:i') }}
                        </span>
                    </div>
                </div>
            </div>
        </x-ui.card>
    </div>

    <div class="mt-6">
        <x-ui.card title="Histórico de Acessos">
            @if(empty($acessos) || count($acessos) === 0)
                <div class="text-center py-4">
                    <p class="text-sm text-gray-500">Nenhum acesso registrado para este dispositivo.</p>
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
                                    Visitante
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Apartamento
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
                                        {{ $acesso->visitante->user->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $acesso->visitante->apartamento->numero }} 
                                        ({{ $acesso->visitante->apartamento->torre->nome }})
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
</x-layout.app> 