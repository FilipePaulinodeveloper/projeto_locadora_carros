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
        Schema::create('config_clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('cpf', 15)->unique();
            $table->date('data_nascimento')->nullable();
            $table->string('telefone',15)->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('token');
            $table->boolean('deleted')->default(0)->change();
            $table->boolean('status');

            // Campos de endereço detalhados
            $table->string('logradouro');
            $table->string('numero');
            $table->string('complemento')->nullable();
            $table->string('bairro');
            $table->string('cep', 9);
            $table->string('cidade');
            $table->char('estado', 2);
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('config_clientes');
    }
};
