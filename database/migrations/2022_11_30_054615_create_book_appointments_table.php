<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookAppointmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('book_appointments', function (Blueprint $table) {
            $table->id();
            $table->integer('trainer_id');
            $table->integer('client_id');
            $table->integer('type_specailties_id');
            $table->longText('message');
            $table->date('date');
            $table->time('time');
            $table->integer('status')->default(0)->comment('1=> accept 2 => Reject');
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
        Schema::dropIfExists('book_appointments');
    }
}
