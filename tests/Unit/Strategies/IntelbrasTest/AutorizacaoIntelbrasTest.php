<?php

namespace Tests\Unit\Strategies\IntelbrasTest;

use Tests\TestCase;
use App\Models\User;
use App\Models\Torre;
use App\Models\Morador;
use App\Models\Visitante;
use App\Models\Condominio;
use App\Models\Apartamento;
use App\Models\Dispositivo;
use App\Models\ControlIdJob;
use Illuminate\Http\Request;
use App\Models\AutorizacaoDispositivo;
use App\Strategies\AutorizacaoStrategy;
use App\Factories\AutorizacaoStrategyFactory;
use App\Http\Controllers\AutorizacaoController;
use App\Strategies\ControlID\ControlIdStrategy;
use App\Strategies\Intelbras\IntelbrasStrategy;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AutorizacaoIntelbrasTest extends TestCase
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

    protected function setUp(): void
    {
        parent::setUp();

        // Criar instância da strategy
        $this->strategy = new ControlIdStrategy();

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
            'fabricante' => 'intelbras',
            'username' => 'admin',
            'password' => 'admin123',
            'ip' => '177.8.126.80:1025',
            'identificador_unico' => 'WF0M24015060Y',
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
            'foto' => 'moradores\4JmJt6WxNxKEf4HYMkS5Dwj90CsXCxhgLvLVLX88.jpg',
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
    public function pode_criar_e_autorizar_morador()
    {
   
        $request = request()->merge([
            'dispositivo_id' => "{$this->dispositivo->id}",
            'morador_id' => "{$this->morador->id}",
        ]);

       // Encontra o dispositivo e seu tipo
       $dispositivo = Dispositivo::findOrFail($request->dispositivo_id);
       $this->assertEquals('intelbras', $dispositivo->fabricante);
       // Cria a estratégia apropriada
       $strategy = AutorizacaoStrategyFactory::criarStrategy($dispositivo->fabricante);
       $this->assertEquals(IntelbrasStrategy::class, get_class($strategy));

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

       $this->assertEquals(Morador::class, get_class($model_selected));
       $this->assertEquals($endpoint, 'create_user_morador');
       // Executar a autorização
       $response = $strategy->validar($dispositivo, $model_selected);
       $this->assertEquals(200, $response->getStatusCode(), $response->getContent());

       // Executar a autorização
       $response = $strategy->createJobByEndpoint($dispositivo, $model_selected, $endpoint);

       $this->assertEquals(201, $response->getStatusCode(), $response->getContent());
    }

    /** @test */
    public function pode_criar_e_autorizar_visitante()
    {
        $request = request()->merge([
            'dispositivo_id' => "{$this->dispositivo->id}",
            'visitante_id' => "{$this->visitante->id}",
        ]);

       // Encontra o dispositivo e seu tipo
       $dispositivo = Dispositivo::findOrFail($request->dispositivo_id);
       $this->assertEquals('intelbras', $dispositivo->fabricante);
       // Cria a estratégia apropriada
       $strategy = AutorizacaoStrategyFactory::criarStrategy($dispositivo->fabricante);
       $this->assertEquals(IntelbrasStrategy::class, get_class($strategy));

       $endpoint = 'create_user_visitante';

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

       $this->assertEquals(Visitante::class, get_class($model_selected));
       $this->assertEquals($endpoint, 'create_user_visitante');
       // Executar a autorização
       $response = $strategy->validar($dispositivo, $model_selected);
       $this->assertEquals(200, $response->getStatusCode(), $response->getContent());

       // Executar a autorização
       $response = $strategy->createJobByEndpoint($dispositivo, $model_selected, $endpoint);

       $this->assertEquals(201, $response->getStatusCode(), $response->getContent());
    
       $this->assertDatabaseHas('autorizacao_dispositivos', [
        'identificador_dispositivo' => $this->dispositivo->identificador_unico,
        'authorizable_type' => get_class($this->visitante),
        'authorizable_id' => $this->visitante->id,
        'status' => 'autorizado',
       ]);
    }

    /** @test */
    public function nao_pode_criar_job_para_dispositivo_inativo()
    {
        // Executar a validação
        $response = $this->strategy->validar($this->dispositivoInativo, $this->morador);
        $this->assertEquals(404, $response->getStatusCode(), $response->getContent());


        // // Verificar se a resposta é um JsonResponse
        // $this->assertInstanceOf(\Illuminate\Http\JsonResponse::class, $response);

        // // Verificar se foi criado um registro na tabela de autorizações sem usuário
        // $this->assertDatabaseMissing('controlid_jobs', [
        //     'identificador_dispositivo' => $this->dispositivoInativo->identificador_unico,    
        //     'user_able_type' => get_class($this->morador),
        //     'user_able_id' => $this->morador->id,
        // ]);
    }

    /** @test */
    public function nao_pode_criar_job_para_se_ja_existir_job_pendente()
    {
        $response = $this->strategy->createJobByEndpoint($this->dispositivo, $this->morador, 'create_user_morador');
        $this->assertEquals(201, $response->getStatusCode(), $response->getContent());

        // Verificar se foi criado um registro na tabela de autorizações
        $this->assertDatabaseHas('controlid_jobs', [
            'identificador_dispositivo' => $this->dispositivo->identificador_unico,
            'user_able_type' => get_class($this->morador),
            'user_able_id' => $this->morador->id,
            'endpoint' => 'create_user_morador',
        ]);

        // Verificar se foi criado um registro na tabela de autorizações
        // $this->assertDatabaseHas('autorizacao_dispositivos', [
        //     'identificador_dispositivo' => $this->dispositivo->identificador_unico,
        //     'authorizable_type' => get_class($this->morador),
        //     'authorizable_id' => $this->morador->id,
        //     'status' => 'processando',
        // ]);

        //simulação de uma nova tentativa de autorização sem a outra processar
        $response = $this->strategy->createJobByEndpoint($this->dispositivo, $this->morador, 'create_user_morador');
        $this->assertEquals(429, $response->getStatusCode(), $response->getContent());
        // Verificar se a resposta é um JsonResponse
        $this->assertInstanceOf(\Illuminate\Http\JsonResponse::class, $response);

        // Verificar se o job foi criado
        // count jobs
        $this->assertEquals(1, ControlIdJob::where([
            'identificador_dispositivo' => $this->dispositivo->identificador_unico,
            'user_able_type' => get_class($this->morador),
            'user_able_id' => $this->morador->id,
            'endpoint' => 'create_user_morador',
        ])->count());
        
        //coutn autorizacao
        // $this->assertEquals(1, AutorizacaoDispositivo::where([
        //     'identificador_dispositivo' => $this->dispositivo->identificador_unico,
        //     'authorizable_type' => get_class($this->morador),
        //     'authorizable_id' => $this->morador->id,
        //     'status' => 'processando',
        // ])->count());

    }
}
