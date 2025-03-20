<x-layout.admin.app>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Selecionar Condomínio') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('condominios.set-tenant') }}">
                        @csrf
                        <div class="mb-4">
                            <label for="condominio_id" class="block text-sm font-medium text-gray-700">Selecione o Condomínio</label>
                            <select name="condominio_id" id="condominio_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Selecione um condomínio</option>
                                @foreach($condominios as $condominio)
                                    <option value="{{ $condominio->id }}">{{ $condominio->nome }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-forms.button type="submit" variant="primary">
                                {{ __('Continuar') }}
                            </x-forms.button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layout.admin.app> 