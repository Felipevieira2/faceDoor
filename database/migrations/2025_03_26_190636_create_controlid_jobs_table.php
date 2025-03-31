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
        Schema::create('controlid_jobs', function (Blueprint $table) {
            $table->bigIncrements('id');     
            $table->unsignedBigInteger('user_able_id')->nullable(); // ID da entidade relacionada
            $table->string('user_able_type')->nullable(); // Nome da classe do modelo relacionado
            $table->string('endpoint');
            $table->string('status')->default(0)->comment('1 - concluído, 0 - pendente, 3 - error');
            $table->string('uuid')->nullable();
            $table->string('response')->nullable();
            $table->text('log')->nullable();
            $table->string('identificador_dispositivo')->nullable();      
            $table->unsignedTinyInteger('tentativas')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('controlid_jobs');
    }
};
