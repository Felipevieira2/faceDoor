<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Torre;
use App\Models\Morador;
use App\Models\Visitante;
use App\Models\Apartamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;

class VisitanteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $visitantes = Visitante::with(['user', 'apartamento.torre', 'moradorResponsavel.user'])
            ->latest()
            ->paginate(10);

        return view('admin.visitantes.index', compact('visitantes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $torres = Torre::all();
        $moradores = Morador::with('user')->get();

        return view('admin.visitantes.create', compact('torres', 'moradores'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
      
            $validated = $request->validate([
                'nome' => 'required|string|max:255',
                'cpf' => 'required|string|max:14|unique:users,cpf',
                'email' => 'required|string|email|max:255|unique:users',
                'telefone' => 'nullable|string|max:15',
                'data_nascimento' => 'nullable|date',
                'foto_visitante' => 'required|image|max:1024',                               
                'morador_responsavel_id' => 'required|exists:moradores,id',
                'data_validade_inicio' => 'required|date',
                'data_validade_fim' => 'nullable|date|after_or_equal:data_validade_inicio',
                'recorrente' => 'sometimes|boolean',
                'dias_semana' => 'required_if:recorrente,1|array',
               
            ]);

            if ($request->has('recorrente') && $request->recorrente == 1) {
                $this->validate($request, [
                    'horario_inicio' => 'required',
                    'horario_fim' => 'required|after:horario_inicio',
                ]);
            }


        try {
            // Atualizar foto se fornecida
            if ($request->hasFile('foto_visitante')) {
                $validated['foto'] = $request->file('foto_visitante')->store('visitantes', 'public');
            }

            $morador = Morador::find($validated['morador_responsavel_id']);
        
            // Criar usuário
            $user = User::create([
                'name' => $validated['nome'],
                'email' => $validated['email'],
                'cpf' => $validated['cpf'],
                'telefone' => $validated['telefone'] ?? null,
                'data_nascimento' => $validated['data_nascimento'] ?? null,
                'foto' => $validated['foto'],
                'password' => Hash::make(substr($validated['cpf'], 0, 6)), // Senha padrão: primeiros 6 dígitos do CPF 
            ]);
         
            // Criar visitante
            $visitante = new Visitante();
            $visitante->user_id = $user->id;
            $visitante->apartamento_id = $morador->apartamento_id;                       
            $visitante->morador_responsavel_id = $validated['morador_responsavel_id'];
            $visitante->data_validade_inicio = $validated['data_validade_inicio'];
            $visitante->data_validade_fim = $validated['data_validade_fim'] ?? null;
            $visitante->recorrente = $request->has('recorrente');

            if ($visitante->recorrente) {
                $visitante->dias_semana = implode(',', $validated['dias_semana']);
                $visitante->horario_inicio = $validated['horario_inicio'];
                $visitante->horario_fim = $validated['horario_fim'];
            }

            $visitante->ativo = true;
            $visitante->save();

            // Atribuir papel de visitante
            $user->assignRole('visitante');

            DB::commit();


            return redirect()->route('admin.visitantes.index')->with('success', 'Visitante cadastrado com sucesso!');
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            Log::error('Erro ao cadastrar visitante: ' . $th->getMessage() . ' line: ' . $th->getLine());
            return redirect()->back()->withInput($request->all())
                ->with('error', 'Erro ao cadastrar visitante: ');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Visitante $visitante)
    {
        $visitante->load(['user', 'apartamento.torre', 'moradorResponsavel.user']);
        return view('admin.visitantes.show', compact('visitante'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Visitante $visitante)
    {
        $visitante->load('user');
        $torres = Torre::all();
        $moradores = Morador::with('user')->get();
        $apartamentos = Apartamento::where('torre_id', $visitante->apartamento->torre_id)->get();

        return view('admin.visitantes.edit', compact('visitante', 'torres', 'moradores', 'apartamentos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Visitante $visitante)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'cpf' => [
                'required',
                'string',
                'max:14',
                Rule::unique('users', 'cpf')->ignore($visitante->user_id)
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($visitante->user_id)
            ],
            'telefone' => 'nullable|string|max:15',
            'data_nascimento' => 'nullable|date',
            'foto_visitante' => 'nullable|image|max:1024',                               
            'morador_responsavel_id' => 'required|exists:moradores,id',
            'data_validade_inicio' => 'required|date',
            'data_validade_fim' => 'nullable|date|after_or_equal:data_validade_inicio',
            'recorrente' => 'sometimes|boolean',
            'dias_semana' => 'required_if:recorrente,1|array',
            'ativo' => 'required|boolean',
            'observacoes' => 'nullable|string',
        ]);

        if ($request->has('recorrente') && $request->recorrente == 1) {
            $this->validate($request, [
                'horario_inicio' => 'required',
                'horario_fim' => 'required|after:horario_inicio',
            ]);
        }
        try {
            DB::beginTransaction();

            // Atualizar foto se fornecida
            if ($request->hasFile('foto_visitante')) {
                // Remover foto antiga
                if ($visitante->user->foto) {
                    Storage::disk('public')->delete($visitante->user->foto);
                }
                $validated['foto'] = $request->file('foto_visitante')->store('moradores', 'public');
            }

            // Atualizar usuário
            $visitante->user->name = $validated['nome'];
            $visitante->user->email = $validated['email'];
            $visitante->user->cpf = $validated['cpf'];
            $visitante->user->telefone = $validated['telefone'] ?? null;
            $visitante->user->data_nascimento = $validated['data_nascimento'] ?? null;
            $visitante->user->save();

            // Atualizar visitante
          
            $visitante->morador_responsavel_id = $validated['morador_responsavel_id'];
            $visitante->data_validade_inicio = $validated['data_validade_inicio'];
            $visitante->data_validade_fim = $validated['data_validade_fim'] ?? null;
            $visitante->recorrente = $request->has('recorrente');
            $visitante->ativo = $request->has('ativo');

            if ($visitante->recorrente) {
                $visitante->dias_semana = implode(',', $validated['dias_semana']);
                $visitante->horario_inicio = $validated['horario_inicio'];
                $visitante->horario_fim = $validated['horario_fim'];
            } else {
                $visitante->dias_semana = null;
                $visitante->horario_inicio = null;
                $visitante->horario_fim = null;
            }

            $visitante->save();

            DB::commit();
            return redirect()->route('admin.visitantes.index')->with('success', 'Visitante atualizado com sucesso!');
        } catch (\Throwable $th) {
            //throw $th;
            Log::error('Erro ao atualizar visitante: ' . $th->getMessage() . ' line: ' . $th->getLine());
            DB::rollBack();
            return redirect()->back()->withInput($request->all())
                ->with('error', 'Erro ao atualizar visitante: ');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Visitante $visitante)
    {
        try {
            $userId = $visitante->user_id;
            $fotoPath = $visitante->user->foto;

            // Excluir o visitante
            $visitante->delete();

            // Encontrar o usuário
            $user = User::find($userId);

            if ($user) {
                // Remover a foto se existir
                if ($fotoPath) {
                    Storage::disk('public')->delete($fotoPath);
                }

                // Excluir o usuário
                $user->delete();
            }

            Session::flash('success', 'Visitante excluído com sucesso!');
        } catch (\Exception $e) {
            Session::flash('error', 'Não foi possível excluir o visitante: ' . $e->getMessage());
        }

        return redirect()->route('admin.visitantes.index');
    }
}
