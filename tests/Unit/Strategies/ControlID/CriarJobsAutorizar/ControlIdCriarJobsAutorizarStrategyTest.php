<?php

namespace Tests\Unit\Strategies\ControlID\CriarJobsAutorizar;

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
use App\Strategies\ControlID\ControlIdStrategy;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ControlIdCriarJobsAutorizarStrategyTest extends TestCase
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
    public function pode_criar_job_para_morador()
    {
        // Executar a autorização
        $response = $this->strategy->validar($this->dispositivo, $this->morador);
        $this->assertEquals(200, $response->getStatusCode(), $response->getContent());

        // Executar a autorização
        $response = $this->strategy->createJobByEndpoint($this->dispositivo, $this->morador, 'create_user_morador');

        // Verificar se a resposta é um JsonResponse
        $this->assertInstanceOf(\Illuminate\Http\JsonResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());

        // Verificar se foi criado um registro na tabela de autorizações
        $this->assertDatabaseHas('controlid_jobs', [
            'identificador_dispositivo' => $this->dispositivo->identificador_unico,
            'user_able_type' => get_class($this->morador),
            'user_able_id' => $this->morador->id,
            'endpoint' => 'create_user_morador',
        ]);

        // Verificar se foi criado um registro na tabela de autorizações
        // $this->assertDatabaseHas('autorizacao_dispositivos', [
        //     'identificador_dispositivo' => $this->dispositivo->id,
        //     'user_able_type' => get_class($this->morador),
        //     'user_able_id' => $this->morador->id,
        // ]);

        // Verificar se foi criado um registro na tabela de autorizações
        // $this->assertDatabaseHas('autorizacao_dispositivos', [
        //     'identificador_dispositivo' => $this->dispositivo->identificador_unico,
        //     'authorizable_type' => get_class($this->morador),
        //     'authorizable_id' => $this->morador->id,
        //     'status' => 'processando',
        // ]);
    }

    /** @test */
    public function pode_criar_job_para_criar_visitante()
    {
        // Executar a autorização
        $response = $this->strategy->validar($this->dispositivo, $this->morador);
        $this->assertEquals(200, $response->getStatusCode(), $response->getContent());

        // Executar a autorização
        $response = $this->strategy->createJobByEndpoint($this->dispositivo, $this->visitante, 'create_user_visitante');

        // Verificar se a resposta é um JsonResponse
        $this->assertInstanceOf(\Illuminate\Http\JsonResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());

        // Verificar se foi criado um registro na tabela de autorizações
        $this->assertDatabaseHas('controlid_jobs', [
            'identificador_dispositivo' => $this->dispositivo->identificador_unico,
            'user_able_type' => get_class($this->visitante),
            'user_able_id' => $this->visitante->id,
            'endpoint' => 'create_user_visitante',
        ]);

        // Verificar se foi criado um registro na tabela de autorizações
        // $this->assertDatabaseHas('autorizacao_dispositivos', [
        //     'identificador_dispositivo' => $this->dispositivo->identificador_unico,
        //     'authorizable_type' => get_class($this->visitante),
        //     'authorizable_id' => $this->morador->id,
        //     'status' => 'processando',
        // ]);
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
