<x-layout.admin.app title="Detalhes do Morador">
    <div class="sm:flex sm:items-center sm:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Detalhes do Morador</h1>
            <p class="mt-1 text-sm text-gray-600">Informações completas do morador</p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <a href="{{ route('admin.moradores.edit', $morador) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Editar
            </a>
            <form action="{{ route('admin.moradores.destroy', $morador) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500" onclick="return confirm('Tem certeza que deseja excluir este morador?')">
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Excluir
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <x-ui.card>
            <h2 class="text-lg font-medium text-gray-900 mb-4">Informações Pessoais</h2>
            
            <div class="space-y-4">
                <div>
                    <p class="text-sm font-medium text-gray-500">Nome Completo</p>
                    <p class="mt-1 text-base text-gray-900">{{ $morador->nome }}</p>
                </div>
                
                <div>
                    <p class="text-sm font-medium text-gray-500">CPF</p>
                    <p class="mt-1 text-base text-gray-900">{{ $morador->cpf }}</p>
                </div>
                
                <div>
                    <p class="text-sm font-medium text-gray-500">E-mail</p>
                    <p class="mt-1 text-base text-gray-900">{{ $morador->email }}</p>
                </div>
                
                <div>
                    <p class="text-sm font-medium text-gray-500">Telefone</p>
                    <p class="mt-1 text-base text-gray-900">{{ $morador->telefone }}</p>
                </div>
                
                <div>
                    <p class="text-sm font-medium text-gray-500">Status</p>
                    <p class="mt-1">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $morador->status === 'ativo' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $morador->status === 'ativo' ? 'Ativo' : 'Inativo' }}
                        </span>
                    </p>
                </div>
            </div>
        </x-ui.card>
        
        <x-ui.card>
            <h2 class="text-lg font-medium text-gray-900 mb-4">Informações Residenciais</h2>
            
            <div class="space-y-4">
                <div>
                    <p class="text-sm font-medium text-gray-500">Condomínio</p>
                    <p class="mt-1 text-base text-gray-900">{{ $morador->condominio->nome }}</p>
                </div>
                
                <div>
                    <p class="text-sm font-medium text-gray-500">Torre</p>
                    <p class="mt-1 text-base text-gray-900">{{ $morador->torre->nome }}</p>
                </div>
                
                <div>
                    <p class="text-sm font-medium text-gray-500">Unidade/Apartamento</p>
                    <p class="mt-1 text-base text-gray-900">{{ $morador->unidade }}</p>
                </div>
                
                <div>
                    <p class="text-sm font-medium text-gray-500">Data de Cadastro</p>
                    <p class="mt-1 text-base text-gray-900">{{ $morador->created_at->format('d/m/Y H:i') }}</p>
                </div>
                
                <div>
                    <p class="text-sm font-medium text-gray-500">Última Atualização</p>
                    <p class="mt-1 text-base text-gray-900">{{ $morador->updated_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </x-ui.card>
    </div>

    <div class="mt-6">
        <a href="{{ route('admin.moradores.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-500">
            &larr; Voltar para lista de moradores
        </a>
    </div>
</x-layout.admin.app> 