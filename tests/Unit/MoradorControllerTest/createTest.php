<?php

namespace Tests\Unit\MoradorControllerTest;

use App\Http\Controllers\MoradorController;
use App\Models\Morador;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;
use Illuminate\Http\Response;

class CreateTest extends TestCase
{
    use RefreshDatabase;

    private MoradorController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new MoradorController();
    }

    public function testCreateMoradorComDadosValidos()
    {
        // Arrange
        $dadosMorador = [
            'nome' => 'João Silva',
            'cpf' => '123.456.789-00',
            'email' => 'joao@exemplo.com',
            'telefone' => '(11) 99999-9999',
            'data_nascimento' => '1990-01-01',
            'apartamento_id' => 1
        ];

        $request = new Request($dadosMorador);

        // Act
        $response = $this->controller->store($request);

        // Assert
        $this->assertEquals(201, $response->status());
        $this->assertDatabaseHas('moradores', [
            'nome' => 'João Silva',
            'email' => 'joao@exemplo.com'
        ]);
    }

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
        $response = $this->controller->store($request);

        // Assert
        $this->assertEquals(422, $response->status());
        $this->assertDatabaseMissing('moradores', ['email' => 'email-invalido']);
    }

    public function testCreateMoradorComCPFDuplicado()
    {
        // Arrange
        // Cria um morador primeiro
        Morador::factory()->create([
            'cpf' => '123.456.789-00'
        ]);

        $dadosMorador = [
            'nome' => 'Maria Silva',
            'cpf' => '123.456.789-00', // CPF já existente
            'email' => 'maria@exemplo.com',
            'telefone' => '(11) 88888-8888',
            'data_nascimento' => '1992-05-15',
            'apartamento_id' => 2
        ];

        $request = new Request($dadosMorador);

        // Act
        $response = $this->controller->store($request);

        // Assert
        $this->assertEquals(422, $response->status());
        $this->assertArrayHasKey('cpf', $response->getData(true)['errors']);
    }

    public function testCreateMoradorComEmailDuplicado()
    {
        // Arrange
        // Cria um morador primeiro
        Morador::factory()->create([
            'email' => 'teste@exemplo.com'
        ]);

        $dadosMorador = [
            'nome' => 'Carlos Silva',
            'cpf' => '987.654.321-00',
            'email' => 'teste@exemplo.com', // Email já existente
            'telefone' => '(11) 77777-7777',
            'data_nascimento' => '1985-10-20',
            'apartamento_id' => 3
        ];

        $request = new Request($dadosMorador);

        // Act
        $response = $this->controller->store($request);

        // Assert
        $this->assertEquals(422, $response->status());
        $this->assertArrayHasKey('email', $response->getData(true)['errors']);
    }
}
