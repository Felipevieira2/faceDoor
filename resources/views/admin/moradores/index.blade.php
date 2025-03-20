<x-layout.admin.app title="Moradores">
    <div class="flex items-center justify-between mb-6 border-b border-gray-200 dark:border-gray-700 pb-3">

        <h1 class="text-2xl font-bold ">Gerenciar Moradores</h1>


        <a href="{{ route('admin.moradores.create') }}" class="mt-2">
            <x-forms.button type="submit" variant="primary" class="cursor-pointer">
                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg> Novo Morador
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
    <div class="flex flex-col">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800 rounded-lg overflow-hidden">
                <thead class="bg-gray-150 dark:bg-blue-600 rounded-t-lg shadow-md">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Foto
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Nome
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            CPF
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Condomínio/Unidade
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Ações
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-gray-50 dark:bg-gray-800 dark:divide-gray-700 rounded-b-lg">
                    @forelse($moradores as $morador)
                        <tr class="hover:bg-gray-300 dark:hover:bg-gray-900">
                            <td class="px-4 py-3">
                                <img src="{{ $morador->foto ? asset('storage/' . $morador->foto) : asset('images/default-user.png') }}"
                                    alt="Foto do morador"
                                    class="h-12 w-12 rounded-full cursor-pointer hover:opacity-75 transition-opacity"
                                    onclick="abrirGaleria(this.src)">


                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium  dark:text-white">{{ $morador->name }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $morador->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm  dark:text-white">
                                {{ $morador->cpf }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm  dark:text-white">{{ $morador->condominio_nome }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $morador->torre_nome }} -
                                    Unidade {{ $morador->apartamento_numero }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($morador->morador_status == 1)
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-white">
                                        Ativo
                                    </span>
                                @else
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200">
                                        Inativo
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('admin.moradores.edit', $morador->id) }}"
                                    class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 mr-3 cursor-pointer"
                                    title="Editar Morador">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <a href="{{ route('admin.moradores.show', $morador->id) }}"
                                    class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 cursor-pointer"
                                    title="Ver Detalhes">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5"
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">
                                Nenhum morador encontrado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            @if (method_exists($moradores, 'links'))
                {{ $moradores->links() }}
            @endif
        </div>

        <!-- Modal da Galeria -->
        <div id="galeriaModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
            <div class="relative">
                <img id="imagemAmpliada" src="" alt="Imagem Ampliada"
                    class="max-h-[80vh] max-w-[90vw] rounded-lg">
                <button onclick="fecharGaleria()" class="absolute top-4 right-4 text-white hover:text-gray-300">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
        </div>


    </div>

    @push('scripts')
        <script>
            function abrirGaleria(src) {
                const modal = document.getElementById('galeriaModal');
                const imagem = document.getElementById('imagemAmpliada');
                imagem.src = src;
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }

            function fecharGaleria() {
                const modal = document.getElementById('galeriaModal');
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        </script>
    @endpush
</x-layout.admin.app>
