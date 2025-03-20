<x-layout.admin.app :title="$condominio->nome">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $condominio->nome }}</h1>
            <p class="mt-1 text-sm text-gray-600">{{ $condominio->endereco }}, {{ $condominio->cidade }}/{{ $condominio->estado }}</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('admin.condominios.edit', $condominio) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:border-indigo-700 focus:ring focus:ring-indigo-200 active:bg-indigo-700 transition">
                <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Editar
            </a>
            <form method="POST" action="{{ route('admin.condominios.destroy', $condominio) }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:border-red-700 focus:ring focus:ring-red-200 active:bg-red-700 transition" onclick="return confirm('Tem certeza que deseja excluir este condomínio?')">
                    <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Excluir
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <x-ui.card title="Informações do Condomínio" class="md:col-span-2">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Endereço</h3>
                    <p class="mt-1 text-sm text-gray-900">{{ $condominio->endereco }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">CEP</h3>
                    <p class="mt-1 text-sm text-gray-900">{{ $condominio->cep }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Cidade</h3>
                    <p class="mt-1 text-sm text-gray-900">{{ $condominio->cidade }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Estado</h3>
                    <p class="mt-1 text-sm text-gray-900">{{ $condominio->estado }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Telefone</h3>
                    <p class="mt-1 text-sm text-gray-900">{{ $condominio->telefone ?? 'Não informado' }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">E-mail</h3>
                    <p class="mt-1 text-sm text-gray-900">{{ $condominio->email ?? 'Não informado' }}</p>
                </div>
            </div>
        </x-ui.card>

        <x-ui.card title="Estatísticas">
            <div class="space-y-4">
                <div>
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-medium text-gray-500">Torres</h3>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ $condominio->torres->count() }}
                        </span>
                    </div>
                    <div class="mt-2">
                        <a href="{{ route('admin.condominios.torres.create', $condominio) }}" class="text-sm text-blue-600 hover:text-blue-900">
                            + Adicionar torre
                        </a>
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-medium text-gray-500">Dispositivos</h3>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            {{ $condominio->dispositivos->count() }}
                        </span>
                    </div>
                    <div class="mt-2">
                        <a href="{{ route('dispositivos.create') }}?condominio_id={{ $condominio->id }}" class="text-sm text-blue-600 hover:text-blue-900">
                            + Adicionar dispositivo
                        </a>
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-medium text-gray-500">Total de Apartamentos</h3>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                            {{ $condominio->torres->flatMap->apartamentos->count() }}
                        </span>
                    </div>
                </div>
            </div>
        </x-ui.card>
    </div>

    <div class="mt-6">
        <x-ui.card title="Torres">
            @if($condominio->torres->isEmpty())
                <div class="text-center py-4">
                    <p class="text-sm text-gray-500">Nenhuma torre cadastrada para este condomínio.</p>
                    <div class="mt-2">
                        <a href="{{ route('admin.condominios.torres.create', $condominio) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Adicionar Torre
                        </a>
                    </div>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nome
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Descrição
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Andares
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Apartamentos
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Dispositivo
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Ações
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($condominio->torres as $torre)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $torre->nome }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $torre->descricao ?? 'Não informada' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $torre->numero_andares ?? 'Não informado' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ $torre->apartamentos->count() }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if($torre->dispositivo)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                {{ $torre->dispositivo->nome }}
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                Não possui
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('torres.show', $torre) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                            Ver
                                        </a>
                                        <a href="{{ route('torres.edit', $torre) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                            Editar
                                        </a>
                                        <a href="{{ route('torres.apartamentos.create', $torre) }}" class="text-green-600 hover:text-green-900 mr-3">
                                            + Apartamento
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-ui.card>
    </div>

    <div class="mt-6">
        <x-ui.card title="Dispositivos">
            @if($condominio->dispositivos->isEmpty())
                <div class="text-center py-4">
                    <p class="text-sm text-gray-500">Nenhum dispositivo cadastrado para este condomínio.</p>
                    <div class="mt-2">
                        <a href="{{ route('dispositivos.create') }}?condominio_id={{ $condominio->id }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Adicionar Dispositivo
                        </a>
                    </div>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nome
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tipo
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Localização
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Torre
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Ações
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($condominio->dispositivos as $dispositivo)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $dispositivo->nome }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if($dispositivo->tipo === 'entrada_condominio')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                Entrada Condomínio
                                            </span>
                                        @elseif($dispositivo->tipo === 'saida_condominio')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                Saída Condomínio
                                            </span>
                                        @elseif($dispositivo->tipo === 'torre')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                                Torre
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $dispositivo->localizacao ?? 'Não informada' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if($dispositivo->torre)
                                            {{ $dispositivo->torre->nome }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if($dispositivo->ativo)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Ativo
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                Inativo
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('dispositivos.show', $dispositivo) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                            Ver
                                        </a>
                                        <a href="{{ route('dispositivos.edit', $dispositivo) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                            Editar
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-ui.card>
    </div>
</x-layout.app> 