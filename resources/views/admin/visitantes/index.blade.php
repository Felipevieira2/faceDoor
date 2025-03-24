<x-layout.admin.app title="Visitantes">
    <div class="flex items-center justify-between mb-6 border-b border-gray-200 dark:border-gray-700 pb-3">

        <div>
            <h1 class="text-2xl font-bold">Visitantes</h1>
         
        </div>
        <a href="{{ route('admin.visitantes.create') }}"
            class="mt-2">
            <x-forms.button type="submit" variant="primary" class="cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Novo Visitante
            </x-forms.button>
        </a>
    </div>
    <div class="flex items-center  mb-3 ">
        <form action="#" method="GET" class="flex w-full">
            <div class="flex justify-between items-center gap-1 w-full sm:w-auto">
                <x-forms.input name="search" placeholder="Buscar " value="{{ request('search') }}" class="w-full " />
                <x-forms.button type="submit" variant="primary" class="cursor-pointer">
                    <i class="fas fa-search mr-2"></i> Buscar
                </x-forms.button>
            </div>
        </form>
    </div>

    @if ($visitantes->isEmpty())
        <x-ui.card>
            <div class="text-center py-12">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                </svg>
                <h3 class="mt-2 text-lg font-medium">Nenhum visitante encontrado</h3>
                <p class="mt-1 text-sm">Comece adicionando um novo visitante ao sistema.</p>
                <div class="mt-6">
                    <a href="{{ route('admin.visitantes.create') }}">
                        <x-forms.button type="button" variant="primary" class="cursor-pointer">
                            Adicionar Visitante
                        </x-forms.button>
                    </a>
                </div>
            </div>
        </x-ui.card>
    @else
        <div class="flex flex-col">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800 rounded-lg overflow-hidden">
                    <thead class="bg-gray-150 dark:bg-blue-600 rounded-t-lg shadow-md">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">
                                Visitante
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">
                                Apartamento
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">
                                Responsável
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">
                                Validade
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-right text-xs font-medium  uppercase tracking-wider">
                                Ações
                            </th>
                        </tr>
                    </thead>
                    <tbody
                        class="divide-y divide-gray-200 bg-gray-50 dark:bg-gray-800 dark:divide-gray-700 rounded-b-lg">
                        @forelse ($visitantes as $visitante)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            @if ($visitante->user && $visitante->user->foto)
                                                <img class="h-10 w-10 rounded-full object-cover"
                                                    src="{{ Storage::url($visitante->user->foto) }}"
                                                    alt="{{ $visitante->user->name }}">
                                            @else
                                                <div
                                                    class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-400">
                                                {{ $visitante->user->name ?? 'N/A' }}
                                            </div>
                                            <div class="text-sm ">
                                                {{ $visitante->user->email ?? 'N/A' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-400">
                                        {{ $visitante->apartamento->numero ?? 'N/A' }}
                                    </div>
                                    <div class="text-xs ">
                                        {{ $visitante->apartamento->torre->nome ?? 'N/A' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm ">
                                    {{ $visitante->moradorResponsavel->user->name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm ">
                                    <div>
                                        <span class="font-semibold">Início:</span>
                                        {{ $visitante->data_validade_inicio ? date('d/m/Y', strtotime($visitante->data_validade_inicio)) : 'N/A' }}
                                    </div>
                                    <div>
                                        <span class="font-semibold">Fim:</span>
                                        {{ $visitante->data_validade_fim ? date('d/m/Y', strtotime($visitante->data_validade_fim)) : 'Indeterminado' }}
                                    </div>
                                    @if ($visitante->recorrente)
                                        <div class="mt-1 text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">
                                            Recorrente
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($visitante->ativo)
                                        @if ($visitante->podeEntrar())
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Permitido
                                            </span>
                                        @else
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                Fora do Período
                                            </span>
                                        @endif
                                    @else
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Inativo
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-2">
                                        <a href="{{ route('admin.visitantes.show', $visitante) }}"
                                            class="text-indigo-600 hover:text-indigo-900" title="Ver detalhes">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('admin.visitantes.edit', $visitante) }}"
                                            class="text-blue-600 hover:text-blue-900" title="Editar">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <form action="{{ route('admin.visitantes.destroy', $visitante) }}"
                                            method="POST" class="inline"
                                            onsubmit="return confirm('Tem certeza que deseja excluir este visitante?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900"
                                                title="Excluir">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm  text-center">
                                    Nenhum visitante cadastrado.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
    @if ($visitantes->hasPages())
        <div class="px-6 py-4 border-t">
            {{ $visitantes->links() }}
        </div>
    @endif

</x-layout.admin.app>
