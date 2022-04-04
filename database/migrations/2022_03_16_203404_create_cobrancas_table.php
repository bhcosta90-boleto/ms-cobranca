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
        Schema::create('cobrancas', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('banco_id')->nullable();
            $table->uuid('conta_id');
            $table->string('cliente_nome');
            $table->string('cliente_documento', 30)->nullable();
            $table->date('vencimento')->nullable();
            $table->unsignedDouble('valor');
            $table->unsignedDouble('valor_pago')->nullable();
            $table->unsignedFloat('taxa_atual');
            $table->unsignedBigInteger('numero_banco')->nullable();
            $table->dateTime('data_pagamento')->nullable();
            $table->dateTime('data_confirmacao')->nullable();
            $table->dateTime('data_baixa')->nullable();
            $table->date('data_credito')->nullable();
            $table->date('data_repasse')->nullable();
            $table->unsignedBigInteger('cobrancaduplicada_id')->nullable();
            $table->boolean('pago')->nullable();
            $table->string('md5')->nullable();
            $table->string('hashpagamento')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['banco_id', 'numero_banco']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cobrancas');
    }
};
