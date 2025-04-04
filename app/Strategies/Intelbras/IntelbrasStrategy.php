<?php

namespace App\Strategies\Intelbras;

use Carbon\Carbon;
use GuzzleHttp\Client;
use App\Models\Morador;
use App\Models\Visitante;
use App\Models\Dispositivo;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Models\AutorizacaoDispositivo;
use App\Strategies\AutorizacaoStrategy;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Exception\RequestException;

class IntelbrasStrategy implements AutorizacaoStrategy
{

    protected $client;

    /**
     * Construtor para injetar o Guzzle HTTP Client e credenciais de autenticação.
     */
    public function __construct()
    {


        $this->client = new Client([
            'timeout'  => 10.0, // Tempo máximo de espera
        ]);
    }

    public function validarRequest(Request $request): void
    {
        $request->validate([
            'dispositivo_id' => 'required|string',
            'visitante_id' => 'nullable|numeric',
            'morador_id' => 'nullable|numeric',
            'data_inicio_visitante' => 'nullable|date',
            'data_fim_visitante' => 'nullable|date',
        ]);
    }

    public function validar(Dispositivo $identificador_dispositivo, Visitante | Morador $model_selected): JsonResponse
    {
        if(!$identificador_dispositivo->ativo){
            return response()->json([
                'message' => 'Dispositivo não está ativo'
            ], 404);
        }
        
        if($identificador_dispositivo->fabricante != 'intelbras'){
            return response()->json([
                'message' => 'Dispositivo não é do fabricante Intelbras'
            ], 404);
        }
            
        return response()->json([
            'message' => 'Dispositivo validado com sucesso'
        ], 200);
    }

    public function createJobByEndpoint(Dispositivo $dispositivo, Visitante | Morador $model_selected, $endpoint): JsonResponse
    {
        
        //using match to call method by endpoint
        return match($endpoint){
            'create_user_morador' => $this->create_user($dispositivo, $model_selected),
            'create_user_visitante' => $this->create_user($dispositivo, $model_selected),
            'remover_user' => $this->deleteUser($dispositivo, $model_selected),
            default => response()->json([
                'message' => 'Função não implementada para o fabricante Intelbras',
                'endpoint' => $endpoint
            ], 501),
        };
        // Implementação para criar um job com base no endpoint
        return response()->json([
            'message' => 'Função não implementada para o fabricante Intelbras',
            'endpoint' => $endpoint
        ], 501);
    }

    public function getMethod($endpoint)
    {
        return $this->$endpoint;
    }

    public function create_user($dispositivo, $model_selected): JsonResponse
    {
        
        try {
            $this->client = new Client([
                'base_uri' => rtrim($dispositivo->ip, '/') . '/', // Base URI para facilitar
                'timeout'  => 10.0, // Tempo máximo de espera
            ]);


            // Preparar os dados para a criação do usuário
            $data = [
                'CardNo' => $model_selected->id,
                'CardName' => $model_selected->user->name,            
                'username' => $dispositivo->username,
                'password' => $dispositivo->password,
                'base_uri' => $dispositivo->ip,
                'deviceId' => $dispositivo->identificador_unico,
                'storeId' => $dispositivo->store_id,
                'face_registration_id' => $model_selected->id,
            ];

            if (get_class($model_selected) == Visitante::class) {
                
                $data['ValidDateStart'] = $model_selected->data_validade_inicio->format('Y-m-d H:i:s');
                $data['ValidDateEnd'] = $model_selected->data_validade_fim->format('Y-m-d H:i:s');
            }
           
            $highterUserId = $this->getMaxUserId($data); //mocar essa função para retornar um valor fixo em  C:\Users\felip\dev\fca\facedoor\tests\Unit\Strategies\IntelbrasTest\AutorizacaoIntelbrasTest.php
            
            //antes de adicionar o usuário, verificar se o maior ID já que já existe
            $data['UserID'] = $highterUserId + 1;
            $data['CardNo'] = $highterUserId + 1;

            $base64Content = base64_encode(file_get_contents(Storage::path('public/'.$model_selected->user->foto)));  

            // $base64Content = base64_encode($fileContent);
            $data['PhotoData'] = $base64Content;        
            $autorizacao = $model_selected->autorizacoeDispositivoByDispositivo($dispositivo);
            
            if($autorizacao){

               
                return response()->json([
                    'message' => 'Autorização já existe'
                ], 404);
            }else{
               
                $autorizacao = new \App\Models\AutorizacaoDispositivo(); // Ajuste o namespace conforme necessário
                $autorizacao->authorizable_type = get_class($model_selected);
                $autorizacao->authorizable_id = $model_selected->id;
                $autorizacao->identificador_dispositivo = $dispositivo->identificador_unico;
                $autorizacao->status = 'processando';
                $autorizacao->user_id_externo = $highterUserId;
                $autorizacao->save();

             
            }       

            // Construir a URL com os parâmetros
            $url = 'cgi-bin/recordUpdater.cgi';
            
            $queryArray = [
                'action'        => 'insert',
                'name'          => 'AccessControlCard',
                'CardNo'        => $data['CardNo'],
                'CardStatus'    => 0,
                'CardName'      => substr($data['CardName'], 0, 30),
                'UserID'        => $data['UserID'],
                'Doors'         => 0, // Exemplo: ['0']               
            ];
         
            if(isset($data['ValidDateStart']) && isset($data['ValidDateEnd'])) {
                $queryArray['ValidDateStart'] = $data['ValidDateStart'];
                $queryArray['ValidDateEnd']   = $data['ValidDateEnd'];
            }
           
            // Enviar a requisição GET para criar o usuário
            $response = $this->client->request('GET', $url, [
                'query' => $queryArray,
                'auth'  => [$data['username'], $data['password'], 'digest'],
            ]);


            // Verificar o código de status HTTP
            $statusCode = $response->getStatusCode();

            if ($statusCode === 200) {
                // Obter o corpo da resposta

                // Chamar a função para upload de imagem
                if (isset($data['PhotoData'])) {
                    $uploadSuccess = $this->uploadImage($data);
                    
                    if (!$uploadSuccess) {

                        $autorizacao->status = 'erro';
                        $autorizacao->messagem_erro = (string) $response->getBody();
                        $autorizacao->save();

                        Log::error('User created but failed to upload image.', ['UserID' => $data['UserID']]);
                        return response()->json([
                            'message' => 'Erro ao criar usuário'
                        ], 404);
                    }

                    $autorizacao->status = 'autorizado';              
                    $autorizacao->messagem_erro = '';
                    $autorizacao->user_id_externo = $data['UserID'];
                    $autorizacao->save();
                }
            


                return response()->json([
                    'message' => 'Usuário criado com sucesso'
                ], 201);

            } else {
                Log::error('Failed to create user. HTTP Status Code: ' . $statusCode);
                return response()->json([
                    'message' => 'Erro ao criar usuário' 
                ], 404);
            }
        } catch (RequestException $e) {
            // Logar erros de requisição
            Log::error('RequestException while creating user:', ['message' => $e->getMessage()]);
            return response()->json([
                'message' => 'Erro ao criar usuário'
            ], 404);
        } catch (\Exception $e) {
            // Logar quaisquer outros erros
            Log::error('Exception while creating user:', ['message' => $e->getMessage()]);
            return response()->json([
                'message' => 'Erro ao criar usuário'
            ], 404);
        }
      

        // Aqui você pode adicionar chamadas para APIs externas específicas
        // do controle de acesso se necessário
        
        return response()->json([
            'message' => 'Autorização de controle de acesso processada',
            'autorizacao' => $autorizacao
        ], 201);
    }

    public function deleteUser($dispositivo, $model_selected)
    {
        try {

            $data = [
                'username' => $dispositivo->username,
                'password' => $dispositivo->password,
                'base_uri' => $dispositivo->ip,
                'deviceId' => $dispositivo->identificador_unico,
                'storeId' => $dispositivo->store_id,
                'UserID' => $model_selected->autorizacoeDispositivoByDispositivo($dispositivo)->user_id_externo,
            ];

            
            $this->client = new Client([
                'base_uri' => rtrim($data['base_uri'], '/') . '/', // Base URI para facilitar
                'timeout'  => 10.0, // Tempo máximo de espera
            ]);


            // Construir a URL com os parâmetros
            $url = 'cgi-bin/AccessUser.cgi';
            $query = [
                'action'        => 'removeMulti',
                'name'          => 'AccessControlCard',
                'UserIDList'    => [
                    $data['UserID']
                ],
            ];

           
            // Enviar a requisição GET para criar o usuário
            $response = $this->client->request('GET', $url, [
                'query' => $query,
                'auth'  => [$data['username'], $data['password'], 'digest'],
            ]);
 
         
            // Obter o corpo da resposta
            $responseBody = $response->getBody()->getContents();
          

            // Logar a resposta para depuração
            // Log::info('Delete User Response:', ['body' => $responseBody]);

            $statusCode = $response->getStatusCode();
            // Log::info('Delete User Response:', ['statuscode' => $statusCode]);
            // Verificar se a deleção foi bem-sucedida
            if ($statusCode == 200) {
                //delete controliduser

                $autorizacao = AutorizacaoDispositivo::where('user_id_externo', $data['UserID'])
                ->where('identificador_dispositivo', $data['deviceId'])
                ->first();

                $autorizacao->status = 'bloqueado';
                $autorizacao->save();

             
                return response()->json([
                    'message' => 'Usuário deletado com sucesso'
                ], 200);
                
            } else {
                Log::error('Failed to delete user.', ['body' => $responseBody]);
                return null;
            }
        } catch (RequestException $e) {
            // Logar erros de requisição
            Log::error('RequestException while deleting user:', ['message' => $e->getMessage()]);
            return null;
        } catch (\Exception $e) {
            // Logar quaisquer outros erros
            Log::error('Exception while deleting user:', ['message' => $e->getMessage()]);
            return null;
        }
    }

     /**
     * Faz o upload de uma imagem para o usuário criado.
     *
     * @param string $userId ID do usuário para associar a imagem.
     * @param string $imageBase64 Imagem em formato base64.
     * @return mixed Resultado do upload da imagem.
     */
    public function uploadImage(array $data)
    {
        try {
            // Construir a URL para upload de imagem
            $url = 'cgi-bin/FaceInfoManager.cgi?action=add'; 

            // Construir o corpo da requisição
            $body = [
                'UserID' => (string)$data['UserID'],
                'Info' => [
                    // 'UserName' => 'Alexandre16', // Ajuste conforme necessário
                    'PhotoData' => [$data['PhotoData']]
                ],
            ];

            // Enviar a requisição POST para upload de imagem
            $response = $this->client->request('POST', $url, [
                'headers' => [
                    'Content-Type' => 'application/json',

                ],
                'json' => $body,
                'auth'  => [$data['username'], $data['password'], 'digest'],
            ]);
            
            // Obter o corpo da resposta
            $responseBody = $response->getBody()->getContents();
            // Logar a resposta para depuração
         
            // Logar a resposta para depuração
            // Log::info('Upload Image Response:', ['body' => $responseBody]);

            $statusCode = $response->getStatusCode();

            // Verificar se o upload foi bem-sucedido
            if ($statusCode == 200) { // Exemplo de verificação
                return $responseBody;
            } else {
                Log::error('Failed to upload image.', ['body' => $responseBody]);
                return null;
            }
        } catch (RequestException $e) {
            // Logar erros de requisição
            Log::error('RequestException while uploading image:', ['message' => $e->getMessage()]);
            return null;
        } catch (\Exception $e) {
            // Logar quaisquer outros erros
            Log::error('Exception while uploading image:', ['message' => $e->getMessage()]);
            return null;
        }
    }

    public function getMaxUserId(array $data)
    {
        try {
            $this->client = new Client([
                'base_uri' => rtrim($data['base_uri'], '/') . '/', // Base URI para facilitar
                'timeout'  => 10.0, // Tempo máximo de espera
            ]);


            // Construir a URL com os parâmetros
            $url = 'cgi-bin/recordFinder.cgi';
            $query = [
                'action'        => 'doSeekFind',
                'name'          => 'AccessControlCard',
                'count'    => 6000,
            ];


            // Enviar a requisição GET para criar o usuário
            $response = $this->client->request('GET', $url, [
                'query' => $query,
                'auth'  => [$data['username'], $data['password'], 'digest'],
            ]);


            // Obter o corpo da resposta
            $responseBody = $response->getBody()->getContents();
          
            // Logar a resposta para depuração
            // Log::info('  Response:', ['body' => $responseBody]);

            $statusCode = $response->getStatusCode();

            // Verificar se a requisição foi bem-sucedida
            if ($statusCode == 200) {
                $records = $this->processarRegistros($responseBody);

                // Encontrar o UserID mais alto
                $highestUserID = 0;

                foreach ($records as $record) {
                    if (isset($record['UserID']) && $record['UserID'] > $highestUserID) {

                        $highestUserID = $record['UserID'];
                    }
                }
              
                // Retornar os registros e o UserID mais alto conforme a necessidade
                return $highestUserID;
            } else {
                Log::error('Falha ao buscar registros.', ['status_code' => $statusCode, 'body' => $responseBody]);
                return null;
            }
        } catch (RequestException $e) {
            // Logar erros de requisição
            Log::error('RequestException while getMaxUserId:', ['message' => $e->getMessage()]);
            return null;
        } catch (\Exception $e) {
            // Logar quaisquer outros erros
            Log::error('Exception while getMaxUserId:', ['message' => $e->getMessage()]);
            return null;
        }
        return response()->json(['max_user_id' => $maxUserId]);
    }

    /**
     * Processa o corpo da resposta para extrair os registros.
     *
     * @param string $responseBody O corpo da resposta da API.
     * @return array O array de registros processados.
     */
    private function processarRegistros(string $responseBody): array
    {
        $records = [];
        $recordPattern = '/records\[(\d+)\]\.(\w+)(?:\[(\d+)\])?=(.*)/';
        preg_match_all($recordPattern, $responseBody, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $index = (int)$match[1];
            $key = $match[2]; // Chave corretamente capturada
            $subIndex = isset($match[3]) ? $match[3] : null;
            $value = trim($match[4]);

            // Se a chave estiver vazia, ignore este registro
            if (empty($key)) {
                continue;
            }

            // Inicialize o array para o registro atual se ainda não existir
            if (!isset($records[$index])) {
                $records[$index] = [];
            }

            // Se houver um subíndice (como Doors[0]), ajusta a chave
            // if ($subIndex !== null) {
            //     dd($subIndex);
            //     $records[$index][$key][$subIndex] = $this->converterValor($value);
            // } else {
            //     // Atribui o valor convertido à chave correspondente
            //     $records[$index][$key] = $this->converterValor($value);
            // }

            $records[$index][$key] = $this->converterValor($value);
        }

        return $records;
    }

     /**
     * Converte os valores para tipos apropriados.
     *
     * @param string $valor O valor a ser convertido.
     * @return mixed O valor convertido.
     */
    private function converterValor(string $valor): mixed
    {
        if (is_numeric($valor)) {
            return (strpos($valor, '.') !== false) ? (float)$valor : (int)$valor;
        }

        if (strcasecmp($valor, 'true') === 0) {
            return true;
        }

        if (strcasecmp($valor, 'false') === 0) {
            return false;
        }

        // Se o valor estiver vazio, retorna null
        return empty($valor) ? null : $valor;
    }


    public function getReportByDateInterval($reportDeviceDTO, Dispositivo $dispositivo)
    {
        try {

            $data = [
                'username' => $dispositivo->username,
                'password' => $dispositivo->password,
                'base_uri' => $dispositivo->ip,
                'deviceId' => $dispositivo->identificador_unico,
                'storeId' => $dispositivo->store_id,
            ];

            $client = new Client([
                'base_uri' => rtrim($data['base_uri'], '/') . '/', // Base URI para facilitar
                'timeout'  => 10.0, // Tempo máximo de espera
            ]);

            // Construir a URL com os parâmetros
            $url = 'cgi-bin/recordFinder.cgi';
            $query = [
                'action'        => 'find',
                'name'          => 'AccessControlCardRec',
                'StartTime'    => $reportDeviceDTO->date_start_time,
                'EndTime'    => $reportDeviceDTO->date_end_time
            ];

            // Enviar a requisição GET para criar o usuário
            $response = $client->request('GET', $url, [
                'query' => $query,
                'auth'  => [$data['username'], $data['password'], 'digest'],
            ]);

            $metodosDeAcesso = [
                0 => 'senha',
                1 => 'cartão de acesso',
                2 => 'cartão de acesso e senha',
                3 => 'senha e cartão de acesso',
                4 => 'destravamento remoto',
                5 => 'botão de saída',
                6 => 'impressão digital',
                7 => 'senha, cartão de acesso e impressão digital',
                8 => 'senha e impressão digital',
                9 => 'cartão de acesso e impressão digital',
                10 => 'reservado',
                11 => 'usuário de acesso múltiplo',
                12 => 'chave',
                13 => 'senha de pânico',
                14 => 'QR code, local',
                15 => 'reconhecimento facial, local',
                16 => 'reservado',
                17 => 'cartão de ID',
                18 => 'face e cartão de ID',
                19 => 'Bluetooth',
                20 => 'senha personalizada',
                21 => 'UserId e senha',
                22 => 'face e senha',
                23 => 'impressão digital e senha',
                24 => 'impressão digital e face',
                25 => 'cartão de acesso e face',
                26 => 'face ou senha',
                27 => 'impressão digital ou senha',
                28 => 'impressão digital ou face',
                29 => 'cartão de acesso ou face',
                30 => 'cartão de acesso ou impressão digital',
                31 => 'impressão digital, face e senha',
                32 => 'cartão de acesso, face e senha',
                33 => 'cartão de acesso, impressão digital e senha',
                34 => 'cartão de acesso, impressão digital e face',
                35 => 'impressão digital ou face ou senha',
                36 => 'cartão de acesso ou face ou senha',
                37 => 'cartão de acesso ou impressão digital ou face',
                38 => 'cartão de acesso, impressão digital, face e senha',
                39 => 'cartão de acesso ou impressão digital ou face ou senha',
                40 => 'cartão de ID e face ou cartão de ID ou face',
                41 => 'cartão de ID ou QR code ou face',
                42 => 'DTMF (SIP INFO, RFC2833, INBAND)',
                43 => 'QR code, remoto',
                44 => 'reconhecimento facial, remoto',
                45 => 'cartão de ID (com impressão digital no ID)',
                46 => 'senha temporária',
                47 => 'código de saúde'
            ];
            

            // Obter o corpo da resposta
            $responseBody = $response->getBody()->getContents();
            $statusCode = $response->getStatusCode();

            // Verificar se a deleção foi bem-sucedida
            if ($statusCode == 200) {
                $arrayLogsIntelbras = self::convertTextToArray($responseBody);

                $accessLogs = [];
               

                foreach ($arrayLogsIntelbras as $logsIntelbras) {
                    $newLog = [];
                    $newLog['time'] = (string)  \Carbon\Carbon::createFromTimestamp($logsIntelbras['CreateTime'])->format('d/m/Y H:i:s');
                    $newLog['event'] = (string)  $logsIntelbras['Status'] == 1 ? "Autorizado" : "Não Autorizado";
                    $newLog['method'] = $metodosDeAcesso[$logsIntelbras['Method']];
                    $newLog['device_id'] = (string)  $dispositivo->identificador_unico;
                    $newLog['user_id'] = (string) empty($logsIntelbras['UserID']) ? 0 : $logsIntelbras['UserID'];
                    $newLog['user_name'] = (string) $logsIntelbras['CardName'] ? $logsIntelbras['CardName'] : "Não identificado";

                    $accessLogs[] = $newLog;
                }

                return $accessLogs;
            } else {

                return false;
            }
        } catch (RequestException $e) {
            // Logar erros de requisição
            Log::error('Error RequestException while get log device' . $e->getMessage());

            return false;
        } catch (\Exception $e) {
            // Logar quaisquer outros erros
            Log::error('Error Exception while get log device' . $e->getMessage());

            return false;
        }
    }


    public function deleteAllUser($data)
    {
        try {

            $client = new Client([
                'base_uri' => rtrim($data['base_uri'], '/') . '/', // Base URI para facilitar
                'timeout'  => 10.0, // Tempo máximo de espera
            ]);

            // Construir a URL com os parâmetros
            $url = 'cgi-bin/AccessUser.cgi?action=removeAll';
            $query = [
                'action' => 'removeAll',         
            ];

            // Enviar a requisição GET para criar o usuário
            $response = $client->request('GET', $url, [
                'query' => $query,
                'auth'  => [$data['username'], $data['password'], 'digest'],
            ]);

            // Obter o corpo da resposta
            $responseBody = $response->getBody()->getContents();
            $statusCode = $response->getStatusCode();

            // Verificar se a deleção foi bem-sucedida
            if ($statusCode == 200) {

                
                return true;

            } else {
                Log::error('Error Delete all users response: ' . $responseBody);
                return false;
            }
        } catch (RequestException $e) {
            // Logar erros de requisição
            Log::error('Error RequestException while get log device' . $e->getMessage());

            return false;
        } catch (\Exception $e) {
            // Logar quaisquer outros erros
            Log::error('Error Exception while get log device' . $e->getMessage());

            return false;
        }
    }


    public function convertTextToArray($texto)
    {
        // Dividir o texto em linhas
        $linhas = explode("\n", trim($texto));

        // Inicializar o array de registros
        $registros = array();

        // Expressão regular para extrair dados
        $padrao = '/^records\[(\d+)\]\.(\w+)=(.*)$/';

        foreach ($linhas as $linha) {
            $linha = trim($linha);
            if (strpos($linha, 'found=') === 0) {
                $found = intval(substr($linha, strlen('found=')));
            } else {
                if (preg_match($padrao, $linha, $matches)) {
                    $idx = intval($matches[1]);
                    $chave = $matches[2];
                    $valor = $matches[3];

                    if (!isset($registros[$idx])) {
                        $registros[$idx] = array();
                    }
                    $registros[$idx][$chave] = $valor;
                }
            }
        }

        return $registros;
    }

    /**
     * Test the connection to the Intelbras device.
     *
     * @param Request $request The request containing connection parameters.
     * @return bool|int Returns the HTTP status code on success, or false on failure.
     */
    public function testConnection(Request $request): bool | int 
    {
        try {
           

            $this->client = new Client([
                'base_uri' => rtrim($request->server_ip, '/') . '/',
                'timeout'  => 10.0,
            ]);

            $url = 'cgi-bin/magicBox.cgi';
            $query = [
                'action' => 'getSerialNo',
                // 'name'   => 'AccessControlCard',
            ];
            
            $response = $this->client->request('GET', $url, [
                'query' => $query,
                'auth'  => [$request->username, $request->password, 'digest'],
                'http_errors' => false
            ]);
            
            
            return $response->getStatusCode();

        } catch (\Exception $e) {
            Log::error('Erro ao testar conexão com dispositivo Intelbras: ' . $e->getMessage());
            return false;
        } 
    }
} 