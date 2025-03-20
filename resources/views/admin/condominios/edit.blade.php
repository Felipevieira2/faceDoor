<x-layout.admin.app title="Editar Condomínio">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Editar Condomínio</h1>
        <p class="mt-1 text-sm text-gray-600">Atualize os dados do condomínio {{ $condominio->nome }}.</p>
    </div>

    <x-ui.card>
        <form action="{{ route('admin.condominios.update', $condominio) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <x-forms.input 
                        name="nome" 
                        label="Nome do Condomínio" 
                        :value="old('nome', $condominio->nome)" 
                        required 
                        :error="$errors->first('nome')"
                    />

                    <x-forms.input 
                        name="endereco" 
                        label="Endereço" 
                        :value="old('endereco', $condominio->endereco)" 
                        required 
                        :error="$errors->first('endereco')"
                    />

                    <x-forms.input 
                        name="cep" 
                        label="CEP" 
                        :value="old('cep', $condominio->cep)" 
                        required 
                        :error="$errors->first('cep')"
                    />
                </div>

                <div>
                    <x-forms.input 
                        name="cidade" 
                        label="Cidade" 
                        :value="old('cidade', $condominio->cidade)" 
                        required 
                        :error="$errors->first('cidade')"
                    />

                    <x-forms.input 
                        name="estado" 
                        label="Estado" 
                        :value="old('estado', $condominio->estado)" 
                        required 
                        :error="$errors->first('estado')"
                    />

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-forms.input 
                            name="telefone" 
                            label="Telefone" 
                            :value="old('telefone', $condominio->telefone)" 
                            :error="$errors->first('telefone')"
                        />

                        <x-forms.input 
                            type="email"
                            name="email" 
                            label="E-mail" 
                            :value="old('email', $condominio->email)" 
                            :error="$errors->first('email')"
                        />
                    </div>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end">
                <a href="{{ route('admin.condominios.show', $condominio) }}" class="mr-2">
                    <x-forms.button type="button" variant="danger" >
                        Cancelar
                    </x-forms.button>
                </a>
                <x-forms.button type="submit" variant="primary">
                    Atualizar Condomínio
                </x-forms.button>
            </div>
        </form>
    </x-ui.card>
</x-layout.app> 