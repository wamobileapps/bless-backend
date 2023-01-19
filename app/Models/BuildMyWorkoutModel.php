<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DigitalExerciseLibrary;
use App\Models\BuildMyWorkoutVideo;

class BuildMyWorkoutModel extends Model
{
    use HasFactory;
    protected $guarded =[];

    public function category(){
        return $this->belongsTo(DigitalExerciseLibrary::class);
    }
    public function built_my_workout_video(){
        return $this->hasMany(BuildMyWorkoutVideo::class,'build_my_workout_id');
    }
    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }



}
