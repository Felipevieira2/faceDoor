<x-layout.admin.app title="Novo Morador" class="overflow-y-auto max-h-[80vh]">
    <div class="flex items-center justify-between">
        <div class="mb-2"> 
            <h1 class="text-2xl font-bold ">Novo Morador</h1>
            <p class="mt-1 text-sm ">Preencha os dados para cadastrar um novo morador.</p>
        </div>
        <x-forms.button variant="primary" class="cursor-pointer" onclick="window.location.href='{{ route('admin.moradores.index') }}'">
            <i class="fas fa-arrow-left mr-2"></i> Voltar
        </x-forms.button>
    </div>

    <x-ui.card class="mt-6 ">
        <form action="{{ route('admin.moradores.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Seção de upload de foto -->
            <div class="mb-6 border-b pb-6 ">
                <h2 class="text-lg font-medium  mb-4">Dados Pessoais</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-center">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-4">
                        <div class="flex-shrink-0 mb-4 sm:mb-0">
                            <div id="foto-preview"
                                class="h-32 w-32 rounded-full bg-gray-200 flex items-center justify-center border border-gray-300 overflow-hidden">
                                @if (isset($imagemAtual))
                                    <img src="{{ $imagemAtual }}" alt="Foto do morador"
                                        class="h-full w-full object-cover">
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 " fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                @endif
                            </div>
                        </div>

                        <div class="flex-grow">
                            <label for="foto_morador"
                                class="inline-block px-4 py-2 bg-indigo-600 text-white rounded-md shadow-sm cursor-pointer hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                                <span class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Selecionar Foto
                                </span>
                            </label>
                            <input type="file" name="foto_morador" id="foto_morador" accept="image/*" class="hidden">
                            <p class="mt-2 text-xs ">Formatos aceitos: JPG, PNG, GIF. Tamanho máximo: 2MB</p>
                            <p id="foto-nome" class="mt-2 text-sm  hidden"></p>
                            @error('foto_morador')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-1 gap-4">
                        <x-forms.input name="nome" label="Nome Completo" :value="old('nome')" required
                        :error="$errors->first('nome')" />
                  
                        {{-- adicione um input que pergunta se o morador é um responsavel pelo apartamento --}}
                        <x-forms.checkbox name="is_responsavel" label="Responsável pelo apartamento" :value="true"
                            :checked="old('is_responsavel', false)" />
                       
                    </div>
                </div>

                <!-- Seção de Dados Pessoais -->
                <div class="mb-3 border-b pb-6">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                       
                        <x-forms.input name="cpf" label="CPF" :value="old('cpf')" required :error="$errors->first('cpf')"
                            id="cpf" placeholder="000.000.000-00" />
                        <x-forms.input name="email" type="email" label="E-mail" required :value="old('email')"
                            :error="$errors->first('email')" />
                        <x-forms.input name="telefone" label="Telefone" :value="old('telefone')" required :error="$errors->first('telefone')"
                            placeholder="(00) 00000-0000" id="telefone" />
                            <x-forms.input name="data_nascimento" label="Data de Nascimento" :value="old('data_nascimento')" required
                            :error="$errors->first('data_nascimento')" type="date" />
                    </div>
                </div>

                <!-- Seção de Localização -->
                <div class="mb-3">
                    <h2 class="text-lg font-medium  mb-4">Informações de Residência</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- <x-forms.select name="condominio_id" label="Condomínio" :value="old('condominio_id', request('condominio_id'))" required
                            :error="$errors->first('condominio_id')">
                            <option value="">Selecione um condomínio</option>
                            @foreach ($condominios as $condominio)
                                <option value="{{ $condominio->id }}"
                                    {{ old('condominio_id', request('condominio_id')) == $condominio->id ? 'selected' : '' }}>
                                    {{ $condominio->nome }}
                                </option>
                            @endforeach
                        </x-forms.select> --}}

                        <x-forms.select name="torre_id" label="Torre" :value="old('torre_id')" required :error="$errors->first('torre_id')">
                            <option value="">Selecione uma torre</option>
                            @foreach ($torres as $torre)
                                <option value="{{ $torre->id }}"
                                    {{ old('torre_id') == $torre->id ? 'selected' : '' }}>
                                    {{ $torre->nome }}
                                </option>
                            @endforeach
                        </x-forms.select>

                        <x-forms.input name="apartamento" label="Apartamento" :value="old('apartamento')" required
                            :error="$errors->first('apartamento')" placeholder="Ex: 101, 202, etc." />

                        <x-forms.select name="status" label="Status" :value="old('status', 'ativo')" required :error="$errors->first('status')">
                            <option value="ativo" {{ old('status', 'ativo') == 'ativo' ? 'selected' : '' }}>Ativo
                            </option>
                            <option value="inativo" {{ old('status') == 'inativo' ? 'selected' : '' }}>Inativo</option>
                        </x-forms.select>

                    </div>
                </div>

                <div class="mt-3 flex items-center justify-end">
                    <a href="{{ route('admin.moradores.index') }}"  class="mr-2">   
                        <x-forms.button type="button" variant="danger" >
                            Cancelar
                        </x-forms.button>
                    </a>
                    <x-forms.button type="submit" variant="primary">
                        Salvar Morador
                    </x-forms.button>
                </div>
        </form>
    </x-ui.card>

    @push('scripts')
        <script src="https://unpkg.com/imask@6.4.3/dist/imask.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Prévia da imagem
                const inputFoto = document.getElementById('foto_morador');
                const previewContainer = document.getElementById('foto-preview');
                const fotoNome = document.getElementById('foto-nome');

                if (inputFoto) {
                    inputFoto.addEventListener('change', function() {
                        if (this.files && this.files[0]) {
                            const reader = new FileReader();

                            reader.onload = function(e) {
                                // Atualiza a prévia
                                previewContainer.innerHTML =
                                    `<img src="${e.target.result}" alt="Prévia da foto" class="h-full w-full object-cover">`;

                                // Mostra o nome do arquivo
                                const fileName = inputFoto.files[0].name;
                                fotoNome.textContent = `Arquivo: ${fileName}`;
                                fotoNome.classList.remove('hidden');
                            }

                            reader.readAsDataURL(this.files[0]);
                        }
                    });
                }

                // Máscara para CPF: 000.000.000-00
                const cpfInput = document.getElementById('cpf');
                if (cpfInput) {
                    IMask(cpfInput, {
                        mask: '000.000.000-00'
                    });
                }

                // Máscara para telefone: (00) 00000-0000
                const telefoneInput = document.getElementById('telefone');
                if (telefoneInput) {
                    IMask(telefoneInput, {
                        mask: [{
                                mask: '(00) 00000-0000', // Celular com 9 dígitos
                            },
                            {
                                mask: '(00) 0000-0000', // Telefone fixo com 8 dígitos
                            }
                        ],
                        dispatch: function(appended, dynamicMasked) {
                            const number = (dynamicMasked.value + appended).replace(/\D/g, '');

                            // Verifica o tamanho do número para decidir qual máscara usar
                            if (number.length > 10) {
                                return dynamicMasked.compiledMasks[0]; // Celular
                            } else {
                                return dynamicMasked.compiledMasks[1]; // Fixo
                            }
                        }
                    });
                }
            });
        </script>
    @endpush
</x-layout.admin.app>
