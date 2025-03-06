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
        Schema::create('config_vendas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_veiculo')->references('id')->on('config_veiculos')->onDelete('cascade');
            $table->foreignId('id_cliente')->references('id')->on('config_clientes')->onDelete('cascade');
            $table->decimal('valor', 10, 2);
            $table->enum('tipo', ['Locacao', 'Venda']);
            $table->string('token', 255)->unique();
            $table->boolean('status')->default(true);
            $table->boolean('deleted');
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
        Schema::dropIfExists('config_vendas');
    }
};
