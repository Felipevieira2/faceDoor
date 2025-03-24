<x-layout.admin.app title="Novo Dispositivo">
    <div class="flex items-center justify-between">
        <div class="mb-6">
            <h1 class="text-2xl font-bold">Novo Dispositivo</h1>
            <p class="mt-1 text-sm">Preencha os dados para cadastrar um novo dispositivo.</p>
        </div>
        <x-forms.button variant="primary" class="cursor-pointer"
            onclick="window.location.href='{{ route('admin.moradores.index') }}'">
            <i class="fas fa-arrow-left mr-2"></i> Voltar
        </x-forms.button>
    </div>

    <x-ui.card>
        <form action="{{ route('admin.dispositivos.store') }}" method="POST" class="flex flex-col gap-4 items-center">
            @csrf

            <div class="w-full px-5">
                <!-- Seção: Informações Básicas -->
                <div class="mb-6">
                    <h2 class="text-lg font-semibold mb-3 border-b pb-2">Informações Básicas</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4" x-data="{ fabricante: '{{ old('fabricante') ?? '' }}' }">
                        <x-forms.input name="nome" label="Nome do Dispositivo" :value="old('nome')" required
                            :error="$errors->first('nome')" />

                        <x-forms.input name="identificador_unico" label="Identificador Único" :value="old('identificador_unico')" required
                            :error="$errors->first('identificador_unico')" placeholder="Ex: Serial, etc." />



                        <x-forms.select name="ativo" label="Status" :value="old('ativo', '1')" required :error="$errors->first('ativo')">
                            <option value="1" {{ old('ativo', '1') == '1' ? 'selected' : '' }}>Ativo</option>
                            <option value="0" {{ old('ativo') == '0' ? 'selected' : '' }}>Inativo</option>
                        </x-forms.select>
                    </div>
                </div>

                <!-- Seção: Localização -->
                <div class="mb-6">
                    <h2 class="text-lg font-semibold mb-3 border-b pb-2">Localização</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4" x-data="{ localizacao: '{{ old('localizacao') ?? '' }}' }">
                        {{-- <x-forms.select  name="condominio_id" label="Condomínio" :value="old('condominio_id', request('condominio_id'))" required
                            :error="$errors->first('condominio_id')">
                            <option value="">Selecione um condomínio</option>
                            @foreach ($condominios as $condominio)
                                <option value="{{ $condominio->id }}"
                                    {{ old('condominio_id', request('condominio_id')) == $condominio->id ? 'selected' : '' }}>
                                    {{ $condominio->nome }}
                                </option>
                            @endforeach
                        </x-forms.select>                         --}}

                        <x-forms.select xmodel="localizacao" name="localizacao" label="Localização do Dispositivo"
                            :value="old('localizacao')" required :error="$errors->first('localizacao')">
                            <option value="">Selecione a Localização</option>
                            <option value="entrada_condominio"
                                {{ old('localizacao') == 'entrada_condominio' ? 'selected' : '' }}>
                                Entrada do Condomínio</option>
                            <option value="saida_condominio"
                                {{ old('localizacao') == 'saida_condominio' ? 'selected' : '' }}>
                                Saída do
                                Condomínio</option>
                            <option value="torre" {{ old('localizacao') == 'torre' ? 'selected' : '' }}>Torre</option>
                        </x-forms.select>

                        <div x-show="localizacao === 'torre'" class="col-span-2 md:col-span-1">
                            <x-forms.select xbindrequired="localizacao === 'torre'" name="torre_id" label="Torre"
                                :value="old('torre_id')" :error="$errors->first('torre_id')">
                                <option value="">Selecione uma torre</option>
                                @foreach ($torres as $torre)
                                    <option value="{{ $torre->id }}"
                                        {{ old('torre_id') == $torre->id ? 'selected' : '' }}>
                                        {{ $torre->nome }} ({{ $torre->condominio->nome }})
                                    </option>
                                @endforeach
                            </x-forms.select>
                        </div>
                    </div>
                </div>

                <!-- Seção: Configurações do Fabricante -->
                <div class="mb-6">
                    <h2 class="text-lg font-semibold mb-3 border-b pb-2">Configurações do Fabricante</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4" x-data="{
                        fabricante: '{{ old('fabricante') ?? '' }}',
                        isIntelbras: false
                    }" x-init="console.log('Inicializado com isIntelbras:', isIntelbras)"
                        x-effect="console.log('isIntelbras atualizado:', isIntelbras)">
                        <x-forms.select name="fabricante" label="Fabricante" :value="old('fabricante')" required
                            :error="$errors->first('fabricante')" xmodel="fabricante">
                            <option value="">Selecione um fabricante</option>
                            <option value="intelbras" {{ old('fabricante') == 'intelbras' ? 'selected' : '' }}>
                                Intelbras
                            </option>
                            <option value="controlid" {{ old('fabricante') == 'controlid' ? 'selected' : '' }}>
                                ControlID
                            </option>
                        </x-forms.select>

                        <div x-show="fabricante === 'intelbras'"
                            class="grid grid-cols-1 md:grid-cols-2 gap-4 col-span-2 mt-2">
                            <x-forms.input name="username" label="Nome de Usuário" :value="old('username')"
                                xbindrequired="fabricante === 'intelbras'" :error="$errors->first('username')" :autocomplete="'new-password'" />

                            <x-forms.input name="password" label="Senha" type="password" :value="old('password')"
                                xbindrequired="fabricante === 'intelbras'" :error="$errors->first('password')" :autocomplete="'new-password'" />

                            {{-- if fabricante is intelbras then set atribute  required --}}

                            <x-forms.input name="ip" label="Endereço IP" :value="old('ip')"
                                xbindrequired="fabricante === 'intelbras'" :error="$errors->first('ip')" />

                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end m-5 self-end">
                <a href="{{ route('admin.dispositivos.index') }}" class="mr-2">
                    <x-forms.button type="button" variant="danger">
                        Cancelar
                    </x-forms.button>
                </a>
                <x-forms.button type="submit" variant="primary">
                    Salvar Dispositivo
                </x-forms.button>
            </div>
        </form>
    </x-ui.card>
</x-layout.admin.app>
