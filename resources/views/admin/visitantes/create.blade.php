<x-layout.admin.app title="Novo Visitante" class="overflow-y-auto max-h-[80vh]">
    <div class="flex items-center justify-between">
        <div class="mb-2">
            <h1 class="text-2xl font-bold">Novo Visitante</h1>
            <p class="mt-1 text-sm">Preencha os dados para cadastrar um novo visitante.</p>
        </div>
        <x-forms.button variant="primary" class="cursor-pointer" onclick="window.location.href='{{ route('admin.visitantes.index') }}'">
            <i class="fas fa-arrow-left mr-2"></i> Voltar
        </x-forms.button>
    </div>

    </div>

    <x-ui.card class="mt-6">
        <form action="{{ route('admin.visitantes.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Seção de Dados Pessoais -->
            <div class="mb-6 border-b pb-6">
                <h2 class="text-lg font-medium mb-4">Dados Pessoais</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-center">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-4">
                        <div class="flex-shrink-0 mb-4 sm:mb-0">
                            <div id="foto-preview"
                                class="h-32 w-32 rounded-full bg-gray-200 flex items-center justify-center border border-gray-300 overflow-hidden">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                        </div>

                        <div class="flex-grow">
                            <label for="foto_visitante"
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
                            <input type="file" name="foto_visitante" id="foto_visitante" accept="image/*" class="hidden">
                            <p class="mt-2 text-xs">Formatos aceitos: JPG, PNG, GIF. Tamanho máximo: 2MB</p>
                            <p id="foto-nome" class="mt-2 text-sm hidden"></p>
                            @error('foto_visitante')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-1 gap-4">
                        <x-forms.input name="nome" label="Nome Completo" :value="old('nome')" required
                            :error="$errors->first('nome')" />
                        
                        <x-forms.input name="cpf" label="CPF" :value="old('cpf')" required :error="$errors->first('cpf')"
                            id="cpf" placeholder="000.000.000-00" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <x-forms.input name="email" type="email" label="E-mail" :value="old('email')"
                        :error="$errors->first('email')" />
                    <x-forms.input name="telefone" label="Telefone" :value="old('telefone')" :error="$errors->first('telefone')"
                        placeholder="(00) 00000-0000" id="telefone" />
                    <x-forms.input name="data_nascimento" label="Data de Nascimento" :value="old('data_nascimento')"
                        :error="$errors->first('data_nascimento')" type="date" />
                </div>
            </div>

            <!-- Seção de Local de Visita -->
            <div class="mb-6 border-b pb-6">
                <h2 class="text-lg font-medium mb-4">Local de Visita</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    

                
                    <x-forms.select name="morador_responsavel_id" label="Visitar o Morador" :value="old('morador_responsavel_id')" required :error="$errors->first('morador_responsavel_id')">
                        <option value="">Selecione um morador </option>
                        @foreach ($moradores as $morador)
                            <option value="{{ $morador->id }}"
                                {{ old('morador_responsavel_id') == $morador->id ? 'selected' : '' }}>
                                {{ $morador->user->name ?? 'Morador '.$morador->id }} (Apto {{ $morador->apartamento->numero ?? 'N/A' }}) ( {{ $morador->apartamento->torre->nome ?? 'N/A' }})
                            </option>
                        @endforeach
                    </x-forms.select>
                </div>
            </div>

            <!-- Seção de Permissão de Acesso -->
            <div class="mb-6">
                <h2 class="text-lg font-medium mb-4">Permissão de Acesso</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4" x-data="{ recorrente: {fase} }">
                    <x-forms.input name="data_validade_inicio" label="Data de Início" :value="old('data_validade_inicio')" required
                        :error="$errors->first('data_validade_inicio')" type="date" />
                    
                    <x-forms.input name="data_validade_fim" label="Data de Término (opcional)" :value="old('data_validade_fim')"
                        :error="$errors->first('data_validade_fim')" type="date" />

                    <div class="md:col-span-2">
                        <x-forms.checkbox name="recorrente" label="Visita Recorrente" xmodel="recorrente" :value="true"
                            :checked="old('recorrente', false)" id="recorrente" />
                    </div>

                    <div id="recorrencia-campos" class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4 p-4 border rounded-md {{ old('recorrente') ? '' : 'hidden' }}">
                        <div>
                            <label class="block text-sm font-medium  mb-2">Dias da Semana</label>
                            <div class="space-y-2">
                                <div>
                                    <input type="checkbox" name="dias_semana[]" value="0" id="dia-0" {{ is_array(old('dias_semana')) && in_array('0', old('dias_semana')) ? 'checked' : '' }}>
                                    <label for="dia-0" class="ml-2 text-sm ">Domingo</label>
                                </div>
                                <div>
                                    <input type="checkbox" name="dias_semana[]" value="1" id="dia-1" {{ is_array(old('dias_semana')) && in_array('1', old('dias_semana')) ? 'checked' : '' }}>
                                    <label for="dia-1" class="ml-2 text-sm ">Segunda-feira</label>
                                </div>
                                <div>
                                    <input type="checkbox" name="dias_semana[]" value="2" id="dia-2" {{ is_array(old('dias_semana')) && in_array('2', old('dias_semana')) ? 'checked' : '' }}>
                                    <label for="dia-2" class="ml-2 text-sm ">Terça-feira</label>
                                </div>
                                <div>
                                    <input type="checkbox" name="dias_semana[]" value="3" id="dia-3" {{ is_array(old('dias_semana')) && in_array('3', old('dias_semana')) ? 'checked' : '' }}>
                                    <label for="dia-3" class="ml-2 text-sm ">Quarta-feira</label>
                                </div>
                                <div>
                                    <input type="checkbox" name="dias_semana[]" value="4" id="dia-4" {{ is_array(old('dias_semana')) && in_array('4', old('dias_semana')) ? 'checked' : '' }}>
                                    <label for="dia-4" class="ml-2 text-sm ">Quinta-feira</label>
                                </div>
                                <div>
                                    <input type="checkbox" name="dias_semana[]" value="5" id="dia-5" {{ is_array(old('dias_semana')) && in_array('5', old('dias_semana')) ? 'checked' : '' }}>
                                    <label for="dia-5" class="ml-2 text-sm ">Sexta-feira</label>
                                </div>
                                <div>
                                    <input type="checkbox" name="dias_semana[]" value="6" id="dia-6" {{ is_array(old('dias_semana')) && in_array('6', old('dias_semana')) ? 'checked' : '' }}>
                                    <label for="dia-6" class="ml-2 text-sm ">Sábado</label>
                                </div>
                            </div>
                            @error('dias_semana')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <x-forms.input name="horario_inicio" label="Horário de Início" xbindrequired="recorrente == true" :value="old('horario_inicio')"
                                :error="$errors->first('horario_inicio')" type="time" />
                            
                            <x-forms.input name="horario_fim" label="Horário de Término" xbindrequired="recorrente == true" :value="old('horario_fim')"
                                :error="$errors->first('horario_fim')" type="time" class="mt-4" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-3 flex items-center justify-end">
                <a href="{{ route('admin.visitantes.index') }}" class="mr-2">
                    <x-forms.button type="button" variant="danger">
                        Cancelar
                    </x-forms.button>
                </a>
                <x-forms.button type="submit" variant="primary">
                    Salvar Visitante
                </x-forms.button>
            </div>
        </form>
    </x-ui.card>

    @push('scripts')
        <script src="https://unpkg.com/imask@6.4.3/dist/imask.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Prévia da imagem
                const inputFoto = document.getElementById('foto_visitante');
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

                // Função para alternar visibilidade dos campos de recorrência
                function toggleRecorrencia() {
                    const recorrenteCheck = document.getElementById('recorrente');
                    const camposRecorrencia = document.getElementById('recorrencia-campos');
                    
                    if (recorrenteCheck.checked) {
                        camposRecorrencia.classList.remove('hidden');
                    } else {
                        camposRecorrencia.classList.add('hidden');
                    }
                }
                
                // Executar na inicialização
                toggleRecorrencia();
                
                // Adicionar evento de alteração
                document.getElementById('recorrente').addEventListener('change', toggleRecorrencia);
            });
        </script>
    @endpush
</x-layout.admin.app> 