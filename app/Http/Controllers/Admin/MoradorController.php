<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Torre;
use App\Models\Morador;
use App\Models\Atividade;
use App\Models\Condominio;
use App\Models\Apartamento;
use Illuminate\Http\Request;
use App\Models\MoradorResponsavel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class MoradorController extends Controller
{
    public function index()
    {
        $moradores = Morador::leftJoin('apartamentos', 'moradores.apartamento_id', '=', 'apartamentos.id')
            ->leftjoin('users', 'moradores.user_id', '=', 'users.id')
            ->leftJoin('torres', 'apartamentos.torre_id', '=', 'torres.id')
            ->leftJoin('condominios', 'torres.condominio_id', '=', 'condominios.id')
            ->select(
                'users.name',
                'users.email',
                'users.cpf',
                'users.telefone',
                'users.data_nascimento',
                'users.foto',
                'moradores.*',  
                'apartamentos.numero as apartamento_numero', 
                'torres.nome as torre_nome', 
                'condominios.nome as condominio_nome',
                'moradores.ativo as morador_status'
            )
            ->latest()
            ->paginate(10);

        return view('admin.moradores.index', compact('moradores'));
    }

    public function create()
    {
        $condominios = Condominio::all();
        $torres = Torre::all();


        return view('admin.moradores.create', compact('condominios', 'torres'));
    }

    public function store(Request $request)
    {

        $validated = $request->validate([
            'condominio_id' => 'required|exists:condominios,id',
            'torre_id' => 'required|exists:torres,id',
            'nome' => 'required|string|max:255',
            'cpf' => 'required|string|max:14|unique:users,cpf,' . $request->condominio_id, //unique in users cpf+condominio_id
            'email' => 'nullable|email|unique:users,email,' . $morador->user_id,
            'telefone' => 'nullable|string|max:20',
            'apartamento' => 'required|string|max:20',
            'data_nascimento' => 'nullable|date',
            'foto_morador' => 'required|image|max:2048',
        ]);

        try {

            DB::beginTransaction();
            
            if ($request->hasFile('foto_morador')) {
                $validated['foto'] = $request->file('foto_morador')->store('moradores', 'public');
            }

            #TODO: CRIAR UM SELECT MULTI COM OS APARTAMENTOS CRIADOS PARA O ADMINISTRADOR CONSEGUIR SELECIONAR NO FORMULARIO DE CADASTRO DO MORADOR
            $apartamento = Apartamento::firstOrCreate(
                [
                    'torre_id' => $request->torre_id,
                    'numero' => $request->apartamento,
                    'bloco' => $request->bloco,
                ]
            );

            $user = User::create([
                'name' => $request->nome,
                'email' => $request->email,
                'password' => Hash::make(rand(100000, 999999)),
                'telefone' => $request->telefone,
                'cpf' => $request->cpf,
                'data_nascimento' => $request->data_nascimento, 
                'foto' => $validated['foto'],
            ]);

 

            $user->assignRole('morador');

            $morador = Morador::create(
                [
                    'user_id' => $user->id,
                    'apartamento_id' => $apartamento->id,
                    'data_inicio' => now(),
                    'data_fim' => null,
                    'ativo' => true,

                ]
            );

            if ($request->is_responsavel) {
                MoradorResponsavel::create([
                    'morador_id' => $morador->id,
                    'apartamento_id' => $apartamento->id,
                    'data_inicio' => now(),
                    'data_fim' => null,
                    'ativo' => true,
                ]);
            }

            DB::commit();
            return redirect()->route('admin.moradores.index')
                ->with('success', 'Morador cadastrado com sucesso!');
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            Log::error('Erro ao cadastrar morador: ' . $th->getMessage() . ' line: ' . $th->getLine());
            return redirect()->back()->withInput($request->all())
                ->with('error', 'Erro ao cadastrar morador: ');
        }

        return redirect()->route('admin.moradores.index')
            ->with('success', 'Morador cadastrado com sucesso!');
    }

    public function show(Morador $morador)
    {
        $morador->load('condominio');
        return view('admin.moradores.show', compact('morador'));
    }

    public function edit($id)
    {       
        $morador = Morador::find($id);
                     
        if(!$morador) {
            return redirect()->route('admin.moradores.index')
                ->with('error', 'Morador não encontrado!');
        }
        
        // Carregando todos os relacionamentos necessários
        $morador->load(['user', 'apartamento', 'apartamento.torre', 'apartamento.torre.condominio']);
        
        // Preparando dados com valores padrão para campos de relacionamentos
        $dados = [
            'user' => [
                'name' => $morador->user->name ?? null,
                'email' => $morador->user->email ?? null,
                'cpf' => $morador->user->cpf ?? null,
                'telefone' => $morador->user->telefone ?? null,
                'data_nascimento' => $morador->user->data_nascimento ?? null,
                'foto' => $morador->user->foto ?? null,
            ],
            'apartamento' => [
                'id' => $morador->apartamento->id ?? null,
                'numero' => $morador->apartamento->numero ?? null,
                'bloco' => $morador->apartamento->bloco ?? null,
            ],
            'torre' => [
                'id' => $morador->apartamento->torre->id ?? null,
                'nome' => $morador->apartamento->torre->nome ?? null,
            ],
            'condominio' => [
                'id' => $morador->apartamento->torre->condominio->id ?? null,
                'nome' => $morador->apartamento->torre->condominio->nome ?? null,
            ],
        ];
        
        // Carregando os dados para os selects
        $condominios = Condominio::all();
        $torres = Torre::all();
        
        // Verificando se o morador é responsável pelo apartamento
        $isResponsavel = false;
        if ($morador->id) {
            $isResponsavel = MoradorResponsavel::where('morador_id', $morador->id)
                                               ->where('ativo', true)
                                               ->exists();
        }
        
        return view('admin.moradores.edit', compact('morador', 'condominios', 'torres', 'isResponsavel', 'dados'));
    }

    public function update(Request $request, Morador $morador)
    {        
        $validated = $request->validate([
            'condominio_id' => 'required|exists:condominios,id',
            'torre_id' => 'required|exists:torres,id',
            'nome' => 'required|string|max:255',
            'cpf' => 'required|string|max:14|unique:users,cpf,' . $morador->user_id,
            'email' => 'nullable|email|unique:users,email,' . $morador->user_id,
            'telefone' => 'nullable|string|max:20',
            'apartamento' => 'required|string|max:20',           
            'data_nascimento' => 'nullable|date',          
            'status' => 'required|string|in:ativo,inativo,bloqueado',
            'foto_morador' => 'nullable|image|max:2048',
        ]);

        try {
            DB::beginTransaction();

            if ($request->hasFile('foto_morador')) {
                // Remover foto antiga
                if ($morador->user->foto) {
                    Storage::disk('public')->delete($morador->foto);
                }
                $validated['foto'] = $request->file('foto_morador')->store('moradores', 'public');
            }
    
            // Criar ou atualizar apartamento
            $apartamento = Apartamento::firstOrCreate(
                [
                    'torre_id' => $request->torre_id,
                    'numero' => $request->apartamento,
                    'bloco' => $request->bloco,
                ]
            );

            // Atualizar usuário
            $morador->user->update([
                'name' => $request->nome,
                'email' => $request->email,
                'telefone' => $request->telefone,
                'cpf' => $request->cpf,
                'data_nascimento' => $request->data_nascimento,
                'foto' => $validated['foto'] ?? $morador->user->foto, // Manter foto antiga se não houver nova
                'ativo' => $request->status == 'ativo' ? true : false,
            ]);

            // Atualizar morador
            $morador->update([
                'apartamento_id' => $apartamento->id,            
                'status' => $request->status,                      
                // ... outros campos que precisam ser atualizados ...
            ]);

            // Registrar atividade
            // Atividade::create([
            //     'usuario_id' => auth()->id(),
            //     'descricao' => 'Atualizou o morador: ' . $morador->nome,
            // ]);

            DB::commit();
            return redirect()->route('admin.moradores.index')
                ->with('success', 'Morador atualizado com sucesso!');
        } catch(\Throwable $th) {
            DB::rollBack();
            Log::error('Erro ao atualizar morador: ' . $th->getMessage() . ' line: ' . $th->getLine());
            return redirect()->back()->withInput($request->all())
                ->with('error', 'Erro ao atualizar morador: ');
        }

        return redirect()->route('admin.moradores.index')
            ->with('success', 'Morador atualizado com sucesso!');
    }

    public function destroy(Morador $morador)
    {
        $nome = $morador->nome;

        // Remover foto
        if ($morador->foto) {
            Storage::disk('public')->delete($morador->foto);
        }

        $morador->delete();

        // Registrar atividade
        // Atividade::create([
        //     'usuario_id' => auth()->id(),
        //     'descricao' => 'Removeu o morador: ' . $nome,
        // ]);

        return redirect()->route('admin.moradores.index')
            ->with('success', 'Morador removido com sucesso!');
    }
}
