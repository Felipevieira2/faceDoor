<?php

namespace Tests\Unit\EventosControlIds\Push;

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
use App\Strategies\ControlID\ControlIdStrategy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Repositories\Interfaces\ControlIdJobRepositoryInterface;

class ControlIdPushEventTest extends TestCase
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
    public function case_1_push_event_tem_job_create_user_morador()
    {
        // |--------------------- CRIADO JOB PARA AUTORIZAR ---------------------|
        $this->strategy->createJobByEndpoint($this->dispositivo, $this->morador, 'create_user_morador');
        $job = ControlIdJob::where('identificador_dispositivo', $this->dispositivo->identificador_unico)->first();

        // |--------------------- passo 2 ---------------------|
        // |--------------------- EVENTO DO CONTROLID PUSH CHEGA PARA PERGUNTAR PRA GENTE SE TEM TAREFA DO DISPOSITIVO ---------------------|

        $request = new Request();
        $request->merge([
            'deviceId' => $this->dispositivo->identificador_unico,
            'is_test' => false,
            'uuid' => Str::uuid(),
        ]);

        Log::info('handlePush', $request->all());

        $deviceId = $request->input('deviceId');
        $uuid = $request->input('uuid');

        // Mock do repositório de jobs
        $jobRepository = Mockery::mock(ControlIdJobRepositoryInterface::class);

        // Configure o comportamento esperado do mock
        $jobRepository->shouldReceive('getPendingJobByDeviceId')
            ->with($deviceId)
            ->once()
            ->andReturn($job); // Ou retorne um job mockado se necessário

        $pushService = new ControlIdJobService($jobRepository);
        $response = $pushService->processPush($deviceId, $uuid);

        $this->assertEquals([
            "verb" => "POST",
            "endpoint" => "create_objects",
            "body" => [
                "object" => "users",
                "values" => [
                    0 => [
                        "name" => $job->user_able->user->name,
                        "registration" => "",
                        "password" => "",
                        "salt" => ""
                    ]
                ]
            ]
        ], $response);
        $this->assertNotEquals(3, $job->status);
    }

    /** @test */
    public function case_2_push_event_nao_tem_job_para_processar()
    {

        // |--------------------- EVENTO DO CONTROLID PUSH CHEGA PARA PERGUNTAR PRA GENTE SE TEM TAREFA DO DISPOSITIVO ---------------------|

        $request = new Request();
        $request = new Request();
        $request->merge([
            'deviceId' => $this->dispositivo->identificador_unico,
            'is_test' => false,
            'uuid' => Str::uuid(),
        ]);

        $deviceId = $request->input('deviceId');
        $uuid = $request->input('uuid');

        // Mock do repositório de jobs
        $jobRepository = Mockery::mock(ControlIdJobRepositoryInterface::class);

        // Configure o comportamento esperado do mock
        $jobRepository->shouldReceive('getPendingJobByDeviceId')
            ->with($deviceId)
            ->once()
            ->andReturn(null); // Ou retorne um job mockado se necessário

        $pushService = new ControlIdJobService($jobRepository);
        $response = $pushService->processPush($deviceId, $uuid);

        // Ajuste a expectativa conforme o comportamento esperado quando não há jobs
        $this->assertEquals(['message' => 'No pending jobs for this device.'], $response);
    }

    /** @test */
    public function case_3_push_event_tem_job_create_user_visitante()
    {
        // |--------------------- CRIADO JOB PARA AUTORIZAR ---------------------|
        $this->strategy->createJobByEndpoint($this->dispositivo, $this->visitante, 'create_user_visitante');
        $job = ControlIdJob::where('identificador_dispositivo', $this->dispositivo->identificador_unico)->first();

        // |--------------------- passo 2 ---------------------|
        // |--------------------- EVENTO DO CONTROLID PUSH CHEGA PARA PERGUNTAR PRA GENTE SE TEM TAREFA DO DISPOSITIVO ---------------------|

        $request = new Request();
        $request->merge([
            'deviceId' => $this->dispositivo->identificador_unico,
            'is_test' => false,
            'uuid' => Str::uuid(),
        ]);

        $deviceId = $request->input('deviceId');
        $uuid = $request->input('uuid');
        // Mock do repositório de jobs
        $jobRepository = Mockery::mock(ControlIdJobRepositoryInterface::class);

        // Configure o comportamento esperado do mock
        $jobRepository->shouldReceive('getPendingJobByDeviceId')
            ->with($deviceId)
            ->once()
            ->andReturn($job); // Ou retorne um job mockado se necessário

        $pushService = new ControlIdJobService($jobRepository);
        $response = $pushService->processPush($deviceId, $uuid);


        Log::info('data_inicio_visita teste');
        $data_inicio = Carbon::parse($job->user_able->data_inicio_visita)->timestamp;
        $data_fim = Carbon::parse($job->user_able->data_fim_visita)->timestamp;
        Log::info('data_inicio_visita teste');

        Log::info($data_inicio);
        Log::info($data_fim);

        $this->assertEquals([
            "verb" => "POST",
            "endpoint" => "create_objects",
            "body" => [
                "object" => "users",
                "values" => [
                    [
                        "name" => $job->user_able->user->name,
                        "registration" => "",
                        "password" => "",
                        "salt" => "",
                        "begin_time" => $data_inicio,
                        "end_time" => $data_fim
                    ]
                ]
            ]
        ], $response);

        //verificar se o não existe erro no job foi processado
        $this->assertNotEquals(3, $job->status);
    }

    /** @test */
    public function case_4_push_event_tem_job_delete_user()
    {
        // |--------------------- CRIADO JOB PARA AUTORIZAR ---------------------|
        $this->strategy->createJobByEndpoint($this->dispositivo, $this->morador, 'delete_user');
        $job = ControlIdJob::where('identificador_dispositivo', $this->dispositivo->identificador_unico)->first();

        // |--------------------- passo 2 ---------------------|
        // |--------------------- EVENTO DO CONTROLID PUSH CHEGA PARA PERGUNTAR PRA GENTE SE TEM TAREFA DO DISPOSITIVO ---------------------|

        $request = new Request();
        $request->merge([
            'deviceId' => $this->dispositivo->identificador_unico,
            'is_test' => false,
            'uuid' => Str::uuid(),
        ]);

        $deviceId = $request->input('deviceId');
        $uuid = $request->input('uuid');
        // Mock do repositório de jobs
        $jobRepository = Mockery::mock(ControlIdJobRepositoryInterface::class);

        // Configure o comportamento esperado do mock
        $jobRepository->shouldReceive('getPendingJobByDeviceId')
            ->with($deviceId)
            ->once()
            ->andReturn($job); // Ou retorne um job mockado se necessário

        $pushService = new ControlIdJobService($jobRepository);
        $response = $pushService->processPush($deviceId, $uuid);

        $this->assertEquals([
            "verb" => "POST",
            "endpoint" => "destroy_objects",
            "body" => [
                "object" => "users",
                "where" => [
                    [
                        "object" => "users",
                        "field" => "id",
                        "operator" => "=",
                        "value" => $job->user_able->autorizacoeDispositivoByDispositivo($job->dispositivo)->controlid_user_id
                    ]
                ]
            ]
        ], $response);

        //verificar se o não existe erro no job foi processado
        $this->assertNotEquals(3, $job->status);
    }

    /** @test */
}
