<x-layout.admin.app title="Detalhes da Torre">
    <div class="mb-2 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold">Detalhes da Torre</h1>
            <p class="mt-1 text-sm">Informações detalhadas sobre a torre.</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('admin.torres.edit', $torre) }}" class="px-4 py-2 bg-blue-600 text-white rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
                <span class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Editar
                </span>
            </a>
            <a href="{{ route('admin.torres.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-md shadow-sm hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition">
                <span class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Voltar
                </span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
        <!-- Informações Básicas -->
        <x-ui.card title="Informações da Torre" class="h-fit">
            <div class="space-y-4">
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Nome da Torre</h3>
                    <p class="mt-1 text-lg">{{ $torre->nome }}</p>
                </div>
                
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Condomínio</h3>
                    <p class="mt-1 text-lg">{{ $torre->condominio->nome ?? 'N/A' }}</p>
                </div>
                
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Número de Andares</h3>
                    <p class="mt-1 text-lg">{{ $torre->numero_andares ?? 'Não informado' }}</p>
                </div>
                
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Descrição</h3>
                    <p class="mt-1">{{ $torre->descricao ?? 'Sem descrição' }}</p>
                </div>
            </div>
        </x-ui.card>

        <!-- Apartamentos -->
        <x-ui.card title="Apartamentos" class="h-fit">
            @if($torre->apartamentos->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Número
                                </th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Bloco
                                </th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Moradores
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($torre->apartamentos as $apartamento)
                                <tr>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                        {{ $apartamento->numero }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        {{ $apartamento->bloco ?? 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        {{ $apartamento->moradores_count ?? $apartamento->moradores->count() }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500 text-center py-4">Nenhum apartamento cadastrado nesta torre.</p>
            @endif
        </x-ui.card>
    </div>
</x-layout.admin.app> 