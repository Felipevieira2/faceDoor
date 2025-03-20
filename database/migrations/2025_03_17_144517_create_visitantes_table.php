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
        Schema::create('visitantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('apartamento_id')->constrained('apartamentos')->onDelete('cascade');
            $table->foreignId('morador_responsavel_id')->constrained('moradores')->onDelete('cascade');
            $table->dateTime('data_hora_entrada')->nullable();
            $table->dateTime('data_hora_saida')->nullable();
            $table->date('data_validade_inicio');
            $table->date('data_validade_fim')->nullable();
            $table->boolean('recorrente')->default(false);
            $table->string('dias_semana')->nullable(); // '1,2,3,4,5,6,7' (domingo a sábado)            
            $table->time('horario_inicio')->nullable();
            $table->time('horario_fim')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitantes');
    }
};
