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
        Schema::create('config_manutencoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_veiculo')->references('id')->on('config_veiculos')->onDelete('cascade');
            $table->date('data_manutencao');
            $table->text('descricao');
            $table->decimal('valor', 10, 2);
            $table->enum('tipo', ['Preventiva', 'Corretiva']);
            $table->string('token', 255)->unique();
            $table->boolean('status')->default(true);
            $table->boolean('deleted');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('config_manutencoes');
    }
};
