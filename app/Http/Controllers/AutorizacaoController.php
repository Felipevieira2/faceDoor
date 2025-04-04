<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Morador;
use App\Models\Visitante;
use App\Models\Dispositivo;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

use App\Models\AutorizacaoDispositivo;
use Illuminate\Support\Facades\Storage;
use App\Factories\AutorizacaoStrategyFactory;


class AutorizacaoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Verificar se o usuário está autenticado via web
        if (!auth()->check()) {
            return redirect()->route('login');
        }


        $filtro = $request->get('filtro', 'todos');
        $busca = $request->get('busca', '');
        $dispositivo_id = $request->get('dispositivo_id');
     
        
        // $autorizacoes = $query->latest()->paginate(10);
        $dispositivos = Dispositivo::where('ativo', true)->get();
        
        return view('admin.autorizacoes.index', compact('filtro', 'busca', 'dispositivos', 'dispositivo_id'));
    }

    

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $dispositivos = Dispositivo::where('ativo', true)->get();
        $moradores = Morador::with('user')->where('ativo', true)->get();
        $visitantes = Visitante::with('user')->where('ativo', true)->get();
        
        return view('admin.autorizacoes.create', compact('dispositivos', 'moradores', 'visitantes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function autorizar(Request $request): JsonResponse
    {
        $request->validate([
            'dispositivo_id' => 'required|string',
            'visitante_id' => 'nullable|numeric',
            'morador_id' => 'nullable|numeric',
            'data_inicio_visitante' => 'nullable|date',
            'data_fim_visitante' => 'nullable|date',
        ]);

        // Encontra o dispositivo e seu tipo
        $dispositivo = Dispositivo::findOrFail($request->dispositivo_id);

        // Cria a estratégia apropriada
        $strategy = AutorizacaoStrategyFactory::criarStrategy($dispositivo->fabricante);
        $endpoint = 'create_user_morador';

        if ($request->visitante_id) {
            $model_selected = Visitante::findOrFail($request->visitante_id);
            $endpoint = 'create_user_visitante';
        } elseif ($request->morador_id) {
            $model_selected = Morador::findOrFail($request->morador_id);
        } else {
            return response()->json([
                'message' => 'Nenhum visitante ou morador informado'
            ], 400);
        }

        // Executar a autorização
        $response = $strategy->validar($dispositivo, $model_selected);

        if ($response->getStatusCode() != 200) {
            return $response;
        }

        // Executar a autorização
        return $strategy->createJobByEndpoint($dispositivo, $model_selected, $endpoint);
    }

    public function revogar(Request $request): JsonResponse
    {
        $request->validate([
            'dispositivo_id' => 'required|string',
            'visitante_id' => 'nullable|numeric',
            'morador_id' => 'nullable|numeric',
        ]);

        // Encontra o dispositivo e seu tipo
        $dispositivo = Dispositivo::findOrFail($request->dispositivo_id);

        // Cria a estratégia apropriada
        $strategy = AutorizacaoStrategyFactory::criarStrategy($dispositivo->fabricante);

        if ($request->visitante_id) {
            $model_selected = Visitante::findOrFail($request->visitante_id);
        } elseif ($request->morador_id) {
            $model_selected = Morador::findOrFail($request->morador_id);
        }

        return $strategy->createJobByEndpoint($dispositivo, $model_selected, 'delete_user');
    }

    /**
     * Show the specified resource.
     */
    public function getAutorizacaoPorDispositivo( string $deviceId, string $userId )
    {
        try {
            $user = User::findOrFail($userId);  
            
            $model_class = $user->morador ? Morador::class : Visitante::class;
          
            $query_autorizacoes = AutorizacaoDispositivo::where('authorizable_id', $userId)
                                ->where('authorizable_type', $model_class)
                                ->where('dispositivo_id', $deviceId)
                                ->first();

            if(!$query_autorizacoes){
                return response()->json([
                    'message' => 'Nenhuma autorização encontrada'
                ], 404);
            }
            
            $model_selected = $model_class::findOrFail($userId);
            
            // Obter o dispositivo para acessar seu tipo (fabricante)
            $dispositivo = Dispositivo::findOrFail($deviceId);
         
            // Formatando a resposta para o Vue
            $response = [
                'id' => $query_autorizacoes->id,
                'name' => $query_autorizacoes->name,    
                'identificador' => $query_autorizacoes->identificador,
                'type' => $dispositivo->fabricante, // Obtem o tipo do dispositivo relacionado
                'location' => $dispositivo->localizacao, // Obtem a localização do dispositivo relacionado
                'status' => $query_autorizacoes->status,
                'created_at' => $query_autorizacoes->created_at,
                'updated_at' => $query_autorizacoes->updated_at,
            ];
            
            return response()->json($response);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar detalhes da autorização: ' . $e->getMessage() . ' - '. $e->getLine() . ' - '. $e->getFile());
            return response()->json([
                'message' => 'Erro ao buscar detalhes da autorização: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $autorizacao = AutorizacaoDispositivo::findOrFail($id);
            
            // Se a autorização estiver com status "autorizado", primeiro tentamos revogar o acesso
            if ($autorizacao->status === 'autorizado') {
                $dispositivo = Dispositivo::where('identificador_unico', $autorizacao->identificador_dispositivo)->firstOrFail();
                $strategy = AutorizacaoStrategyFactory::criarStrategy($dispositivo->fabricante);
                
                if ($autorizacao->authorizable_type === 'App\\Models\\Visitante') {
                    $model_selected = Visitante::findOrFail($autorizacao->authorizable_id);
                } else {
                    $model_selected = Morador::findOrFail($autorizacao->authorizable_id);
                }
                
                // Tenta revogar a autorização no dispositivo
                $strategy->createJobByEndpoint($dispositivo, $model_selected, 'delete_user');
            }
            
            // Exclui o registro de autorização
            $autorizacao->delete();
            
            return response()->json([
                'message' => 'Autorização excluída com sucesso.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao excluir autorização: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retorna os dados de autorizações em formato JSON para a API
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function apiIndex(Request $request)
    {
        try {
            // 1. Obtendo todos os dispositivos ativos
            $dispositivosAtivos = Dispositivo::where('ativo', true)->get();
            
            // Obtendo moradores ativos
            $moradores = Morador::with(['user', 'apartamento.torre', 'autorizacoesDispositivos'])
                         ->where('ativo', true);
            
            // Aplicar filtros de status se definidos
            if ($request->filled('status')) {
                if ($request->status == 'aguardando') {
                    // Precisamos filtrar por 'processando' pois é o que usamos no banco agora
                    $moradores->whereHas('autorizacoesDispositivos', function($query) {
                        $query->where('status', 'aguardando');
                    });
                } else {
                    $moradores->whereHas('autorizacoesDispositivos', function($query) use ($request) {
                        $query->where('status', $request->status);
                    });
                }
            }
            
            // Aplicar busca se definida
            if ($request->filled('search')) {
                $search = $request->search;
                $moradores->where(function($q) use ($search) {
                    $q->whereHas('user', function($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%")
                              ->orWhere('email', 'like', "%{$search}%")
                              ->orWhere('cpf', 'like', "%{$search}%");
                    });
                });
            }
            
            // Obter resultados paginados
            $paginator = $moradores->orderBy('created_at', 'desc')->paginate(10);
            
            // Formatar dados para o componente Vue
            $formattedData = $paginator->map(function ($morador) use ($dispositivosAtivos) {
                // 2. Verificar dispositivos que o usuário pode ter acesso
                $dispositivosAcessiveis = $this->getDispositivosAcessiveis($morador, $dispositivosAtivos);
                
                // 3. Verificar/criar autorizações para os dispositivos acessíveis
                $autorizacoes = $this->processarAutorizacoes($morador, $dispositivosAcessiveis);
                
                // Retornar dados formatados
                return [
                    'user_id' => $morador->user_id,
                    'id' => $morador->id,
                    'name' => $morador->user->name,
                    'cpf' => $morador->user->cpf,
                    'apartamento' => $morador->apartamento->numero,
                    'torre' => $morador->apartamento->torre->nome,
                    'type' => $morador->nome_entidade(),
                    'autorizacoes' => $autorizacoes,
                    'foto' => Storage::url($morador->user->foto),
                ];
            });
            
            // Filtrar moradores sem autorizações, se necessário
            if ($request->filled('only_with_authorizations') && $request->only_with_authorizations) {
                $formattedData = $formattedData->filter(function($item) {
                    return count($item['autorizacoes']) > 0;
                });
            }
            
            // Retornar no formato esperado pelo Vue
            return response()->json([
                'data' => $formattedData,
                'meta' => [
                    'current_page' => $paginator->currentPage(),
                    'from' => $paginator->firstItem(),
                    'last_page' => $paginator->lastPage(),
                    'per_page' => $paginator->perPage(),
                    'to' => $paginator->lastItem(),
                    'total' => $paginator->total(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao buscar autorizações: ' . $e->getMessage() . ' - '. $e->getLine() . ' - '. $e->getFile()
            ], 500);
        }
    }
    
    /**
     * Determina quais dispositivos um morador pode acessar
     * 
     * @param Morador $morador
     * @param Collection $dispositivosAtivos
     * @return Collection
     */
    private function getDispositivosAcessiveis($morador, $dispositivosAtivos)
    {
        // Filtrar dispositivos com base nas regras de acesso
        return $dispositivosAtivos->filter(function($dispositivo) use ($morador) {
            // Regra 1: Dispositivos sem torre (entrada do condomínio) são acessíveis a todos
            if (empty($dispositivo->torre_id)) {
                return true;
            }
            
            // Regra 2: Dispositivos com torre específica são acessíveis apenas a moradores daquela torre
            if ($dispositivo->torre_id == $morador->apartamento->torre_id) {
                return true;
            }
            
            return false;
        });
    }
    
    /**
     * Processa autorizações para dispositivos acessíveis
     * 
     * @param Morador $morador
     * @param Collection $dispositivosAcessiveis
     * @return Collection
     */
    private function processarAutorizacoes($morador, $dispositivosAcessiveis)
    {
        // NOTA IMPORTANTE: No banco de dados, usamos 'aguardando autorização' como status para novas autorizações
        // que ainda não foram explicitamente autorizadas. Na interface, este status é tratado como
        // "aguardando autorização". É necessário ajustar o componente Vue para exibir corretamente este status.
        
        $autorizacoes = collect();
        
        // Para cada dispositivo acessível
        foreach ($dispositivosAcessiveis as $dispositivo) {
            // Verificar se já existe uma autorização
            $autorizacaoExistente = $morador->autorizacoesDispositivos()
                ->where('identificador_dispositivo', $dispositivo->identificador_unico)
                ->first();
            
            if ($autorizacaoExistente) {
                // Se existe, adiciona à coleção com o tipo apenas para visualização
                // Não estamos salvando o type, apenas adicionando à coleção de retorno
                $autorizacaoExistente = clone $autorizacaoExistente;
                $autorizacaoExistente->type = $dispositivo->fabricante;
                $autorizacaoExistente->localizacao = $dispositivo->localizacao;
                $autorizacoes->push($autorizacaoExistente);
            } else {
                // Se não existe, cria uma nova autorização com status "processando"
           
                $novaAutorizacao = new AutorizacaoDispositivo([
                    'identificador_dispositivo' => $dispositivo->identificador_unico,
                    'localizacao' => $dispositivo->localizacao,
                    'status' => 'aguardando',
                    'authorizable_id' => $morador->id,          
                    'authorizable_type' => get_class($morador),
                    'type' => $dispositivo->fabricante,
                    'dispositivo_id' => $dispositivo->id // Adicionando dispositivo_id que é obrigatório
                ]);
                
                // // Salvar a nova autorização no banco sem o campo type
                // $autorizacaoSalva = $morador->autorizacoesDispositivos()->save($novaAutorizacao);
                
                // // Adicionar o type apenas para visualização na coleção que será retornada
                // $autorizacaoSalva->type = $dispositivo->fabricante;
                
                // Adicionar à coleção para retorno
                $autorizacoes->push($novaAutorizacao);
            }
        }
        
        return $autorizacoes;
    }

    public function apiLocalizacoesIndex()
    {
        $localizacoes = Dispositivo::where('ativo', true)->get();

        $formattedData = $localizacoes->map(function ($localizacao) {
            return [
                'id' => $localizacao->id,
                'nome' => $localizacao->nome,
                'localizacao' => $localizacao->localizacao,
                'fabricante' => $localizacao->fabricante,
                'tipo' => $localizacao->tipo,
                'identificador_unico' => $localizacao->identificador_unico,             
            ];
        });

        return response()->json($formattedData);
    }
}
