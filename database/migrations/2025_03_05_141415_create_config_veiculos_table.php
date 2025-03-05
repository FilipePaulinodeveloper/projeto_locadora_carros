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
        Schema::create('config_veiculos', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo', ['Carro', 'Moto']);
            $table->string('marca');
            $table->string('modelo');
            $table->year('ano_fabricacao');
            $table->year('ano_modelo');
            $table->string('placa')->unique();
            $table->string('renavam')->unique();
            $table->string('chassi')->unique();
            $table->string('cor');
            $table->decimal('quilometragem', 10, 2);
            $table->enum('combustivel', ['Gasolina', 'Álcool', 'Flex', 'Diesel', 'Elétrico', 'Híbrido']);
            $table->decimal('valor_venda', 10, 2)->nullable();
            $table->decimal('valor_diaria', 10, 2)->nullable();
            $table->boolean('disponivel_venda')->default(false);
            $table->boolean('disponivel_locacao')->default(false);
            $table->boolean('status');
            $table->boolean('deleted');
            $table->string('token');
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
        Schema::dropIfExists('config_veiculos');
    }
};
