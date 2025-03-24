<x-layout.admin.app title="Nova Torre" class="overflow-y-auto max-h-[80vh]">
    <div class="mb-2">
        <h1 class="text-2xl font-bold ">Nova Torre</h1>
        <p class="mt-1 text-sm ">Preencha os dados para cadastrar uma nova torre.</p>
    </div>

    <x-ui.card class="mt-6 ">
        <form action="{{ route('admin.torres.store') }}" method="POST">
            @csrf

            <!-- Seção de Informações Básicas -->
            <div class="mb-6 border-b pb-6 ">
                <h2 class="text-lg font-medium mb-4">Informações Básicas</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-forms.input name="nome" label="Nome da Torre" :value="old('nome')" required
                        :error="$errors->first('nome')" placeholder="Ex: Torre A, Bloco 1, etc." />
                    
                    <x-forms.select name="condominio_id" label="Condomínio" :value="old('condominio_id')" required
                        :error="$errors->first('condominio_id')">
                        <option value="">Selecione um condomínio</option>
                        @foreach ($condominios as $condominio)
                            <option value="{{ $condominio->id }}"
                                {{ old('condominio_id') == $condominio->id ? 'selected' : '' }}>
                                {{ $condominio->nome }}
                            </option>
                        @endforeach
                    </x-forms.select>
                    
                    <x-forms.input name="numero_andares" type="number" label="Número de Andares" :value="old('numero_andares')" 
                        :error="$errors->first('numero_andares')" placeholder="Ex: 10" min="1" />
                    
                    <x-forms.textarea name="descricao" label="Descrição" :value="old('descricao')"
                        :error="$errors->first('descricao')" placeholder="Informações adicionais sobre a torre" />
                </div>
            </div>

            <div class="mt-3 flex items-center justify-end">
                <a href="{{ route('admin.torres.index') }}" class="mr-2">
                    <x-forms.button type="button" variant="danger">
                        Cancelar
                    </x-forms.button>
                </a>
                <x-forms.button type="submit" variant="primary">
                    Salvar Torre
                </x-forms.button>
            </div>
        </form>
    </x-ui.card>
</x-layout.admin.app> 