<x-layout.admin.app title="Dashboard">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Painel do Morador</h1>
        <p class="mt-1 text-sm text-gray-600">Bem-vindo ao sistema de gestão de acesso de condomínio.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <x-ui.card title="Seu Apartamento" class="bg-gradient-to-br from-blue-50 to-blue-100">
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-500">Condomínio:</span>
                    <span class="text-sm text-gray-900">{{ $apartamentos->torre->condominio->nome }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-500">Torre:</span>
                    <span class="text-sm text-gray-900">{{ $apartamentos->torre->nome }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-500">Apartamento:</span>
                    <span class="text-sm text-gray-900">{{ $apartamentos->numero }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-500">Andar:</span>
                    <span class="text-sm text-gray-900">{{ $apartamentos->andar }}</span>
                </div>
                @if($apartamentos->bloco)
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-500">Bloco:</span>
                    <span class="text-sm text-gray-900">{{ $apartamentos->bloco }}</span>
                </div>
                @endif
            </div>
        </x-ui.card>

        <x-ui.card title="Visitantes" class="bg-gradient-to-br from-green-50 to-green-100">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                    <svg class="h-8 w-8 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div class="ml-5">
                    <div class="text-3xl font-semibold text-gray-900">{{ $visitantes }}</div>
                    <div class="text-sm text-gray-600">Total de visitantes</div>
                </div>
            </div>
            <div class="mt-2">
                <div class="flex items-center">
                    <x-ui.badge variant="success" size="sm">{{ $visitantesAtivos }}</x-ui.badge>
                    <span class="ml-2 text-xs text-gray-600">Visitantes ativos</span>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('visitantes.index') }}" class="text-green-600 hover:text-green-800 text-sm font-medium">
                    Ver todos os visitantes →
                </a>
            </div>
        </x-ui.card>

        <x-ui.card title="Ações Rápidas" class="bg-gradient-to-br from-purple-50 to-purple-100">
            <div class="space-y-3">
                <a href="{{ route('visitantes.create') }}" class="block px-4 py-2 bg-white rounded-md shadow-sm hover:bg-gray-50 transition-colors duration-150">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 text-purple-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        <span class="ml-2 text-sm font-medium text-gray-700">Adicionar Visitante</span>
                    </div>
                </a>
            </div>
        </x-ui.card>
    </div>
</x-layout.app> 