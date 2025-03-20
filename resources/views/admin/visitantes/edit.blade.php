<x-layout.admin.app title="Editar Visitante">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Editar Visitante</h1>
        <p class="mt-1 text-sm text-gray-600">Atualize os dados do visitante {{ $visitante->user->name }}.</p>
    </div>

    <x-ui.card>
        <form action="{{ route('visitantes.update', $visitante) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Dados Pessoais</h2>
                    
                    <x-forms.input 
                        name="nome" 
                        label="Nome Completo" 
                        :value="old('nome', $visitante->user->name)" 
                        required 
                        :error="$errors->first('nome')"
                    />

                    <x-forms.input 
                        type="email"
                        name="email" 
                        label="E-mail" 
                        :value="old('email', $visitante->user->email)" 
                        required 
                        :error="$errors->first('email')"
                    />

                    <x-forms.input 
                        name="telefone" 
                        label="Telefone" 
                        :value="old('telefone', $visitante->telefone)" 
                        :error="$errors->first('telefone')"
                    />

                    <x-forms.input 
                        name="documento" 
                        label="Documento (CPF/RG)" 
                        :value="old('documento', $visitante->documento)" 
                        required 
                        :error="$errors->first('documento')"
                    />
                </div>

                <div>
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Dados da Visita</h2>

                    <div x-data="{ recorrente: {{ old('recorrente', $visitante->recorrente) ? 'true' : 'false' }} }">
                        <div class="mb-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="recorrente" value="1" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" x-model="recorrente" {{ old('recorrente', $visitante->recorrente) ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-gray-600">Visitante recorrente</span>
                            </label>
                        </div>

                        <div x-show="!recorrente">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <x-forms.input 
                                    type="date"
                                    name="data_validade_inicio" 
                                    label="Data de Início" 
                                    :value="old('data_validade_inicio', $visitante->data_validade_inicio ? \Carbon\Carbon::parse($visitante->data_validade_inicio)->format('Y-m-d') : '')" 
                                    required 
                                    :error="$errors->first('data_validade_inicio')"
                                />

                                <x-forms.input 
                                    type="date"
                                    name="data_validade_fim" 
                                    label="Data de Término" 
                                    :value="old('data_validade_fim', $visitante->data_validade_fim ? \Carbon\Carbon::parse($visitante->data_validade_fim)->format('Y-m-d') : '')" 
                                    :error="$errors->first('data_validade_fim')"
                                />
                            </div>
                        </div>

                        <div x-show="recorrente">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Dias da Semana</label>
                                <div class="grid grid-cols-4 gap-2">
                                    @php
                                        $diasSemana = [
                                            '0' => 'Domingo',
                                            '1' => 'Segunda',
                                            '2' => 'Terça',
                                            '3' => 'Quarta',
                                            '4' => 'Quinta',
                                            '5' => 'Sexta',
                                            '6' => 'Sábado'
                                        ];
                                        $diasSelecionados = old('dias_semana', $visitante->dias_semana ? explode(',', $visitante->dias_semana) : []);
                                        if (!is_array($diasSelecionados) && !empty($diasSelecionados)) {
                                            $diasSelecionados = explode(',', $diasSelecionados);
                                        }
                                    @endphp
                                    
                                    @foreach($diasSemana as $valor => $dia)
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="dias_semana[]" value="{{ $valor }}" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" {{ in_array($valor, $diasSelecionados) ? 'checked' : '' }}>
                                            <span class="ml-2 text-sm text-gray-600">{{ $dia }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('dias_semana')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <x-forms.input 
                                    type="time"
                                    name="horario_inicio" 
                                    label="Horário de Início" 
                                    :value="old('horario_inicio', $visitante->horario_inicio ? \Carbon\Carbon::parse($visitante->horario_inicio)->format('H:i') : '')" 
                                    :error="$errors->first('horario_inicio')"
                                />

                                <x-forms.input 
                                    type="time"
                                    name="horario_fim" 
                                    label="Horário de Término" 
                                    :value="old('horario_fim', $visitante->horario_fim ? \Carbon\Carbon::parse($visitante->horario_fim)->format('H:i') : '')" 
                                    :error="$errors->first('horario_fim')"
                                />
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                <x-forms.input 
                                    type="date"
                                    name="data_validade_inicio" 
                                    label="Validade Inicial" 
                                    :value="old('data_validade_inicio', $visitante->data_validade_inicio ? \Carbon\Carbon::parse($visitante->data_validade_inicio)->format('Y-m-d') : '')" 
                                    required 
                                    :error="$errors->first('data_validade_inicio')"
                                />

                                <x-forms.input 
                                    type="date"
                                    name="data_validade_fim" 
                                    label="Validade Final" 
                                    :value="old('data_validade_fim', $visitante->data_validade_fim ? \Carbon\Carbon::parse($visitante->data_validade_fim)->format('Y-m-d') : '')" 
                                    :error="$errors->first('data_validade_fim')"
                                />
                            </div>
                        </div>

                        <div class="mt-4">
                            <x-forms.select 
                                name="ativo" 
                                label="Status" 
                                :value="old('ativo', $visitante->ativo)" 
                                required 
                                :error="$errors->first('ativo')"
                            >
                                <option value="1" {{ old('ativo', $visitante->ativo) == 1 ? 'selected' : '' }}>Ativo</option>
                                <option value="0" {{ old('ativo', $visitante->ativo) == 0 ? 'selected' : '' }}>Inativo</option>
                            </x-forms.select>
                        </div>

                        <div class="mt-4">
                            <x-forms.textarea 
                                name="observacoes" 
                                label="Observações" 
                                :value="old('observacoes', $visitante->observacoes)" 
                                :error="$errors->first('observacoes')"
                                rows="3"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end">
                <a href="{{ route('visitantes.show', $visitante) }}" class="mr-2">
                    <x-forms.button type="button" variant="danger" >
                        Cancelar
                    </x-forms.button>
                </a>
                <x-forms.button type="submit" variant="primary">
                    Atualizar Visitante
                </x-forms.button>
            </div>
        </form>
    </x-ui.card>
</x-layout.app> 