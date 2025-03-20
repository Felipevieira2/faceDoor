<x-layout.admin.app title="Condomínios">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Condomínios</h1>
            <p class="mt-1 text-sm text-gray-600">Gerencie os condomínios cadastrados no sistema.</p>
        </div>
        <div>
            <a href="{{ route('admin.condominios.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-200 active:bg-blue-700 transition">
                <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Novo Condomínio
            </a>
        </div>
    </div>

    <x-ui.card>
        @if($condominios->isEmpty())
            <div class="text-center py-8">
                <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum condomínio encontrado</h3>
                <p class="mt-1 text-sm text-gray-500">Comece cadastrando um novo condomínio.</p>
                <div class="mt-6">
                    <a href="{{ route('admin.condominios.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Novo Condomínio
                    </a>
                </div>
            </div>
        @else
            <x-ui.table :headers="['Nome', 'Endereço', 'Cidade/Estado', 'Torres', 'Dispositivos']">
                @foreach($condominios as $condominio)
                    <x-ui.table-row>
                        <x-ui.table-cell class="font-medium text-gray-900">
                            {{ $condominio->nome }}
                        </x-ui.table-cell>
                        <x-ui.table-cell>
                            {{ $condominio->endereco }}, CEP {{ $condominio->cep }}
                        </x-ui.table-cell>
                        <x-ui.table-cell>
                            {{ $condominio->cidade }}/{{ $condominio->estado }}
                        </x-ui.table-cell>
                        <x-ui.table-cell>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ $condominio->torres->count() }}
                            </span>
                        </x-ui.table-cell>
                        <x-ui.table-cell>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                {{ $condominio->dispositivos->count() }}
                            </span>
                        </x-ui.table-cell>
                        <x-slot name="actions">
                            <a href="{{ route('admin.condominios.show', $condominio) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                Ver
                            </a>
                            <a href="{{ route('admin.condominios.edit', $condominio) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                Editar
                            </a>
                            <form method="POST" action="{{ route('admin.condominios.destroy', $condominio) }}" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Tem certeza que deseja excluir este condomínio?')">
                                    Excluir
                                </button>
                            </form>
                        </x-slot>
                    </x-ui.table-row>
                @endforeach
            </x-ui.table>

            <div class="mt-4">
                {{ $condominios->links() }}
            </div>
        @endif
    </x-ui.card>
</x-layout.app> 