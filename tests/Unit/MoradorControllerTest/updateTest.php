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

class UpdateTest extends TestCase
{
    use RefreshDatabase;

    private MoradorController $controller;
    private User $user;
    private Dispositivo $dispositivo;
    private Dispositivo $dispositivoInativo;
    private Apartamento $apartamento;
    private Morador $morador;
    private User $moradorUser;

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

        $this->dispositivo = Dispositivo::factory()->create([
            'tenant_id' => '1',
            'fabricante' => 'controlid',
            'identificador_unico' => '1234567890',
            'condominio_id' => '1',
            'ativo' => true,
        ]);

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

        $this->moradorUser = User::factory()->create([
            'tenant_id' => '1',
            'name' => 'Morador Teste',
            'email' => 'morador@teste.com',
            'cpf' => '123.456.789-00',
            'telefone' => '(11) 99999-9999',
            'data_nascimento' => '1990-01-01',
        ]);

        $this->morador = Morador::factory()->create([
            'tenant_id' => '1',
            'user_id' => $this->moradorUser->id,
            'apartamento_id' => $this->apartamento->id,
            'data_inicio' => now()->format('Y-m-d'),
            'ativo' => 1,
        ]);

        session(['tenant_id' => '1']);
        $this->actingAs($this->user);

        Role::create(['name' => 'administrador']);
        Role::create(['name' => 'morador']);
        Role::create(['name' => 'visitante']);

        $this->user->assignRole('administrador');
    }
    /** @test */
    public function testUpdateMoradorComDadosValidos()
    {
        // Arrange
        $dadosAtualizados = [
            "foto" => UploadedFile::fake()->image('nova_foto.jpg'),
            "nome" => "João Silva Atualizado",
            "is_responsavel" => true,
            "cpf" => "987.654.321-00",
            "email" => "joao_novo@exemplo.com",
            "telefone" => "(11) 88888-8888",
            "data_nascimento" => "1992-01-01",
            "condominio_id" => 1,
            "torre_id" => 1,
            "apartamento" => "101",
            "status" => "ativo",
        ];

        $request = new Request($dadosAtualizados);

        // Act
        $response = $this->controller->update($request, $this->morador);

        // Assert
        $this->assertDatabaseHas('users', [
            'id' => $this->moradorUser->id,
            'name' => 'João Silva Atualizado',
            'email' => 'joao_novo@exemplo.com',
            'cpf' => '987.654.321-00',
            'telefone' => '(11) 88888-8888',
            'data_nascimento' => '1992-01-01',
        ]);
    }
    /** @test */
    public function testUpdateMoradorComDadosInvalidos()
    {
        // Arrange
        $dadosInvalidos = [
            'nome' => '',
            'email' => 'email-invalido',
            'cpf' => '123',
        ];

        $request = new Request($dadosInvalidos);

        // Act
        try {
            $response = $this->controller->update($request, $this->morador);
            $this->fail('O método deveria lançar uma exceção ValidationException');
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->assertTrue($e->validator->errors()->has('nome') ||
                $e->validator->errors()->has('email') ||
                $e->validator->errors()->has('cpf'));
        }
    }
    /** @test */
    public function testUpdateMoradorComCPFDuplicado()
    {
        // Arrange
        $outroUser = User::factory()->create([
            'cpf' => '999.888.777-66',
        ]);

        $dadosAtualizados = [
            "nome" => "João Silva",
            "cpf" => "999.888.777-66", // CPF já existente
            "email" => "joao_novo@exemplo.com",
            "telefone" => "(11) 88888-8888",
            "data_nascimento" => "1991-01-01",
        ];

        $request = new Request($dadosAtualizados);

        // Act
        try {
            $response = $this->controller->update($request, $this->morador);
            $this->fail('O método deveria lançar uma exceção ValidationException');
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->assertTrue($e->validator->errors()->has('cpf'));
        }
    }
    /** @test */
    public function testUpdateMoradorComEmailDuplicado()
    {
        // Arrange
        $outroUser = User::factory()->create([
            'email' => 'outro@exemplo.com',
        ]);

        $dadosAtualizados = [
            "nome" => "João Silva",
            "cpf" => "123.456.789-00",
            "email" => "outro@exemplo.com", // Email já existente
            "telefone" => "(11) 88888-8888",
            "data_nascimento" => "1991-01-01",
        ];

        $request = new Request($dadosAtualizados);

        // Act
        try {
            $response = $this->controller->update($request, $this->morador);
            $this->fail('O método deveria lançar uma exceção ValidationException');
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->assertTrue($e->validator->errors()->has('email'));
        }
    }
}
