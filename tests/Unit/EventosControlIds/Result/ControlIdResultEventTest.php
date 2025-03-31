<?php
namespace Tests\Unit\EventosControlIds\Result;

use Mockery;
use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;
use App\Models\Torre;
use App\Models\Morador;
use App\Models\Visitante;
use App\Models\Condominio;
use App\Models\Apartamento;
use App\Models\Dispositivo;
use Illuminate\Support\Str;
use App\Models\ControlIdJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\ControlIdJobService;
use Illuminate\Support\Facades\Storage;
use App\Repositories\ControlIdJobRepository;
use App\Strategies\ControlID\ControlIdStrategy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Repositories\Interfaces\ControlIdJobRepositoryInterface;
use Intervention\Image\Facades\Image;

class ControlIdResultEventTest extends TestCase
{
    use RefreshDatabase;

    protected ControlIdStrategy $strategy;
    protected Dispositivo $dispositivo;
    protected Dispositivo $dispositivoInativo;
    protected Morador $morador;
    protected Visitante $visitante;
    protected Apartamento $apartamento;
    protected Torre $torre;
    protected Condominio $condominio;
    protected User $user;
    private ControlIdJobRepositoryInterface $jobRepository;

   


    protected function setUp(): void
    {
        parent::setUp();

        // Criar instância da strategy
        $this->strategy = new ControlIdStrategy();
        $this->jobRepository = app(ControlIdJobRepositoryInterface::class);

        Condominio::factory()->create([
            'id' => '1',
            'nome' => 'Condominio Teste',
        ]);

        Torre::factory()->create([
            'id' => '1',
            'condominio_id' => '1',
            'nome' => 'Torre Teste',
            'tenant_id' => '1',
        ]);

        // Criar dispositivo de teste
        $this->dispositivo = Dispositivo::factory()->create([
            'tenant_id' => '1',
            'fabricante' => 'controlid',
            'identificador_unico' => '1234567890',
            'condominio_id' => '1',
            'ativo' => true,
        ]);

        // Criar dispositivo de teste
        $this->dispositivoInativo = Dispositivo::factory()->create([
            'tenant_id' => '1',
            'fabricante' => 'controlid',
            'identificador_unico' => '1234567891',
            'condominio_id' => '1',
            'ativo' => false,
        ]);

        $this->apartamento = Apartamento::factory()->create([
            'torre_id' => '1',
            'numero' => '101',
        ]);

        $this->user = User::factory()->create([
            'tenant_id' => '1',
            'name' => 'Teste',
        ]);

        // Criar morador e visitante para teste
        $this->morador = Morador::factory()->create([
            'tenant_id' => 1,
            'user_id' => $this->user->id,
            'apartamento_id' => $this->apartamento->id,
        ]);


        $this->visitante = Visitante::factory()->create([
            'tenant_id' => 1,
            'user_id' => $this->user->id,
            'apartamento_id' => $this->apartamento->id,
            'morador_responsavel_id' => $this->morador->id,
            'data_validade_inicio' => now(),
            'data_validade_fim' => now()->addDays(7),
            'ativo' => true,
            'dias_semana' => implode(',', ['seg', 'ter', 'qua', 'qui', 'sex', 'sab', 'dom']), // Convertendo para string
            'horario_inicio' => now(),
            'horario_fim' => now()->addDays(7)
        ]);
    }

    /** @test */
    public function case_1_result_event_tem_job_create_user_morador()
    {
        // |--------------------- CRIADO JOB PARA AUTORIZAR ---------------------|
        $this->strategy->createJobByEndpoint($this->dispositivo, $this->morador, 'create_user_morador');
        
         // Verificar se foi criado um registro na tabela de autorizações
        $this->assertDatabaseHas('controlid_jobs', [ 
            'identificador_dispositivo' => $this->dispositivo->identificador_unico,
            'status' => 0,
            'endpoint' => 'create_user_morador',
        ]);

  
        // |--------------------- passo 2 ---------------------|
        // |--------------------- EVENTO DO CONTROLID PUSH CHEGA PARA PERGUNTAR PRA GENTE SE TEM TAREFA DO DISPOSITIVO ---------------------|
      


        $request = new Request();
        $uuid = Str::uuid()->toString();
        $request->merge([
            'deviceId' => $this->dispositivo->identificador_unico,
            'uuid' => $uuid,
            'is_test' => false,
        ]);

        $deviceId = $request->input('deviceId');
        $uuid = $request->input('uuid');

       
        $pushService = new ControlIdJobService($this->jobRepository);
        $response = $pushService->processPush($deviceId, $uuid);

        $this->assertEquals([
            "verb" => "POST",
            "endpoint" => "create_objects",
            "body" => [
                "object" => "users",
                "values" => [
                    0 => [
                        "name" => $this->morador->user->name,
                        "registration" => "",
                        "password" => "",
                        "salt" => ""
                    ]
                ]
            ]
        ], $response);

        // REQUEST RESULT DO CONTROLID
        $request = new Request();
        $request->merge([
            'response' => '{"ids":[1003115]}',
            'deviceId' => '4408801109214683',
            'uuid' => $uuid,
            'endpoint' => 'create_objects',
        ]);
    
        $controlidService = new ControlIdJobService($this->jobRepository);

        $response = $controlidService->processResult($request);        
 
        $this->assertEquals(200, $response->getStatusCode());

        // Verificar se o job foi processado com sucesso
        $this->assertDatabaseHas('controlid_jobs', [
            'identificador_dispositivo' => $this->dispositivo->identificador_unico,
            'status' => 1,
            'user_able_type' => get_class($this->morador),
            'user_able_id' => $this->morador->id,
            'uuid' => $uuid,
            'endpoint' => 'create_user_morador',
        ]);

        //tem que ter uma autorizacao_dispositivo com o user_id_externo 1003115
        $this->assertDatabaseHas('autorizacao_dispositivos', [
            'user_id_externo' => 1003115,
            'authorizable_type' => get_class($this->morador),
            'authorizable_id' => $this->morador->id,
            'identificador_dispositivo' => $this->dispositivo->identificador_unico,
            'status' => 'processando',
        ]);

        // Verificar se o job de adicionar o grupo ao morador foi criado
        $this->assertDatabaseHas('controlid_jobs', [
            'identificador_dispositivo' => $this->dispositivo->identificador_unico,
            'user_able_type' => get_class($this->morador),
            'user_able_id' => $this->morador->id,
            'endpoint' => 'add_group_user',           
            'status' => 0,
        ]);

        // criar mais um novo request push para adicionar o grupo ao morador
        $request = new Request();
        $uuid = Str::uuid()->toString();
        $request->merge([
            'deviceId' => $this->dispositivo->identificador_unico,
            'uuid' => $uuid,
            'is_test' => false,
        ]);

        $deviceId = $request->input('deviceId');
        $uuid = $request->input('uuid');

        $pushService = new ControlIdJobService($this->jobRepository);
        $response = $pushService->processPush($deviceId, $uuid);

        $this->assertEquals([
            "verb" => "POST",
            "endpoint" => "create_objects",
            "body" => [
                "object" => "user_groups",
                "fields" => ["user_id", "group_id"],
                "values" => [
                    [
                        "user_id" => (int) $this->morador->autorizacoesDispositivos->first()->user_id_externo,
                        "group_id" => 1,
                    ]
                ]
            ]
        ], $response);
        
        
        // REQUEST RESULT DO CONTROLID COM O RESULTADO DO PUSH
        $request = new Request();
        $request->merge([
            'response' => '{"ids":[1003115]}',
            'deviceId' => $this->dispositivo->identificador_unico,
            'uuid' => $uuid,
            'endpoint' => 'add_group_user',
        ]);

        $controlidService = new ControlIdJobService($this->jobRepository);
        $response = $controlidService->processResult($request);        
 
        $this->assertEquals(200, $response->getStatusCode());
        

        $this->assertDatabaseHas('controlid_jobs', [
            'identificador_dispositivo' => $this->dispositivo->identificador_unico,
            'user_able_type' => get_class($this->morador),
            'user_able_id' => $this->morador->id,
            'endpoint' => 'add_group_user',
            'uuid' => $uuid,
            'status' => 1,
        ]); 
        

        // Verificar se o job de adicionar o grupo ao morador foi criado
        $this->assertDatabaseHas('controlid_jobs', [
            'identificador_dispositivo' => $this->dispositivo->identificador_unico,
            'user_able_type' => get_class($this->morador),
            'user_able_id' => $this->morador->id,
            'endpoint' => 'upload_image_user',           
            'status' => 0,
        ]);

        // criar mais um novo request push para adicionar a imagem do morador
        $request = new Request();
        $uuid = Str::uuid()->toString();
        $request->merge([
            'deviceId' => $this->dispositivo->identificador_unico,
            'uuid' => $uuid,
            'is_test' => false,
        ]);

        $deviceId = $request->input('deviceId');
        $uuid = $request->input('uuid');

        $pushService = new ControlIdJobService($this->jobRepository);
        $this->morador->user->foto = 'moradores/4JmJt6WxNxKEf4HYMkS5Dwj90CsXCxhgLvLVLX88.jpg';
        $this->morador->user->save();
        $response = $pushService->processPush($deviceId, $uuid);
     
        $base64Content = base64_encode(file_get_contents(Storage::path('public/moradores/4JmJt6WxNxKEf4HYMkS5Dwj90CsXCxhgLvLVLX88.jpg')));   
    
        $this->assertEquals([
            "verb" => "POST",
            "endpoint" => "user_set_image",
            "body" => $base64Content,
            "queryString" => http_build_query([
                'user_id' => $this->morador->autorizacoesDispositivos->first()->user_id_externo,
                'match' => 1,
                'timestamp' => time()
            ]),
            'contentType' => 'application/octet-stream'
        ], $response);
        
        
        // REQUEST RESULT DO CONTROLID COM O RESULTADO DO PUSH
        $request = new Request();
        $request->merge([
            'response' => '{"ids":[1003115]}',
            'deviceId' => $this->dispositivo->identificador_unico,
            'uuid' => $uuid,
            'endpoint' => 'user_set_image',
        ]);

        $controlidService = new ControlIdJobService($this->jobRepository);
        $response = $controlidService->processResult($request);        
 
        $this->assertEquals(200, $response->getStatusCode());

        $this->assertDatabaseHas('controlid_jobs', [
            'identificador_dispositivo' => $this->dispositivo->identificador_unico,
            'user_able_type' => get_class($this->morador),
            'user_able_id' => $this->morador->id,
            'endpoint' => 'upload_image_user',
            'uuid' => $uuid,
            'status' => 1,
        ]);

        $this->assertDatabaseHas('autorizacao_dispositivos', [
            'user_id_externo' => $this->morador->autorizacoesDispositivos->first()->user_id_externo,
            'authorizable_type' => get_class($this->morador),
            'authorizable_id' => $this->morador->id,
            'identificador_dispositivo' => $this->dispositivo->identificador_unico,
            'status' => 'autorizado',
        ]);
    }

    
}
