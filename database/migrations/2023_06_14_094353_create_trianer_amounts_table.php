<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrianerAmountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trianer_amounts', function (Blueprint $table) {
            $table->id();
            $table->integer('trainer_id')->nullable();
            $table->string('clientSecret')->nullable();
            $table->string('amount')->nullable();
            $table->string('plan_id')->nullable();
           

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
        Schema::dropIfExists('trianer_amounts');
    }
}
