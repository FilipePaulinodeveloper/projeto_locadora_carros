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
        Schema::create('config_funcionarios', function (Blueprint $table) {
            $table->id();

            $table->string('nome');
            $table->string('cpf')->unique();
            $table->string('salario');
            $table->enum('cargo', ['Mecanico', 'Vendedor'])->nullable();

            $table->boolean('status');
            $table->boolean('deleted');
            $table->string('token', 255)->unique();
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
        Schema::dropIfExists('config_funcionarios');
    }
};
