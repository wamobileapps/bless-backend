<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShareWorkout extends Model
{
    use HasFactory;
    protected $guarded=[];

    public function build_my_workout(){
        return $this->belongsTo(BuildMyWorkoutModel::class,'build_my_workout_id');
    }
}
