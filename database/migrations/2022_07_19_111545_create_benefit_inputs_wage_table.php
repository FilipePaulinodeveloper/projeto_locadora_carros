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
        // Schema::create('benefit_inputs_wage', function (Blueprint $table) {
        //     $table->id();
        //     $table->bigInteger('benefit_id')->nullable();
        //     $table->foreignId('wage_id')->constrained();
        //     $table->string('value', 255)->nullable();
        //     $table->timestamp('created_at')->useCurrent();
        //     $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('benefit_inputs_wage');
    }
};
