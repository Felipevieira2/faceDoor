<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('autorizacao_dispositivos', function (Blueprint $table) {
            $table->id();
            $table->string('identificador_dispositivo'); // codigo unico do dispositivo 
            $table->unsignedBigInteger('authorizable_id'); // ID da entidade relacionada
            $table->string('authorizable_type'); // Nome da classe do modelo relacionado
            $table->enum('status', ['autorizado', 'nao_autorizado', 'processando', 'erro', 'bloqueado'])->default('processando'); // status do dispositivo
            $table->date('data_inicio_visitante')->nullable(); // data de inicio da autorizacao
            $table->date('data_fim_visitante')->nullable(); // data de fim da autorizacao
            $table->string('messagem_erro')->nullable(); // mensagem de erro do dispositivo

            $table->unsignedBigInteger('user_id_externo')->nullable(); // Campo do user id do dispositivo
            $table->unsignedBigInteger('match_user_id_externo')->nullable(); // Campo do match user id do dispositivo
            $table->unsignedBigInteger('group_id_externo')->nullable(); // Campo do group id do dispositivo
            $table->integer('autorizado_por')->nullable(); // Campo do id do usuario que autorizou o dispositivo

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('autorizacao_dispositivos');
    }
};
