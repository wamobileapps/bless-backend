<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBuildMyWorkoutVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('build_my_workout_videos', function (Blueprint $table) {
            $table->id();
            $table->string('video_id');
            $table->integer('user_id');
            $table->integer('build_my_workout_id');
            $table->string('day_id')->nullable();
            $table->string('date')->nullable();
            $table->longText('description')->nullable();
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
        Schema::dropIfExists('build_my_workout_videos');
    }
}
