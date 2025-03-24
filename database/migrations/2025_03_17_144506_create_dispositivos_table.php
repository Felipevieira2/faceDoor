<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('dispositivos', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('localizacao'); // 'entrada_condominio', 'saida_condominio', 'torre'
            $table->string('identificador_unico');
            $table->string('fabricante');
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->string('ip')->nullable();
            $table->foreignId('condominio_id')->constrained('condominios')->onDelete('cascade');
            $table->foreignId('torre_id')->nullable()->constrained('torres')->onDelete('set null');            
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dispositivos');
    }
};
