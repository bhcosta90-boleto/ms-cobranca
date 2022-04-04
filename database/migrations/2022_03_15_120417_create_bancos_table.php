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
        Schema::create('bancos', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->index();
            $table->string('codigo');
            $table->string('cnpj');
            $table->string('nome');
            $table->string('agencia');
            $table->string('conta');
            $table->string('carteira');
            $table->boolean('principal')->default(false);
            $table->boolean('repasse')->default(false);
            $table->json('data')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bancos');
    }
};
