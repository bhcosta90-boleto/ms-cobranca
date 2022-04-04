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
        Schema::create('conta_splits', function (Blueprint $table) {
            $table->id();
            $table->string('credencial')->index();
            $table->string('chave');
            $table->string('nome');
            $table->string('documento');
            $table->string('banco');
            $table->string('agencia');
            $table->string('conta');
            $table->string('tipo')->nullable();
            $table->timestamps();

            $table->unique(['banco', 'documento', 'agencia', 'conta']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('conta_splits');
    }
};
