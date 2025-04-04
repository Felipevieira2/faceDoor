<?php

namespace Tests\Unit\MoradorControllerTest;

use Tests\TestCase;
use App\Models\User;
use App\Models\Torre;
use App\Models\Morador;
use App\Models\Visitante;
use App\Models\Condominio;
use App\Models\Apartamento;
use App\Models\Dispositivo;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Admin\MoradorController;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateTest extends TestCase
{
    use RefreshDatabase;

    private MoradorController $controller;
    private User $user;
    private Dispositivo $dispositivo;
    private Dispositivo $dispositivoInativo;
    private Apartamento $apartamento;
    private Morador $morador;
    private Visitante $visitante;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new MoradorController();

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
            'email' => 'teste@teste.com',
            'password' => 'password',
        ]);

        session(['tenant_id' => '1']);
        //make login in user admin
        $this->actingAs($this->user);

        Role::create(['name' => 'administrador']);
        Role::create(['name' => 'morador']);
        Role::create(['name' => 'visitante']);

        $this->user->assignRole('administrador');
    }
    /** @test */
    public function testCreateMoradorComDadosValidos()
    {
        // Arrange
        $dadosMorador = [
            "foto" => UploadedFile::fake()->image('foto.jpg'),
            "nome" => "João Silva",
            "is_responsavel" => true,
            "cpf" => "123.456.789-00",
            "email" => "joao@exemplo.com",
            "telefone" => "(11) 99999-9999",
            "data_nascimento" => "1990-01-01",
            "condominio_id" => 1,
            "torre_id" => 1,
            "apartamento" => "101",
            "status" => "ativo",
        ];



        $request = new Request($dadosMorador);

        // Act
        $response = $this->controller->store($request);

        // Assert

        $this->assertDatabaseHas('moradores', [
            'user_id' => 2,
            'apartamento_id' => 2,
            'data_inicio' => now()->format('Y-m-d'),
            'data_fim' => null,
            'ativo' => 1,
            'tenant_id' => '1',
        ]);

        $this->assertDatabaseHas('users', [
            'cpf' => '123.456.789-00',
            'email' => 'joao@exemplo.com',
            'name' => 'João Silva',
            'telefone' => '(11) 99999-9999',
            'data_nascimento' => '1990-01-01',
            'foto' => $dadosMorador['foto'],
            'tenant_id' => '1',
        ]);
    }


    /**
     * @test
     */
    public function testCreateMoradorComDadosInvalidos()
    {
        // Arrange
        $dadosMorador = [
            'nome' => '', // Nome vazio deve falhar validação
            'email' => 'email-invalido',
            'cpf' => '123', // CPF inválido
        ];

        $request = new Request($dadosMorador);

        // Act
        try {
            $response = $this->controller->store($request);
            $this->fail('O método deveria lançar uma exceção ValidationException');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // OU Opção 2: Verificar os erros de validação diretamente
            $this->assertTrue($e->validator->errors()->has('nome') ||
                $e->validator->errors()->has('telefone') ||
                $e->validator->errors()->has('data_nascimento'));
        }
        // Assert

        $this->assertDatabaseMissing('users', ['email' => 'email-invalido']);
    }
    /** @test */
    public function testCreateMoradorComCPFDuplicado()
    {
        // Arrange
        $user = User::factory()->create([
            'tenant_id' => '1',
            'cpf' => '123.456.789-00',
            'name' => 'Teste',
            'email' => 'test2@teste.com',
            'password' => 'password',
        ]);
        
        // Cria um morador primeiro
        Morador::factory()->create([
           
            'tenant_id' => '1',
            'user_id' => $user->id,
            'apartamento_id' => $this->apartamento->id,
            'data_inicio' => now()->format('Y-m-d'),
            'data_fim' => null,
            'ativo' => 1,
        ]);

      

        // Arrange
        $dadosMorador = [
            "foto" => UploadedFile::fake()->image('foto.jpg'),
            "nome" => "Maria Silva",
            "is_responsavel" => true,
            "cpf" => "123.456.789-00",
            "email" => "maria@exemplo.com",
            "telefone" => "(11) 99999-9999",
            "data_nascimento" => "1990-01-01",
            "condominio_id" => 1,
            "torre_id" => 1,
            "apartamento" => "101",
            "status" => "ativo",
        ];

        $request = new Request($dadosMorador);

        // Act
        try {
            $response = $this->controller->store($request);
            $this->fail('O método deveria lançar uma exceção ValidationException');
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->assertTrue($e->validator->errors()->has('cpf'));
        }

        // Assert
      
    }
    /** @test */
    public function testCreateMoradorComEmailDuplicado()
    {
        // Arrange
        $user = User::factory()->create([
            'tenant_id' => '1',
            'cpf' => '123.456.789-00',
            'name' => 'Teste',
            'email' => 'test2@teste.com',
            'password' => 'password',
        ]);
        
        // Cria um morador primeiro
        Morador::factory()->create([
           
            'tenant_id' => '1',
            'user_id' => $user->id,
            'apartamento_id' => $this->apartamento->id,
            'data_inicio' => now()->format('Y-m-d'),
            'data_fim' => null,
            'ativo' => 1,
        ]);

      

        // Arrange
        $dadosMorador = [
            "foto" => UploadedFile::fake()->image('foto.jpg'),
            "nome" => "Maria Silva",
            "is_responsavel" => true,
            "cpf" => "123.456.789-00",
            "email" => "test2@teste.com",
            "telefone" => "(11) 99999-9999",
            "data_nascimento" => "1990-01-01",
            "condominio_id" => 1,
            "torre_id" => 1,
            "apartamento" => "101",
            "status" => "ativo",
        ];

        $request = new Request($dadosMorador);

        // Act
        try {
            $response = $this->controller->store($request);
            $this->fail('O método deveria lançar uma exceção ValidationException');
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->assertTrue($e->validator->errors()->has('email'));
        }

    }
}
