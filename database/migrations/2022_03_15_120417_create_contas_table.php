<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contas', function (Blueprint $table) {
            $table->id();
            $table->uuid('agencia_id')->nullable()->index();
            $table->uuid('banco_emissor_id')->nullable();
            $table->string('credencial')->index();
            $table->string('chave');
            $table->string('tipo')->nullable();
            $table->unsignedDouble('valor_taxa')->nullable();
            $table->unsignedDouble('valor_minimo')->default(7.50);
            $table->string('status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contas');
    }
};
