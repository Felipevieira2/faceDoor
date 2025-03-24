<x-layout.admin.app title="Dispositivos">
    <div class="flex items-center justify-between mb-6 border-b border-gray-200 dark:border-gray-700 pb-3">
        <h1 class="text-2xl font-bold">Gerenciar Dispositivos</h1>

        <a href="{{ route('admin.dispositivos.create') }}" class="mt-2">
            <x-forms.button type="submit" variant="primary" class="cursor-pointer">
                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg> Novo Dispositivo
            </x-forms.button>
        </a>
    </div>

    <div class="flex items-center mb-3">
        <form action="#" method="GET" class="flex w-full">
            <div class="flex justify-between items-center gap-1 w-full sm:w-auto">
                <x-forms.input name="search" placeholder="Buscar" value="{{ request('search') }}" class="w-full" />
                <x-forms.button type="submit" variant="primary" class="cursor-pointer">
                    <i class="fas fa-search mr-2"></i> Buscar
                </x-forms.button>
            </div>
        </form>
    </div>

    <div class="flex flex-col">
        <div class="overflow-x-auto">
            @if($dispositivos->isEmpty())
                <x-ui.card>
                <div class="text-center py-12">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                    </svg>
                    <h3 class="mt-2 text-lg font-medium">Nenhum dispositivo encontrado</h3>
                    <p class="mt-1 text-sm">Comece adicionando um novo dispositivo ao sistema.</p>
                    <div class="mt-6">
                        <a href="{{ route('admin.dispositivos.create') }}">
                            <x-forms.button type="button" variant="primary" class="cursor-pointer">
                                Adicionar Dispositivo
                            </x-forms.button>
                        </a>
                    </div>
                </div>
                </x-ui.card>
            @else
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800 rounded-lg overflow-hidden">
                    <thead class="bg-gray-150 dark:bg-blue-600 rounded-t-lg shadow-md">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nome</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Fabricante</th>                      
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Localização</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-gray-50 dark:bg-gray-800 dark:divide-gray-700 rounded-b-lg">
                        @foreach($dispositivos as $dispositivo)
                            <tr class="hover:bg-gray-300 dark:hover:bg-gray-900">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium dark:text-white">{{ $dispositivo->nome . ' - ' . $dispositivo->identificador_unico }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm dark:text-white">{{ $dispositivo->fabricante }}</div>
                                </td>
                             
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm dark:text-white">{{ $dispositivo->localizacao_string . ($dispositivo->torre ? ' - ' . $dispositivo->torre->nome : '') }}</div>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($dispositivo->ativo )
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-white">
                                            Ativo
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200">
                                            Inativo
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('admin.dispositivos.edit', $dispositivo) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 mr-3 cursor-pointer" title="Editar Dispositivo">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    {{-- <a href="{{ route('admin.dispositivos.show', $dispositivo) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 cursor-pointer" title="Ver Detalhes">
                                        <i class="fa-solid fa-eye"></i>
                                    </a> --}}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <div class="mt-4">
            {{ $dispositivos->links() }}
        </div>
    </div>
</x-layout.admin.app> 