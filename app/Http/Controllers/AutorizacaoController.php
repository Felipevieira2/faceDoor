<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Morador;
use App\Models\Visitante;
use App\Models\Dispositivo;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

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

         
            
            // Formatando a resposta para o Vue
            $response = [
                'id' => $query_autorizacoes->id,
                'name' => $query_autorizacoes->name,    
                'identificador' => $query_autorizacoes->identificador,
                'type' => $query_autorizacoes->type,
                'location' => $query_autorizacoes->location,
                'status' => $query_autorizacoes->status,
                'created_at' => $query_autorizacoes->created_at,
                'updated_at' => $query_autorizacoes->updated_at,
            ];
            
            return response()->json($response);
        } catch (\Exception $e) {
            \Log::error('Erro ao buscar detalhes da autorização: ' . $e->getMessage() . ' - '. $e->getLine() . ' - '. $e->getFile());
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

        /*
        * Preciso pega todos os moradores e visitantes e checar quais dispositivos eles precisam de autorização ou revogação
        * 
        * 
        * 
        * 
        */
        try {
            // $model_selected = $request->get('model_selected');


            // if($model_selected == 'morador'){
            //     $model_selected = Morador::class;
            // }elseif($model_selected == 'visitante'){
            //     $model_selected = Visitante::class;
            // }
           
            // $query = AutorizacaoDispositivo::with(['authorizable.user']);
            // obter todos os moradores e visitantes 

            $dispositivo_id = $request->get('device_id');                    
            
            $dispositivo = Dispositivo::findOrFail($dispositivo_id);
            $model_selected_users  = Morador::with('user')->where('ativo', 1);

            if($dispositivo->torre_id){
                //pega todos os moradores da torre
                $model_selected_users = $model_selected_users->leftJoin('apartamentos', 'moradores.apartamento_id', '=', 'apartamentos.id')
                ->leftJoin('torres', 'apartamentos.torre_id', '=', 'torres.id')
                ->where('torres.id', $dispositivo->torre_id)
                ->select('moradores.*', 'user.id as user_id'); // Especificar claramente que queremos todas as colunas da tabela moradores
            }

            // // Aplicar filtros de status se definidos
            if ($request->filled('status')) {

                if($request->status == 'aguardando autorização'){
                    $model_selected_users->whereDoesntHave('autorizacoesDispositivos', function($query) use ($request){
                        $query->whereIn('status', ['autorizado', 'processando', 'revogado']);
                        $query->where('dispositivo_id', $request->device_id);
                    });

                
                }else{
                    $model_selected_users->whereHas('autorizacoesDispositivos', function($query) use ($request){
                        $query->where('status', $request->status);
                        $query->where('dispositivo_id', $request->device_id);
                    }); 
                                      
                }
                
            }

            // buscar todas as autorizacoes de moradores e visitantes
            $autorizacoesPorDispositivo = AutorizacaoDispositivo::where('dispositivo_id', $dispositivo_id)
                                                    ->whereIn('authorizable_type', [Morador::class, Visitante::class])
                                                     // add condition to query
                                                        
                                                    ->when($request->filled('status'), function($query) use ($request) {
                                                        return $query->where('status', $request->status);
                                                    })

                                                    ->whereIn('authorizable_id', $model_selected_users->pluck('moradores.id')                                                                                                      
                                                    ->concat($model_selected_users->pluck('moradores.id')))
                                                    ->get();                                                                           
            
            // Aplicar busca se definida
            if ($request->filled('search')) {
                $search = $request->search;
                $model_selected_users->where(function($q) use ($search) {
                    //busque no relacionamento de moradores com user
                    $q->whereHas('user', function($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%")
                              ->orWhere('email', 'like', "%{$search}%")
                              ->orWhere('cpf', 'like', "%{$search}%");
                    });
                });
            }
            //Preciso saber quais são os moradores ou visitantes precisam de autorização ou revogação no dispositivo selecionado (dispositivo_id)            
            $paginator = $model_selected_users->orderBy('created_at', 'desc')->paginate(10);


            // Formata os dados para o componente Vue
            $formattedData = $paginator->map(function ($model_selected_user) use ($autorizacoesPorDispositivo, $dispositivo) {
            
                $autorizacoes_moradores = $autorizacoesPorDispositivo->filter(function($autorizacao) use ($model_selected_user){                   
                    return $autorizacao->authorizable_type == Morador::class && $autorizacao->authorizable_id == $model_selected_user->id;
                });
               

                if(count($autorizacoes_moradores) == 0){ //para usuários que estão aguardando autorização
                   //create App\Models\AutorizacaoDispositivo 

                   $autorizacao = new AutorizacaoDispositivo();
                   $autorizacao->dispositivo_id = $dispositivo->id;
                   $autorizacao->authorizable_type = Morador::class;
                   $autorizacao->authorizable_id = $model_selected_user->id;
                   $autorizacao->status = 'aguardando autorização'; 
                   $autorizacao->identificador_dispositivo = $dispositivo->identificador_unico;
                   $autorizacao->authorizable_id = $model_selected_user->id;
                   $autorizacao->authorizable_type = Morador::class;   
                   $autorizacao->data_inicio_visitante = null;
                   $autorizacao->data_fim_visitante = null;
                   $autorizacao->messagem_erro = null;
                   $autorizacao->user_id_externo = null;
                   $autorizacao->match_user_id_externo = null;
                   $autorizacao->group_id_externo = null;
                   $autorizacao->autorizado_por = null;
                   $autorizacao->created_at = null;
                   $autorizacao->updated_at = null;

                   $autorizacoes_moradores->push($autorizacao);
                    
                  
                }
    
                return [
                    'user_id' => $model_selected_user->user_id,
                    'id' => $model_selected_user->id,
                    'name' => $model_selected_user->user->name,      
                    'cpf' => $model_selected_user->user->cpf,
                    'apartamento' => $model_selected_user->apartamento->numero,
                    'torre' => $model_selected_user->apartamento->torre->nome,
                    'type' =>  $model_selected_user->nome_entidade(), 
                    'autorizacoes' => $autorizacoes_moradores,
                    'foto' => Storage::url($model_selected_user->user->foto),
                                 
                ];
            });

           
            //remover os moradores que não tem autorizações por que preciso disso??????
            foreach($formattedData as $key => $value){

                
                if( count($value['autorizacoes']) == 0){
                    unset($formattedData[$key]);
                }
            }


          
                        
            // Retorna no formato esperado pelo Vue
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
                'message' => 'Erro ao buscar autorizações: ' . $e->getMessage()
            ], 500);
        }
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
