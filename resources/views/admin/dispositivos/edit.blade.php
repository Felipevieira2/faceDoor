<x-layout.admin.app title="Editar Dispositivo">
    <div class="mb-6">
        <h1 class="text-2xl font-bold">Editar Dispositivo</h1>
        <p class="mt-1 text-sm">Atualize os dados do dispositivo {{ $dispositivo->nome }}.</p>
    </div>

    <x-ui.card>
        <form action="{{ route('admin.dispositivos.update', $dispositivo) }}" method="POST" class="flex flex-col gap-4 items-center">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 px-5 w-3xl" x-data="{ fabricante: '{{ old('fabricante', $dispositivo->fabricante) }}', tipo: '{{ old('tipo', $dispositivo->tipo) }}' }">

                <x-forms.input name="nome" label="Nome do Dispositivo" :value="old('nome', $dispositivo->nome)" required :error="$errors->first('nome')" />

                <x-forms.select xmodel="tipo" name="tipo" label="Tipo de Dispositivo" :value="old('tipo', $dispositivo->tipo)" required :error="$errors->first('tipo')">
                    <option value="">Selecione um tipo</option>
                    <option value="entrada_condominio" {{ old('tipo', $dispositivo->tipo) == 'entrada_condominio' ? 'selected' : '' }}>
                        Entrada do Condomínio</option>
                    <option value="saida_condominio" {{ old('tipo', $dispositivo->tipo) == 'saida_condominio' ? 'selected' : '' }}>Saída do
                        Condomínio</option>
                    <option value="torre" {{ old('tipo', $dispositivo->tipo) == 'torre' ? 'selected' : '' }}>Torre</option>
                </x-forms.select>

                <x-forms.input name="identificador_unico" label="Identificador Único" :value="old('identificador_unico', $dispositivo->identificador_unico)" required
                    :error="$errors->first('identificador_unico')" placeholder="Ex: Serial, etc." />

                <x-forms.select name="condominio_id" label="Condomínio" :value="old('condominio_id', $dispositivo->condominio_id)" required :error="$errors->first('condominio_id')">
                    <option value="">Selecione um condomínio</option>
                    @foreach ($condominios as $condominio)
                        <option value="{{ $condominio->id }}"
                            {{ old('condominio_id', $dispositivo->condominio_id) == $condominio->id ? 'selected' : '' }}>
                            {{ $condominio->nome }}
                        </option>
                    @endforeach
                </x-forms.select>

                <x-forms.input name="localizacao" label="Localização" :value="old('localizacao', $dispositivo->localizacao)" required :error="$errors->first('localizacao')" />

                <x-forms.select name="ativo" label="Status" :value="old('ativo', $dispositivo->ativo)" required :error="$errors->first('ativo')">
                    <option value="1" {{ old('ativo', $dispositivo->ativo) == '1' ? 'selected' : '' }}>Ativo</option>
                    <option value="0" {{ old('ativo', $dispositivo->ativo) == '0' ? 'selected' : '' }}>Inativo</option>
                </x-forms.select>

                <x-forms.select name="fabricante" label="Fabricante" :value="old('fabricante', $dispositivo->fabricante)" required :error="$errors->first('fabricante')"
                    xmodel="fabricante">
                    <option value="">Selecione um fabricante</option>
                    <option value="intelbras" {{ old('fabricante', $dispositivo->fabricante) == 'intelbras' ? 'selected' : '' }}>Intelbras
                    </option>
                    <option value="controlid" {{ old('fabricante', $dispositivo->fabricante) == 'controlid' ? 'selected' : '' }}>ControlID
                    </option>
                </x-forms.select>

                <div x-show="fabricante === 'intelbras'">
                    <x-forms.input name="username" label="Nome de Usuário" :value="old('username', $dispositivo->username)" 
                        :required="old('fabricante', $dispositivo->fabricante) == 'intelbras'"
                        :error="$errors->first('username')" :autocomplete="'new-password'" />
                </div>

                <div x-show="fabricante === 'intelbras'">
                    <x-forms.input name="password" label="Senha" type="password" :value="old('password')" 
                        :error="$errors->first('password')" :autocomplete="'new-password'" 
                        placeholder="Deixe em branco para manter a senha atual" />
                </div>

                <div x-show="fabricante === 'intelbras'">
                    <x-forms.input name="ip" label="Endereço IP" :value="old('ip', $dispositivo->ip)" 
                        :required="old('fabricante', $dispositivo->fabricante) == 'intelbras'"
                        :error="$errors->first('ip')" />
                </div>
            </div>

            <div x-data="{ tipo: '{{ old('tipo', $dispositivo->tipo) }}' }" x-init="$watch('tipo', value => { console.log(value) })">
                <div x-show="tipo === 'torre'">
                    <x-forms.select name="torre_id" label="Torre" :value="old('torre_id', $dispositivo->torre_id)" :error="$errors->first('torre_id')">
                        <option value="">Selecione uma torre</option>
                        @foreach ($torres as $torre)
                            <option value="{{ $torre->id }}" {{ old('torre_id', $dispositivo->torre_id) == $torre->id ? 'selected' : '' }}>
                                {{ $torre->nome }} ({{ $torre->condominio->nome }})
                            </option>
                        @endforeach
                    </x-forms.select>
                </div>
            </div>

            <div x-show="tipo === 'torre'" class="mt-4">
                <x-forms.select name="torre_id" label="Torre" :value="old('torre_id', $dispositivo->torre_id)" :error="$errors->first('torre_id')">
                    <option value="">Selecione uma torre</option>
                    @foreach ($torres as $torre)
                        <option value="{{ $torre->id }}" {{ old('torre_id', $dispositivo->torre_id) == $torre->id ? 'selected' : '' }}>
                            {{ $torre->nome }} ({{ $torre->condominio->nome }})
                        </option>
                    @endforeach
                </x-forms.select>
            </div>

            <div class="mt-6 flex items-center justify-end m-5 self-end">
                <a href="{{ route('admin.dispositivos.show', $dispositivo) }}" class="mr-2">
                    <x-forms.button type="button" variant="danger">
                        Cancelar
                    </x-forms.button>
                </a>
                <x-forms.button type="submit" variant="primary">
                    Atualizar Dispositivo
                </x-forms.button>
            </div>
        </form>
    </x-ui.card>
</x-layout.admin.app> 