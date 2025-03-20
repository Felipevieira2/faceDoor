<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Adiciona tenant_id em todas as tabelas relevantes
        $tables = [
      
            'moradores',
            'visitantes',
            'dispositivos',
            'torres',
            'users'
        ];

        foreach ($tables as $table) {

            Schema::table($table, function (Blueprint $table) {
                $table->foreignId('tenant_id')->after('id')->constrained('condominios')->onDelete('cascade');
            });
        }
    }

    public function down()
    {
        $tables = [         
            'moradores',
            'visitantes',
            'dispositivos',
            'torres',
            'users',
            'apartamentos'
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                if (Schema::hasColumn($table, 'tenant_id')) {
                    $table->dropForeign(['tenant_id']);
                    $table->dropColumn('tenant_id');
                }
            });
        }
    }
}; 