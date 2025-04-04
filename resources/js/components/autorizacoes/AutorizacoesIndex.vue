<template>
	<div>
		<div class="flex items-center justify-between mb-6 border-b border-gray-200 dark:border-gray-700 pb-3">
			<h1 class="text-2xl font-bold">Controle de Acesso</h1>
		</div>

		<!-- Layout responsivo para filtros e busca -->
		<div class="flex flex-col sm:flex-row justify-between border-gray-200 dark:border-gray-700 pb-3 gap-3">
			<!-- Filtros em botões -->
			<div class="flex gap-2 my-3 overflow-x-auto pb-2">
				<label for="tipo_entidade"
					class="block text-lg font-medium text-gray-700 dark:text-gray-300">Filtro:</label>
				<button @click="filters.status = ''; searchautorizacoes()" :class="[
					'px-3 py-1.5 rounded-md text-sm transition cursor-pointer font-bold',
					filters.status === '' ? 'bg-blue-700 text-white' : 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-200'
				]">
					Todos
				</button>

				<button @click="filters.status = 'autorizado'; searchautorizacoes()" :class="[
					'px-3 py-1.5 rounded-md text-sm transition cursor-pointer font-bold',
					filters.status === 'autorizado' ? 'bg-green-700 text-white' : 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-200 '
				]">
					Autorizados
				</button>
				<button @click="filters.status = 'aguardando autorização'; searchautorizacoes()" :class="[
					'px-3 py-1.5 rounded-md text-sm transition cursor-pointer font-bold',
					filters.status === 'aguardando autorização' ? 'bg-yellow-500 text-black' : 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-200'
				]">
					Aguardando
				</button>

				<button @click="filters.status = 'revogado'; searchautorizacoes()" :class="[
					'px-3 py-1.5 rounded-md text-sm transition cursor-pointer font-bold',
					filters.status === 'revogado' ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-200'
				]">
					Revogados
				</button>
				<button @click="filters.status = 'processando'; searchautorizacoes()" :class="[
					'px-3 py-1.5 rounded-md text-sm transition cursor-pointer font-bold',
					filters.status === 'processando' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-200'
				]">
					Processando
				</button>
			</div>
		</div>
		<!-- Campo de busca -->
		<div class="flex flex-wrap justify-start sm:justify-start gap-2 overflow-x-auto mb-3  w-full sm:w-auto">
			<div class="flex  justify-between items-center gap-1 ">
				<input v-model="searchTerm" type="text" placeholder="Buscar"
					class="w-full px-2 py-1 border border-gray-300 rounded-lg focus:outline-none  dark:bg-gray-700 dark:text-white dark:border-gray-600" />
				<button @click="searchautorizacoes"
					class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-sm whitespace-nowrap">
					<i class="fas fa-search mr-2"></i> Buscar
				</button>
			</div>
		</div>



		<!-- Tabela responsiva -->
		<div class="flex flex-col">
			<div class="overflow-x-auto -mx-4 sm:mx-0">
				<div class="inline-block min-w-full py-2 align-middle px-4 sm:px-0">
					<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800 rounded-lg overflow-hidden">
						<thead class="bg-gray-150 dark:bg-blue-600 rounded-t-lg shadow-md">
							<tr>
								<th
									class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
									Foto</th>
								<th
									class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
									Nome/CPF</th>
								<th
									class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider hidden sm:table-cell">
									Torre</th>
								<th
									class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider hidden sm:table-cell">
									Apto</th>
								<th
									class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
									Status</th>
								<th
									class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
									Ações</th>
							</tr>
						</thead>
						<tbody
							class="divide-y divide-gray-200 bg-gray-50 dark:bg-gray-800 dark:divide-gray-700 rounded-b-lg">
							<!-- Spinner de carregamento -->
							<tr v-if="loading">
								<td colspan="6" class="px-3 sm:px-6 py-10">
									<div class="flex justify-center items-center">
										<div
											class="animate-spin rounded-full h-12 w-12 border-t-4 border-b-4 border-blue-600">
										</div>
										<span class="ml-3 text-lg text-gray-700 dark:text-gray-300">Carregando...</span>
									</div>
								</td>
							</tr>
							<tr v-else-if="autorizacoes.length === 0">
								<td colspan="6"
									class="px-3 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">
									Nenhuma autorização encontrada.
								</td>
							</tr>
							<tr v-else v-for="autorizacao in autorizacoes" :key="autorizacao.id"
								class="hover:bg-gray-300 dark:hover:bg-gray-900">
								<td class="px-3 sm:px-6 py-4 whitespace-nowrap">
									<img :src="autorizacao.foto" alt="Foto do usuário"
										class="w-8 h-8 sm:w-10 sm:h-10 rounded-full cursor-pointer hover:opacity-75 transition-opacity"
										@click="openGallery(autorizacao.foto)">
								</td>
								<td class="px-3 sm:px-6 py-4 whitespace-nowrap">
									<div class="text-xs sm:text-sm font-medium dark:text-white">{{ autorizacao.name }}

										<!-- cpf em letra pequena e sublinhado como legenda de foto -->
										<legend class="text-xs  dark:text-gray-400 text-stone-400"> CPF: {{
											autorizacao.cpf
											}}</legend>

										<legend class="text-xs  dark:text-gray-400 text-stone-400"></legend>
										<legend class="text-xs  dark:text-gray-400 text-stone-400"> Tipo: {{
											getDeviceTypeName(autorizacao.type) }}</legend>
									</div>
								</td>
								<td class="px-3 sm:px-6 py-4 whitespace-nowrap hidden sm:table-cell">
									<div class="text-xs sm:text-sm dark:text-white">{{
										autorizacao.torre }}</div>
								</td>
								<td class="px-3 sm:px-6 py-4 whitespace-nowrap hidden sm:table-cell">
									<div class="text-xs sm:text-sm dark:text-white"> {{
										autorizacao.apartamento }}</div>
								</td>
								<td class="px-3 sm:px-6 py-4 whitespace-nowrap">
									<div class="flex gap-1 flex-wrap">
										<div v-for="autorizacao_item in autorizacao.autorizacoes" :key="autorizacao_item.id">
											<div :class="[
												'flex flex-col rounded-md overflow-hidden shadow-sm border-l-4 min-w-32 mx-1 my-1',
												autorizacao_item.status === 'autorizado' ? 'border-l-green-500 bg-green-50 dark:bg-green-900/20' : 
												autorizacao_item.status === 'processando' ? 'border-l-blue-500 bg-blue-50 dark:bg-blue-900/20' :
												autorizacao_item.status === 'aguardando' ? 'border-l-yellow-500 bg-yellow-50 dark:bg-yellow-900/20' : 
												autorizacao_item.status === 'nao_autorizado' ? 'border-l-red-500 bg-red-50 dark:bg-red-900/20' :
												'border-l-gray-500 bg-gray-50 dark:bg-gray-900/20'
											]">
												<!-- Cabeçalho do card com ícone e tipo de dispositivo -->
												<div class="flex items-center px-2 py-1 border-b border-gray-200 dark:border-gray-700">
													<i :class="[
														'mr-2 text-lg',
														autorizacao_item.status === 'autorizado' ? 'fas fa-door-open text-green-600 dark:text-green-400' : 
														autorizacao_item.status === 'processando' ? 'fas fa-cog fa-spin text-blue-600 dark:text-blue-400' :
														autorizacao_item.status === 'aguardando' ? 'fas fa-hourglass-half text-yellow-600 dark:text-yellow-400' : 
														autorizacao_item.status === 'nao_autorizado' ? 'fas fa-ban text-red-600 dark:text-red-400' :
														'fas fa-question-circle text-gray-600 dark:text-gray-400'
													]"></i>
												<!-- ID do Dispositivo -->
												<div class="flex items-center">
														
														<span class="text-xs text-gray-700 dark:text-gray-300 truncate max-w-32" :title="autorizacao_item.identificador_dispositivo">
															{{ autorizacao_item.localizacao }}
														</span>
													</div>
												</div>
												<!-- Corpo do card com informações do dispositivo -->
												<div class="px-2 py-1">
													<!-- Status -->
													<div class="flex items-center mb-1">
														<span class="text-xs text-gray-500 dark:text-gray-400 w-14">Status:</span>
														<span :class="[
															'text-xs font-semibold px-1.5 py-0.5 rounded-full',
															autorizacao_item.status === 'autorizado' ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' :
															autorizacao_item.status === 'processando' ? 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100' :
															autorizacao_item.status === 'aguardando' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100' :
															autorizacao_item.status === 'nao_autorizado' ? 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100' :
															'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100'
														]">
															{{ autorizacao_item.status === 'processando' ? 'Processando' : getStatusName(autorizacao_item.status) }}
														</span>
													</div>
													
													
												</div>
											</div>
										</div>
									</div>
								</td>
								<td class="px-3 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm font-medium">
									<div class="flex gap-2">
										<a v-if="autorizacao.status !== 'autorizado'"
											@click="authorizeDevice(autorizacao.id)"
											class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300 cursor-pointer"
											title="Autorizar Dispositivo">
											<i class="fa-solid fa-check"></i>
										</a>
										<a v-if="autorizacao.status !== 'revogado'"
											@click="rejectDevice(autorizacao.id)"
											class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 cursor-pointer"
											title="Revogar Autorização">
											<i class="fa-solid fa-ban"></i>
										</a>
										<a @click="viewDeviceDetails(autorizacao.id)"
											class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 cursor-pointer"
											title="Ver Detalhes">
											<i class="fa-solid fa-eye"></i>
										</a>
										<a @click="openDevicesModal(autorizacao)"
											class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 cursor-pointer"
											title="Dispositivos Disponíveis">
											<i class="fa-solid fa-list"></i>
										</a>
									</div>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>

			<!-- Paginação -->
			<div class="mt-4">
				<nav v-if="pagination.last_page > 1" class="flex justify-between items-center">
					<!-- Aqui você pode implementar os links de paginação similar ao Laravel -->
				</nav>
			</div>
		</div>

		<!-- Modal para detalhes do dispositivo -->
		<div v-if="showModal" class="fixed inset-0 flex items-center justify-center z-50"
			style="background-color: rgba(0, 0, 0, 0.7);">
			<div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-4xl w-full mx-4 shadow-xl overflow-y-auto max-h-[90vh]">
				<div class="flex justify-between items-center mb-4 border-b dark:border-gray-700 pb-3">
					<h3 class="text-lg font-bold dark:text-white">Detalhes Completos</h3>
					<button @click="showModal = false"
						class="text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-white cursor-pointer">
						<i class="fas fa-times"></i>
					</button>
				</div>

				<div v-if="selectedDevice" class="space-y-6">
					<!-- Cabeçalho com foto e nome -->
					<div class="flex items-center space-x-4 pb-4 border-b dark:border-gray-700">
						<img :src="selectedDevice.foto" alt="Foto do usuário"
							class="w-20 h-20 rounded-full object-cover border-2 border-blue-500">
						<div>
							<h2 class="text-xl font-bold dark:text-white">{{ selectedDevice.name }}</h2>
							<div class="flex items-center mt-1">
								<span :class="{
									'px-2 inline-flex text-xs leading-5 font-semibold rounded-full': true,
									'bg-green-100 dark:bg-green-900 text-green-800 dark:text-white': selectedDevice.status == 'autorizado',
									'bg-yellow-100 dark:bg-yellow-500 text-yellow-800 dark:text-black': selectedDevice.status == 'aguardando autorização',
									'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200': selectedDevice.status == 'revogado',
									'bg-blue-100 dark:bg-blue-500 text-blue-800 dark:text-black': selectedDevice.status == 'processando'
								}">
									{{ getStatusName(selectedDevice.status) }}
								</span>
							</div>
						</div>
					</div>

					<!-- Informações Pessoais -->
					<div>
						<h4 class="text-lg font-medium dark:text-white mb-3 flex items-center">
							<i class="fas fa-user-circle mr-2 text-blue-600 dark:text-blue-400"></i>
							Informações Pessoais
						</h4>
						<div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 grid grid-cols-1 md:grid-cols-2 gap-4">
							<div>
								<p class="text-sm text-gray-500 dark:text-gray-400">Nome Completo:</p>
								<p class="text-sm font-medium dark:text-white">{{ selectedDevice.name }}</p>
							</div>
							<div>
								<p class="text-sm text-gray-500 dark:text-gray-400">CPF:</p>
								<p class="text-sm font-medium dark:text-white">{{ selectedDevice.cpf }}</p>
							</div>
							<div>
								<p class="text-sm text-gray-500 dark:text-gray-400">E-mail:</p>
								<p class="text-sm font-medium dark:text-white">{{ selectedDevice.email || 'email@exemplo.com' }}</p>
							</div>
							<div>
								<p class="text-sm text-gray-500 dark:text-gray-400">Telefone:</p>
								<p class="text-sm font-medium dark:text-white">{{ selectedDevice.telefone || '(11) 98765-4321' }}</p>
							</div>
							<div>
								<p class="text-sm text-gray-500 dark:text-gray-400">Data de Nascimento:</p>
								<p class="text-sm font-medium dark:text-white">{{ selectedDevice.data_nascimento || '15/05/1985' }}</p>
							</div>
							<div>
								<p class="text-sm text-gray-500 dark:text-gray-400">Tipo:</p>
								<p class="text-sm font-medium dark:text-white">{{ selectedDevice.tipo_pessoa || 'Morador' }}</p>
							</div>
						</div>
					</div>

					<!-- Informações de Residência -->
					<div>
						<h4 class="text-lg font-medium dark:text-white mb-3 flex items-center">
							<i class="fas fa-home mr-2 text-green-600 dark:text-green-400"></i>
							Informações de Residência
						</h4>
						<div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 grid grid-cols-1 md:grid-cols-3 gap-4">
							<div>
								<p class="text-sm text-gray-500 dark:text-gray-400">Torre:</p>
								<p class="text-sm font-medium dark:text-white">{{ selectedDevice.torre }}</p>
							</div>
							<div>
								<p class="text-sm text-gray-500 dark:text-gray-400">Apartamento:</p>
								<p class="text-sm font-medium dark:text-white">{{ selectedDevice.apartamento }}</p>
							</div>
							<div>
								<p class="text-sm text-gray-500 dark:text-gray-400">Bloco:</p>
								<p class="text-sm font-medium dark:text-white">{{ selectedDevice.bloco || 'N/A' }}</p>
							</div>
							<div class="md:col-span-3">
								<p class="text-sm text-gray-500 dark:text-gray-400">Endereço Completo:</p>
								<p class="text-sm font-medium dark:text-white">{{ 
									`Torre ${selectedDevice.torre}, Apto ${selectedDevice.apartamento}${selectedDevice.bloco ? ', Bloco ' + selectedDevice.bloco : ''}` 
								}}</p>
							</div>
						</div>
					</div>

					<!-- Informações do Dispositivo Atual -->
					<div>
						<h4 class="text-lg font-medium dark:text-white mb-3 flex items-center">
							<i class="fas fa-microchip mr-2 text-purple-600 dark:text-purple-400"></i>
							Dispositivo Atual
						</h4>
						<div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 grid grid-cols-1 md:grid-cols-2 gap-4">
							<div>
								<p class="text-sm text-gray-500 dark:text-gray-400">Tipo de Dispositivo:</p>
								<p class="text-sm font-medium dark:text-white">{{ getDeviceTypeName(selectedDevice.type) }}</p>
							</div>
							<div>
								<p class="text-sm text-gray-500 dark:text-gray-400">Localização:</p>
								<p class="text-sm font-medium dark:text-white">{{ selectedDevice.localizacao || selectedDevice.location }}</p>
							</div>
							<div>
								<p class="text-sm text-gray-500 dark:text-gray-400">ID do Dispositivo:</p>
								<p class="text-sm font-medium dark:text-white">{{ selectedDevice.identificador }}</p>
							</div>
							<div>
								<p class="text-sm text-gray-500 dark:text-gray-400">Status de Acesso:</p>
								<p class="text-sm font-medium dark:text-white">{{ getStatusName(selectedDevice.status) }}</p>
							</div>
							<div>
								<p class="text-sm text-gray-500 dark:text-gray-400">Data de Solicitação:</p>
								<p class="text-sm font-medium dark:text-white">{{ formatDate(selectedDevice.created_at) }}</p>
							</div>
							<div>
								<p class="text-sm text-gray-500 dark:text-gray-400">Última Atualização:</p>
								<p class="text-sm font-medium dark:text-white">{{ formatDate(selectedDevice.updated_at) }}</p>
							</div>
						</div>
					</div>

					<!-- Dispositivos Disponíveis para Autorização -->
					<div>
						<h4 class="text-lg font-medium dark:text-white mb-3 flex items-center">
							<i class="fas fa-key mr-2 text-yellow-600 dark:text-yellow-400"></i>
							Dispositivos Disponíveis para Autorização
						</h4>
						
						<div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
							<div v-if="dispositivosDisponiveis.length === 0" class="text-center py-4">
								<p class="text-gray-500 dark:text-gray-400">Nenhum dispositivo adicional disponível para autorização.</p>
							</div>
							<div v-else class="overflow-x-auto">
								<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
									<thead>
										<tr>
											<th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Dispositivo</th>
											<th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Localização</th>
											<th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
											<th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ações</th>
										</tr>
									</thead>
									<tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
										<tr v-for="dispositivo in dispositivosDisponiveis" :key="dispositivo.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
											<td class="px-4 py-2 whitespace-nowrap">
												<div class="flex items-center">
													<i :class="[
														'mr-2 text-lg',
														dispositivo.type === 'face' ? 'fas fa-user text-blue-500' : '',
														dispositivo.type === 'rfid' ? 'fas fa-id-card text-green-500' : '',
														dispositivo.type === 'entrada' ? 'fas fa-door-open text-purple-500' : '',
														dispositivo.type === 'card' ? 'far fa-credit-card text-indigo-500' : '',
														dispositivo.type === 'keypad' ? 'fas fa-keyboard text-yellow-500' : ''
													]"></i>
													<div>
														<div class="text-sm font-medium dark:text-white">{{ getDeviceTypeName(dispositivo.type) }}</div>
														<div class="text-xs text-gray-500 dark:text-gray-400">ID: {{ dispositivo.id }}</div>
													</div>
												</div>
											</td>
											<td class="px-4 py-2 whitespace-nowrap">
												<div class="text-sm dark:text-white">{{ dispositivo.localizacao }}</div>
											</td>
											<td class="px-4 py-2 whitespace-nowrap">
												<span :class="{
													'px-2 inline-flex text-xs leading-5 font-semibold rounded-full': true,
													'bg-green-100 dark:bg-green-900 text-green-800 dark:text-white': dispositivo.status === 'autorizado',
													'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200': dispositivo.status === 'revogado',
													'bg-yellow-100 dark:bg-yellow-500 text-yellow-800 dark:text-black': dispositivo.status === 'aguardando autorização',
													'bg-blue-100 dark:bg-blue-500 text-blue-800 dark:text-black': dispositivo.status === 'processando'
												}">
													{{ dispositivo.status === 'aguardando autorização' ? 'Aguardando' : 
													   dispositivo.status === 'processando' ? 'Processando' : 
													   dispositivo.status }}
												</span>
											</td>
											<td class="px-4 py-2 whitespace-nowrap">
												<div class="flex gap-2">
													<a v-if="dispositivo.status !== 'autorizado' && dispositivo.status !== 'processando'"
														@click="authorizeDeviceFromModal(dispositivo.id)"
														class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300 cursor-pointer"
														title="Autorizar Dispositivo">
														<i class="fa-solid fa-check"></i>
													</a>
													<a v-if="dispositivo.status !== 'revogado'"
														@click="rejectDeviceFromModal(dispositivo.id)"
														class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 cursor-pointer"
														title="Revogar Autorização">
														<i class="fa-solid fa-ban"></i>
													</a>
													<a @click="viewDeviceDetails(dispositivo.id)"
														class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 cursor-pointer"
														title="Ver Detalhes">
														<i class="fa-solid fa-eye"></i>
													</a>
												</div>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>

					<!-- Histórico de Acessos (opcional) -->
					<div>
						<h4 class="text-lg font-medium dark:text-white mb-3 flex items-center">
							<i class="fas fa-history mr-2 text-indigo-600 dark:text-indigo-400"></i>
							Histórico de Acessos
						</h4>
						
						<div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
							<div v-if="true" class="text-center py-4">
								<p class="text-gray-500 dark:text-gray-400">Nenhum registro de acesso disponível.</p>
							</div>
							<!-- Tabela de histórico pode ser adicionada aqui no futuro -->
						</div>
					</div>

					<!-- Botões de ação -->
					<div class="flex justify-end space-x-3 mt-6 pt-3 border-t dark:border-gray-700">
						<button v-if="selectedDevice.status !== 'autorizado'" @click="authorizeSelectedDevice"
							class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition cursor-pointer">
							<i class="fas fa-check-circle mr-1"></i> Autorizar
						</button>
						<button v-if="selectedDevice.status !== 'revogado'" @click="rejectSelectedDevice"
							class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition cursor-pointer">
							<i class="fas fa-ban mr-1"></i> Revogar
						</button>
						<button @click="showModal = false"
							class="bg-gray-300 text-gray-700 dark:bg-gray-700 dark:text-white px-4 py-2 rounded-md hover:bg-gray-400 dark:hover:bg-gray-600 transition cursor-pointer">
							<i class="fas fa-times-circle mr-1"></i> Fechar
						</button>
					</div>
				</div>
			</div>
		</div>

		<!-- Modal da Galeria -->
		<div v-if="showGallery" class="fixed inset-0 flex items-center justify-center z-50"
			style="background-color: rgba(0, 0, 0, 0.7);"
			@click="closeGallery" @keydown.esc="closeGallery">
			<div class="relative" @click.stop>
				<img :src="currentImage" alt="Imagem Ampliada" class="max-h-[80vh] max-w-[90vw] rounded-lg">
				<button @click="closeGallery" class="absolute top-4 right-4 text-white hover:text-gray-300">
					<i class="fas fa-times text-2xl"></i>
				</button>
			</div>
		</div>

		<!-- Modal de Confirmação de Autorização -->
		<div v-if="showAuthModal" class="fixed inset-0 flex items-center justify-center z-50"
			style="background-color: rgba(0, 0, 0, 0.7);">
			<div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-md w-full mx-4 shadow-xl">
				<div class="flex justify-between items-center mb-4 border-b dark:border-gray-700 pb-3">
					<h3 class="text-lg font-bold dark:text-white">Confirmar Autorização</h3>
					<button @click="cancelAuth"
						class="text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-white cursor-pointer">
						<i class="fas fa-times cursor-pointer"></i>
					</button>
				</div>

				<div v-if="authDevice" class="space-y-4">
					<div class="flex space-x-4 items-center">
						<img :src="authDevice.foto" alt="Foto do usuário"
							class="w-16 h-16 rounded-full object-cover border-2 border-blue-500">
						<div>
							<h4 class="font-medium dark:text-white text-lg">{{ authDevice.name }}</h4>
							<p class="text-sm text-gray-500 dark:text-gray-400">CPF: {{ authDevice.cpf }}</p>
						</div>
					</div>

					<div class="bg-gray-100 dark:bg-gray-700 p-3 rounded-lg">
						<h5 class="font-medium dark:text-white mb-2">Informações do Dispositivo</h5>
						<div class="grid grid-cols-2 gap-2 text-sm">
							<div>
								<p class="text-gray-500 dark:text-gray-400">Tipo:</p>
								<p class="dark:text-white">{{ getDeviceTypeName(authDevice.type) }}</p>
							</div>
							<div>
								<p class="text-gray-500 dark:text-gray-400">Localização:</p>
								<p class="dark:text-white">{{ authDevice.localizacao }}</p>
							</div>
							<div>
								<p class="text-gray-500 dark:text-gray-400">Torre:</p>
								<p class="dark:text-white">{{ authDevice.torre }}</p>
							</div>
							<div>
								<p class="text-gray-500 dark:text-gray-400">Apartamento:</p>
								<p class="dark:text-white">{{ authDevice.apartamento }}</p>
							</div>
							<div>
								<p class="text-gray-500 dark:text-gray-400">ID do Dispositivo:</p>
								<p class="dark:text-white">{{ authDevice.identificador }}</p>
							</div>
							<div>
								<p class="text-gray-500 dark:text-gray-400">Data de Solicitação:</p>
								<p class="dark:text-white">{{ formatDate(authDevice.created_at) }}</p>
							</div>
						</div>
					</div>

					<div class="bg-yellow-50 dark:bg-yellow-900/30 p-3 rounded-lg border-l-4 border-yellow-500 mt-2">
						<p class="text-sm text-yellow-800 dark:text-yellow-200">
							<i class="fas fa-exclamation-circle mr-2"></i>
							Ao autorizar este dispositivo, o usuário terá acesso a área onde o dispositivo está localizado.
						</p>
					</div>

					<div class="flex justify-between space-x-3 mt-6">
						<button @click="cancelAuth"
							class="w-1/2 bg-gray-300 text-gray-700 dark:bg-gray-700 dark:text-white px-4 py-2 rounded-md hover:bg-gray-400 dark:hover:bg-gray-600 transition cursor-pointer">
							Cancelar
						</button>
						<button @click="confirmAuth"
							class="w-1/2 bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition cursor-pointer">
							Confirmar Autorização
						</button>
					</div>
				</div>
			</div>
		</div>

		<!-- Modal de Confirmação de Revogação -->
		<div v-if="showRevokeModal" class="fixed inset-0 flex items-center justify-center z-50"
			style="background-color: rgba(0, 0, 0, 0.7);">
			<div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-md w-full mx-4 shadow-xl">
				<div class="flex justify-between items-center mb-4 border-b dark:border-gray-700 pb-3">
					<h3 class="text-lg font-bold dark:text-white">Confirmar Revogação de Acesso</h3>
					<button @click="cancelRevoke"
						class="text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-white cursor-pointer">
						<i class="fas fa-times cursor-pointer"></i>
					</button>
				</div>

				<div v-if="revokeDevice" class="space-y-4">
					<div class="flex space-x-4 items-center">
						<img :src="revokeDevice.foto" alt="Foto do usuário"
							class="w-16 h-16 rounded-full object-cover border-2 border-red-500">
						<div>
							<h4 class="font-medium dark:text-white text-lg">{{ revokeDevice.name }}</h4>
							<p class="text-sm text-gray-500 dark:text-gray-400">CPF: {{ revokeDevice.cpf }}</p>
						</div>
					</div>

					<div class="bg-gray-100 dark:bg-gray-700 p-3 rounded-lg">
						<h5 class="font-medium dark:text-white mb-2">Informações do Dispositivo</h5>
						<div class="grid grid-cols-2 gap-2 text-sm">
							<div>
								<p class="text-gray-500 dark:text-gray-400">Tipo:</p>
								<p class="dark:text-white">{{ getDeviceTypeName(revokeDevice.type) }}</p>
							</div>
							<div>
								<p class="text-gray-500 dark:text-gray-400">Localização:</p>
								<p class="dark:text-white">{{ revokeDevice.localizacao }}</p>
							</div>
							<div>
								<p class="text-gray-500 dark:text-gray-400">Torre:</p>
								<p class="dark:text-white">{{ revokeDevice.torre }}</p>
							</div>
							<div>
								<p class="text-gray-500 dark:text-gray-400">Apartamento:</p>
								<p class="dark:text-white">{{ revokeDevice.apartamento }}</p>
							</div>
							<div>
								<p class="text-gray-500 dark:text-gray-400">ID do Dispositivo:</p>
								<p class="dark:text-white">{{ revokeDevice.identificador }}</p>
							</div>
							<div>
								<p class="text-gray-500 dark:text-gray-400">Data de Autorização:</p>
								<p class="dark:text-white">{{ formatDate(revokeDevice.updated_at || revokeDevice.created_at) }}</p>
							</div>
						</div>
					</div>

					<div class="bg-red-50 dark:bg-red-900/30 p-3 rounded-lg border-l-4 border-red-500 mt-2">
						<p class="text-sm text-red-800 dark:text-red-200">
							<i class="fas fa-exclamation-triangle mr-2"></i>
							Ao revogar este acesso, o usuário não poderá mais utilizar este dispositivo para entrar na área. Esta ação pode ser revertida posteriormente.
						</p>
					</div>

					<div class="flex justify-between space-x-3 mt-6">
						<button @click="cancelRevoke"
							class="w-1/2 bg-gray-300 text-gray-700 dark:bg-gray-700 dark:text-white px-4 py-2 rounded-md hover:bg-gray-400 dark:hover:bg-gray-600 transition cursor-pointer">
							Cancelar
						</button>
						<button @click="confirmRevoke"
							class="w-1/2 bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition cursor-pointer">
							Confirmar Revogação
						</button>
					</div>
				</div>
			</div>
		</div>

		<!-- Modal de Dispositivos Disponíveis -->
		<div v-if="showDevicesModal" class="fixed inset-0 flex items-center justify-center z-50"
			style="background-color: rgba(0, 0, 0, 0.7);">
			<div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-3xl w-full mx-4 shadow-xl">
				<div class="flex justify-between items-center mb-4 border-b dark:border-gray-700 pb-3">
					<h3 class="text-lg font-bold dark:text-white">Dispositivos Disponíveis para {{ selectedUserForDevices?.name }}</h3>
					<button @click="showDevicesModal = false"
						class="text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-white">
						<i class="fas fa-times"></i>
					</button>
				</div>

				<div class="overflow-x-auto">
					<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
						<thead class="bg-gray-100 dark:bg-gray-700">
							<tr>
								<th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
									Dispositivo
								</th>
								<th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
									Localização
								</th>
								<th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
									Status
								</th>
								<th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
									Ações
								</th>
							</tr>
						</thead>
						<tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
							<tr v-for="dispositivo in availableDevices" :key="dispositivo.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
								<td class="px-4 py-2 whitespace-nowrap">
									<div class="text-sm dark:text-white">{{ getDeviceTypeName(dispositivo.type) }}</div>
									<div class="text-xs text-gray-500 dark:text-gray-400">ID: {{ dispositivo.id }}</div>
								</td>
								<td class="px-4 py-2 whitespace-nowrap">
									<div class="text-sm dark:text-white">{{ dispositivo.localizacao }}</div>
								</td>
								<td class="px-4 py-2 whitespace-nowrap">
									<span :class="{
										'px-2 inline-flex text-xs leading-5 font-semibold rounded-full': true,
										'bg-green-100 dark:bg-green-900 text-green-800 dark:text-white': dispositivo.status === 'ativo',
										'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200': dispositivo.status === 'inativo',
										'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200': dispositivo.status === 'manutenção'
									}">
										{{ dispositivo.status }}
									</span>
								</td>
								<td class="px-4 py-2 whitespace-nowrap">
									<button @click="assignDeviceToUser(dispositivo.id)" 
										class="bg-blue-600 text-white text-xs px-2 py-1 rounded hover:bg-blue-700 transition">
										Atribuir
									</button>
								</td>
							</tr>
						</tbody>
					</table>
				</div>

				<div class="flex justify-end mt-6">
					<button @click="showDevicesModal = false"
						class="bg-gray-300 text-gray-700 dark:bg-gray-700 dark:text-white px-4 py-2 rounded-md hover:bg-gray-400 dark:hover:bg-gray-600 transition">
						Fechar
					</button>
				</div>
			</div>
		</div>

		<!-- Add AlertContainer inside the component -->
		<div class="alert-local-container">
			<AlertContainer ref="alertContainer" />
		</div>
	</div>
</template>

<script>
import { ref, reactive, onMounted, computed } from 'vue';
import axios from 'axios';
import { useAlert } from '../../composables/useAlert';
import AlertContainer from '../AlertContainer.vue';

export default {
	name: 'AccessControlDeviceAuthorization',
	components: {
		AlertContainer
	},
	setup() {
		const autorizacoes = ref([]);
		const searchTerm = ref('');
		const loading = ref(false);
		const showModal = ref(false);
		const selectedDevice = ref(null);
		const locations = ref([]);
		const alertContainer = ref(null);
		const dispositivosDisponiveis = ref([]);
		
		// Criar uma instância local do alertService
		const alertCustom = {
			success: (message, options = {}) => {
				if (alertContainer.value?.$el.querySelector) {
					console.log("Usando serviço interno");
					alertContainer.value.alertService.success(message, options);
				} else {
					console.log("Mensagem de sucesso:", message);
				}
			},
			error: (message, options = {}) => {
				if (alertContainer.value?.$el.querySelector) {
					alertContainer.value.alertService.error(message, options);
				} else {
					console.error("Mensagem de erro:", message);
				}
			},
			warning: (message, options = {}) => {
				if (alertContainer.value?.$el.querySelector) {
					alertContainer.value.alertService.warning(message, options);
				} else {
					console.warn("Mensagem de aviso:", message);
				}
			},
			info: (message, options = {}) => {
				if (alertContainer.value?.$el.querySelector) {
					alertContainer.value.alertService.info(message, options);
				} else {
					console.info("Mensagem de informação:", message);
				}
			}
		};

		// Variáveis para a galeria
		const showGallery = ref(false);
		const currentImage = ref('');

		// Variáveis para modal de autorização
		const showAuthModal = ref(false);
		const authDevice = ref(null);
		const authDeviceId = ref(null);

		// Variáveis para modal de revogação
		const showRevokeModal = ref(false);
		const revokeDevice = ref(null);
		const revokeDeviceId = ref(null);

		// Variáveis para modal de dispositivos disponíveis
		const showDevicesModal = ref(false);
		const selectedUserForDevices = ref(null);
		const availableDevices = ref([]);

		const filters = reactive({
			status: '',
			deviceType: '',
			location: ''
		});

		const pagination = reactive({
			current_page: 1,
			from: 1,
			to: 10,
			total: 0,
			last_page: 1
		});

		// Configuração do axios
		const setupAxios = () => {
			// Configurar axios para usar cookies de sessão Laravel
			axios.defaults.withCredentials = true;

			// Obter o CSRF token da meta tag
			const token = document.head.querySelector('meta[name="csrf-token"]');
			if (token) {
				axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
			} else {
				console.error('CSRF token não encontrado: https://laravel.com/docs/csrf#csrf-x-csrf-token');
			}

			// Definir cabeçalho Accept para API
			axios.defaults.headers.common['Accept'] = 'application/json';
		};

		// Buscar dispositivos
		const fetchautorizacoes = async (page = 1) => {
			loading.value = true;
			try {
				const response = await axios.get('/api/controle-acesso/autorizacoes', {
					params: {
						page,
						search: searchTerm.value,
						status: filters.status,
						device_type: filters.deviceType
					}
				}).then(response => { // loading.value = false;

					autorizacoes.value = response.data.data || [];


					if (response.data.meta) {
						pagination.current_page = response.data.meta.current_page;
						pagination.from = response.data.meta.from;
						pagination.to = response.data.meta.to;
						pagination.total = response.data.meta.total;
						pagination.last_page = response.data.meta.last_page;
					}
					
					loading.value = false;
				});
			
				
			} catch (error) {
				console.error('Erro ao buscar autorizações:', error);
				if (error.response) {
					console.error('Status:', error.response.status);
					console.error('Data:', error.response.data);
				}
				if (error.response && error.response.status === 401) {
					// Redirecionar para login se não estiver autenticado
					window.location.href = '/login';
				}
				loading.value = false;
			}
		};

		// Abrir modal de dispositivos disponíveis
		const openDevicesModal = (user) => {
			selectedUserForDevices.value = user;
			showDevicesModal.value = true;
			
			// Dados estáticos para dispositivos disponíveis
			availableDevices.value = [
				{
					id: 101,
					type: 'face',
					localizacao: 'Portaria Principal',
					status: 'ativo'
				},
				{
					id: 102,
					type: 'rfid',
					localizacao: 'Entrada de Serviço',
					status: 'ativo'
				},
				{
					id: 103,
					type: 'entrada',
					localizacao: 'Academia',
					status: 'manutenção'
				},
				{
					id: 104,
					type: 'card',
					localizacao: 'Piscina',
					status: 'inativo'
				},
				{
					id: 105,
					type: 'keypad',
					localizacao: 'Salão de Festas',
					status: 'ativo'
				}
			];
		};

		// Atribuir dispositivo ao usuário
		const assignDeviceToUser = (deviceId) => {
			// Usar o novo sistema de alertas
			alertCustom.success(`Dispositivo ${deviceId} atribuído para ${selectedUserForDevices.value.name}`, {
				title: 'Dispositivo Atribuído',
				duration: 4000,
				action: true,
				actionText: 'Ver Detalhes',
				actionCallback: () => {
					console.log('Ação de ver detalhes do dispositivo', deviceId);
					// Aqui você poderia abrir uma modal com detalhes
					viewDeviceDetails(deviceId);
				}
			});
			
			showDevicesModal.value = false;
		};

		// Autorizar dispositivo
		const authorizeDevice = async (deviceId) => {
			try {
				// Simular busca de informações do dispositivo com dados estáticos
				const dispositivoEstatico = {
					id: deviceId,
					name: autorizacoes.value.find(a => a.id === deviceId)?.name || 'Usuário',
					foto: autorizacoes.value.find(a => a.id === deviceId)?.foto || 'https://randomuser.me/api/portraits/lego/1.jpg',
					cpf: autorizacoes.value.find(a => a.id === deviceId)?.cpf || '000.000.000-00',
					type: autorizacoes.value.find(a => a.id === deviceId)?.type || 'face',
					localizacao: 'Portaria Principal',
					torre: autorizacoes.value.find(a => a.id === deviceId)?.torre || 'A',
					apartamento: autorizacoes.value.find(a => a.id === deviceId)?.apartamento || '101',
					identificador: `DEV-${deviceId}`,
					created_at: new Date().toISOString()
				};
				
				authDevice.value = dispositivoEstatico;
				authDeviceId.value = deviceId;
				showAuthModal.value = true;
				
				// Comentando a chamada real de API
				/*
				const response = await axios.get(`/api/controle-acesso/	/${deviceId}`);
				authDevice.value = response.data;
				authDeviceId.value = deviceId;
				showAuthModal.value = true;
				*/
			} catch (error) {
				console.error('Erro ao buscar detalhes do dispositivo:', error);
				if (error.response && error.response.status === 401) {
					window.location.href = '/login';
				}
			}
		};

		// Confirmar autorização
		const confirmAuth = async () => {
			try {
				// Simular autorização bem-sucedida
				showAuthModal.value = false;
			
				// Atualizar status na lista sem fazer chamada real à API
				const index = autorizacoes.value.findIndex(a => a.id === authDeviceId.value);
				if (index !== -1) {
					autorizacoes.value[index].status = 'autorizado';
					autorizacoes.value[index].autorizacoes.forEach(auth => {
						auth.status = 'autorizado';
					});
					
					// Mostrar alerta de sucesso
					
					alertCustom.success(`Processo de autorização de ${autorizacoes.value[index].name} foi iniciado com sucesso!`);
				}
				
				// Comentando a chamada real de API
				/*
				await axios.post(`/api/controle-acesso/autorizar`, {
					dispositivo_id: authDeviceId.value
				});
				showAuthModal.value = false;
				await fetchautorizacoes(pagination.current_page);
				*/
			} catch (error) {
				console.error('Erro ao autorizar dispositivo:', error);
				// Mostrar alerta de erro
				alertCustom.error('Erro ao tentar autorizar dispositivo. Tente novamente.');
				if (error.response && error.response.status === 401) {
					window.location.href = '/login';
				}
			}
		};

		// Cancelar autorização
		const cancelAuth = () => {
			showAuthModal.value = false;
			authDevice.value = null;
			authDeviceId.value = null;
		};

		// Rejeitar dispositivo
		const rejectDevice = async (deviceId, usuarioId) => {
			try {
				// Buscar informações do dispositivo para o modal de revogação
				// const dispositivoEstatico = {
				// 	id: deviceId,
				// 	name: autorizacoes.value.find(a => a.id === deviceId)?.name || 'Usuário',
				// 	foto: autorizacoes.value.find(a => a.id === deviceId)?.foto || 'https://randomuser.me/api/portraits/lego/1.jpg',
				// 	cpf: autorizacoes.value.find(a => a.id === deviceId)?.cpf || '000.000.000-00',
				// 	type: autorizacoes.value.find(a => a.id === deviceId)?.type || 'face',
				// 	localizacao: 'Portaria Principal',
				// 	torre: autorizacoes.value.find(a => a.id === deviceId)?.torre || 'A',
				// 	apartamento: autorizacoes.value.find(a => a.id === deviceId)?.apartamento || '101',
				// 	identificador: `DEV-${deviceId}`,
				// 	created_at: new Date().toISOString(),
				// 	updated_at: new Date().toISOString()
				// };
				
				// revokeDevice.value = dispositivoEstatico;
				// revokeDeviceId.value = deviceId;
				// showRevokeModal.value = true;
				
				// Comentando a chamada real de API
				
				const response = await axios.get(`/api/controle-acesso/autorizacoes/${deviceId}/`);
				revokeDevice.value = response.data;
				revokeDeviceId.value = deviceId;
				showRevokeModal.value = true;
				
			} catch (error) {
				console.error('Erro ao buscar detalhes do dispositivo para revogação:', error);
				alertCustom.error('Erro ao carregar informações do dispositivo. Tente novamente.');
				if (error.response && error.response.status === 401) {
					window.location.href = '/login';
				}
			}
		};

		// Ver detalhes do dispositivo
		const viewDeviceDetails = async (deviceId) => {
			try {
				// Dados estáticos para simular a resposta da API
				const dispositivoEstatico = {
					id: deviceId,
					name: autorizacoes.value.find(a => a.id === deviceId)?.name || 'Usuário',
					foto: autorizacoes.value.find(a => a.id === deviceId)?.foto || 'https://randomuser.me/api/portraits/lego/1.jpg',
					cpf: autorizacoes.value.find(a => a.id === deviceId)?.cpf || '000.000.000-00',
					email: 'usuario@exemplo.com',
					telefone: '(11) 98765-4321',
					data_nascimento: '15/05/1985',
					tipo_pessoa: autorizacoes.value.find(a => a.id === deviceId)?.type === 'face' ? 'Morador' : 'Visitante',
					type: autorizacoes.value.find(a => a.id === deviceId)?.type || 'face',
					localizacao: 'Portaria Principal',
					torre: autorizacoes.value.find(a => a.id === deviceId)?.torre || 'A',
					apartamento: autorizacoes.value.find(a => a.id === deviceId)?.apartamento || '101',
					bloco: 'Residencial',
					identificador: `DEV-${deviceId}`,
					status: autorizacoes.value.find(a => a.id === deviceId)?.status || 'autorizado',
					created_at: new Date(Date.now() - 1000 * 60 * 60 * 24 * 7).toISOString(), // 7 dias atrás
					updated_at: new Date().toISOString()
				};
				
				selectedDevice.value = dispositivoEstatico;
				showModal.value = true;
				
				// Simular dispositivos disponíveis para autorização
				dispositivosDisponiveis.value = [
					{
						id: 201,
						type: 'face',
						localizacao: 'Academia',
						status: 'aguardando autorização'
					},
					{
						id: 202,
						type: 'rfid',
						localizacao: 'Piscina',
						status: 'autorizado'
					},
					{
						id: 203,
						type: 'keypad',
						localizacao: 'Salão de Festas',
						status: 'revogado'
					},
					{
						id: 204,
						type: 'card',
						localizacao: 'Garagem',
						status: 'processando'
					}
				];
				
				// Comentando a chamada real de API
				/*
				const response = await axios.get(`/api/controle-acesso/autorizacoes/${deviceId}/detalhes`);
				selectedDevice.value = response.data.usuario;
				dispositivosDisponiveis.value = response.data.dispositivos_disponiveis;
				showModal.value = true;
				*/
			} catch (error) {
				console.error('Erro ao buscar detalhes do dispositivo:', error);
				alertCustom.error('Erro ao carregar detalhes. Tente novamente.');
				if (error.response && error.response.status === 401) {
					window.location.href = '/login';
				}
			}
		};

		// Autorizar dispositivo selecionado no modal
		const authorizeSelectedDevice = async () => {
			if (selectedDevice.value) {
				await authorizeDevice(selectedDevice.value.id);
				showModal.value = false;
			}
		};

		// Rejeitar dispositivo selecionado no modal
		const rejectSelectedDevice = async () => {
			if (selectedDevice.value) {
				await rejectDevice(selectedDevice.value.id);
				showModal.value = false;
			}
		};

		// Buscar dispositivos baseado nos termos de pesquisa
		const searchautorizacoes = () => {
			fetchautorizacoes(1);
		};

		// Formatar data
		const formatDate = (dateString) => {
			const options = { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' };
			return new Date(dateString).toLocaleDateString('pt-BR', options);
		};

		// Obter nome do tipo de dispositivo
		const getDeviceTypeName = (type) => {
			const types = {
				'entrada': 'Biométrico',
				'rfid': 'RFID',
				'keypad': 'Teclado',
				'face': 'Reconhecimento Facial',
				'card': 'Cartão'
			};

			return types[type] || type;
		};

		// Obter nome do status
		const getStatusName = (status) => {
			const statuses = {
				'autorizado': 'Autorizado',
				'aguardando autorização': 'Aguardando Autorização',
				'nao_autorizado': 'Revogado',
				'processando': 'Processando'
			};

			return statuses[status] || status;
		};

		// Navegar para página de adicionar dispositivo
		const goToNewDevicePage = () => {
			window.location.href = '/admin/dispositivos/create';
		};

		// Abrir galeria
		const openGallery = (imageUrl) => {
			currentImage.value = imageUrl;
			showGallery.value = true;

			// Adicionar listener para tecla ESC
			document.addEventListener('keydown', handleKeyDown);
		};

		// Fechar galeria
		const closeGallery = () => {
			showGallery.value = false;

			// Remover listener para tecla ESC
			document.removeEventListener('keydown', handleKeyDown);
		};

		// Handler para tecla ESC
		const handleKeyDown = (e) => {
			if (e.key === 'Escape') {
				closeGallery();
			}
		};

		// Cancelar revogação
		const cancelRevoke = () => {
			showRevokeModal.value = false;
			revokeDevice.value = null;
			revokeDeviceId.value = null;
		};

		// Confirmar revogação
		const confirmRevoke = async () => {
			try {
				// Simular revogação bem-sucedida
				showRevokeModal.value = false;
				
				// Atualizar status na lista sem fazer chamada real à API
				const index = autorizacoes.value.findIndex(a => a.id === revokeDeviceId.value);
				if (index !== -1) {
					autorizacoes.value[index].status = 'revogado';
					autorizacoes.value[index].autorizacoes.forEach(auth => {
						auth.status = 'revogado';
					});
					
					// Mostrar alerta de sucesso
					alertCustom.warning(`Acesso de ${autorizacoes.value[index].name} foi revogado.`);
				}
				
				// Comentando a chamada real de API
				/*
				await axios.post(`/api/controle-acesso/revogar`, {
					dispositivo_id: revokeDeviceId.value
				});
				await fetchautorizacoes(pagination.current_page);
				// Mostrar alerta após revogação real
				alertCustom.warning('Acesso revogado com sucesso.');
				*/
			} catch (error) {
				console.error('Erro ao revogar dispositivo:', error);
				// Mostrar alerta de erro
				alertCustom.error('Erro ao revogar acesso. Tente novamente.');
			}
		};

		// Atribuir dispositivo adicional ao usuário
		const atribuirDispositivoAoUsuario = (dispositivoId, usuarioId) => {
			try {
				// Simular atribuição com dados estáticos
				alertCustom.success(`Dispositivo ${dispositivoId} atribuído com sucesso!`, {
					title: 'Atribuição de Dispositivo',
					duration: 4000
				});
				
				// Remover o dispositivo da lista de disponíveis
				dispositivosDisponiveis.value = dispositivosDisponiveis.value.filter(d => d.id !== dispositivoId);
				
				// Comentando a chamada real de API
				/*
				await axios.post(`/api/controle-acesso/atribuir-dispositivo`, {
					dispositivo_id: dispositivoId,
					usuario_id: usuarioId
				});
				alertCustom.success(`Dispositivo atribuído com sucesso!`);
				dispositivosDisponiveis.value = dispositivosDisponiveis.value.filter(d => d.id !== dispositivoId);
				*/
			} catch (error) {
				console.error('Erro ao atribuir dispositivo:', error);
				alertCustom.error('Erro ao atribuir dispositivo. Tente novamente.');
			}
		};

		// Autorizar dispositivo a partir da modal de detalhes
		const authorizeDeviceFromModal = (deviceId) => {
			try {
				// Atualizar o status do dispositivo na lista
				const index = dispositivosDisponiveis.value.findIndex(d => d.id === deviceId);
				if (index !== -1) {
					dispositivosDisponiveis.value[index].status = 'processando';
					
					// Simular processamento e atualização após 1.5 segundos
					setTimeout(() => {
						dispositivosDisponiveis.value[index].status = 'autorizado';
						alertCustom.success(`Dispositivo ${dispositivosDisponiveis.value[index].localizacao} autorizado com sucesso!`);
					}, 1500);
				}
				
				// Comentando a chamada real de API
				/*
				await axios.post(`/api/controle-acesso/autorizar`, {
					dispositivo_id: deviceId
				});
				// Atualizar lista após sucesso
				const dispositivoAtualizado = await axios.get(`/api/controle-acesso/dispositivos/${deviceId}`);
				const index = dispositivosDisponiveis.value.findIndex(d => d.id === deviceId);
				if (index !== -1) {
					dispositivosDisponiveis.value[index] = dispositivoAtualizado.data;
				}
				alertCustom.success(`Dispositivo autorizado com sucesso!`);
				*/
			} catch (error) {
				console.error('Erro ao autorizar dispositivo:', error);
				alertCustom.error('Erro ao autorizar dispositivo. Tente novamente.');
			}
		};
		
		// Revogar dispositivo a partir da modal de detalhes
		const rejectDeviceFromModal = (deviceId) => {
			try {
				// Atualizar o status do dispositivo na lista
				const index = dispositivosDisponiveis.value.findIndex(d => d.id === deviceId);
				if (index !== -1) {
					dispositivosDisponiveis.value[index].status = 'revogado';
					alertCustom.warning(`Acesso ao dispositivo ${dispositivosDisponiveis.value[index].localizacao} revogado.`);
				}
				
				// Comentando a chamada real de API
				/*
				await axios.post(`/api/controle-acesso/revogar`, {
					dispositivo_id: deviceId
				});
				// Atualizar lista após sucesso
				const dispositivoAtualizado = await axios.get(`/api/controle-acesso/dispositivos/${deviceId}`);
				const index = dispositivosDisponiveis.value.findIndex(d => d.id === deviceId);
				if (index !== -1) {
					dispositivosDisponiveis.value[index] = dispositivoAtualizado.data;
				}
				alertCustom.warning(`Acesso revogado com sucesso.`);
				*/
			} catch (error) {
				console.error('Erro ao revogar dispositivo:', error);
				alertCustom.error('Erro ao revogar acesso. Tente novamente.');
			}
		};

		onMounted(() => {
			// Configurar axios antes de fazer requisições
			setupAxios();

			// Definir loading como true ao iniciar
			loading.value = true;

			// Buscar autorizações diretamente, sem chamar fetchLocations
			fetchautorizacoes();
		});

		return {
			autorizacoes,
			searchTerm,
			loading,
			filters,
			pagination,
			showModal,
			selectedDevice,
			locations,
			showGallery,
			currentImage,
			showAuthModal,
			authDevice,
			showDevicesModal,
			selectedUserForDevices,
			availableDevices,
			openGallery,
			closeGallery,
			fetchautorizacoes,
			searchautorizacoes,
			authorizeDevice,
			confirmAuth,
			cancelAuth,
			rejectDevice,
			viewDeviceDetails,
			formatDate,
			getDeviceTypeName,
			getStatusName,
			authorizeSelectedDevice,
			rejectSelectedDevice,
			goToNewDevicePage,
			openDevicesModal,
			assignDeviceToUser,
			alertCustom,
			alertContainer,
			// Novas propriedades para modal de revogação
			showRevokeModal,
			revokeDevice,
			cancelRevoke,
			confirmRevoke,
			dispositivosDisponiveis,
			atribuirDispositivoAoUsuario,
			authorizeDeviceFromModal,
			rejectDeviceFromModal
		};
	}
};
</script>

<style scoped>
.alert-local-container {
	position: absolute;
	width: 1px;
	height: 1px;
	overflow: visible;
	bottom: 0;
	right: 0;
	z-index: 100;
}
</style>