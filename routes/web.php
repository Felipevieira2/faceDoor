<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TorreController;
use App\Http\Controllers\MoradorController;
use App\Http\Controllers\VisitanteController;
use App\Http\Controllers\CondominioController;

use App\Http\Controllers\DispositivoController;
use App\Http\Controllers\Admin\AcessoController;
use App\Http\Controllers\Admin\EventoController;
use App\Http\Controllers\Admin\AtividadeController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ComunicadoController;
use App\Http\Controllers\Admin\OcorrenciaController;
use App\Http\Controllers\Admin\MoradorController as AdminMoradorController;
use App\Http\Controllers\Admin\CondominioController as AdminCondominioController;
use App\Http\Controllers\Admin\DispositivoController as AdminDispositivoController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Rotas de autenticação
Route::middleware('guest')->group(function () {
    Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);

    Route::get('/register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [App\Http\Controllers\Auth\RegisterController::class, 'register']);

    // Rotas adicionais para recuperação de senha podem ser adicionadas aqui
});

Route::get('/', function () {
    if(Auth::check()){
        if(Auth::user()->hasRole('admin')){
            return redirect()->route('admin.dashboard');
        }else{
            return redirect()->route('user.dashboard');
        }
    }
    return view('welcome');
});

Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');


// Rotas autenticadas para administração
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Condomínios
    Route::resource('condominios', AdminCondominioController::class);
    
    // Dispositivos
    Route::resource('dispositivos', AdminDispositivoController::class);
    
    // Moradores
    Route::get('moradores', [AdminMoradorController::class, 'index'])->name('moradores.index');
    Route::get('moradores/create', [AdminMoradorController::class, 'create'])->name('moradores.create');
    Route::post('moradores', [AdminMoradorController::class, 'store'])->name('moradores.store');
    Route::get('moradores/{morador}', [AdminMoradorController::class, 'show'])->name('moradores.show');
    Route::get('moradores/{morador_id}/edit', [AdminMoradorController::class, 'edit'])->name('moradores.edit');
    Route::put('moradores/{morador}', [AdminMoradorController::class, 'update'])->name('moradores.update');
    Route::patch('moradores/{morador}', [AdminMoradorController::class, 'update']);
    Route::delete('moradores/{morador}', [AdminMoradorController::class, 'destroy'])->name('moradores.destroy');
    
    // Ocorrências
    Route::resource('ocorrencias', OcorrenciaController::class);
    
    // Atividades
    Route::get('/atividades', [AtividadeController::class, 'index'])->name('atividades.index');
    
    // Acessos
    Route::get('/acessos', [AcessoController::class, 'index'])->name('acessos.index');
    
    // Comunicados
    Route::resource('comunicados', ComunicadoController::class);
    
    // Eventos
    Route::resource('eventos', EventoController::class);
});

// Roteamento para o frontend
Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

// Roteamento para o público (não autenticado)
Route::get('/login', function () {
    // Implementar lógica de login
    return view('auth.login');
})->name('login');

Route::middleware(['auth'])->group(function () {
    Route::get('/condominios/select', [CondominioController::class, 'select'])->name('condominios.select');
    Route::post('/condominios/set-tenant', [CondominioController::class, 'setTenant'])->name('condominios.set-tenant');
});

