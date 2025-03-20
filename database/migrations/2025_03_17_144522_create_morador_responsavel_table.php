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
        Schema::create('morador_responsavel', function (Blueprint $table) {
            $table->id();
            $table->foreignId('morador_id')->constrained('moradores')->onDelete('cascade');
            $table->foreignId('apartamento_id')->constrained('apartamentos')->onDelete('cascade');
            $table->date('data_inicio');
            $table->date('data_fim')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
            
            // Garante que um morador só pode ser responsável uma vez por apartamento
            $table->unique(['morador_id', 'apartamento_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('morador_responsavel');
    }
};
