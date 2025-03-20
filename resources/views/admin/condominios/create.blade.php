<x-layout.admin.app title="Novo Condomínio">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Novo Condomínio</h1>
        <p class="mt-1 text-sm text-gray-600">Preencha os dados para cadastrar um novo condomínio.</p>
    </div>

    <x-ui.card>
        <form action="{{ route('admin.condominios.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <x-forms.input 
                        name="nome" 
                        label="Nome do Condomínio" 
                        :value="old('nome')" 
                        required 
                        :error="$errors->first('nome')"
                    />

                    <x-forms.input 
                        name="endereco" 
                        label="Endereço" 
                        :value="old('endereco')" 
                        required 
                        :error="$errors->first('endereco')"
                    />

                    <x-forms.input 
                        name="cep" 
                        label="CEP" 
                        :value="old('cep')" 
                        required 
                        :error="$errors->first('cep')"
                    />
                </div>

                <div>
                    <x-forms.input  
                        name="cidade" 
                        label="Cidade" 
                        :value="old('cidade')" 
                        required 
                        :error="$errors->first('cidade')"
                    />

                    <x-forms.input 
                        name="estado" 
                        label="Estado" 
                        :value="old('estado')" 
                        required 
                        :error="$errors->first('estado')"
                    />

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-forms.input 
                            name="telefone" 
                            label="Telefone" 
                            :value="old('telefone')" 
                            :error="$errors->first('telefone')"
                        />

                        <x-forms.input 
                            type="email"
                            name="email" 
                            label="E-mail" 
                            :value="old('email')" 
                            :error="$errors->first('email')"
                        />
                    </div>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end">
                <a href="{{ route('admin.condominios.index') }}"   class="mr-2">
                    <x-forms.button type="button" variant="danger" >
                        Cancelar
                    </x-forms.button>
                </a>
                <x-forms.button type="submit" variant="primary">
                    Salvar Condomínio
                </x-forms.button>
            </div>
        </form>
    </x-ui.card>
</x-layout.app> 