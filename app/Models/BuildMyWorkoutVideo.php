<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuildMyWorkoutVideo extends Model
{
    use HasFactory;
    protected $guarded =[];

    public function user(){
        return $this->belongsTo(User::class);
    }
    public function comment(){
        return $this->hasMany(BuildMyWorkoutComment::class);
    }

    public function like(){
        return $this->hasMany(LikeBuildMyWorkout::class,'build_workout_video_id');
    }
    public function video(){
        return $this->belongsTo(Video::class,'video_id');
    }
    public function day(){
        return $this->belongsTo(WeekDay::class);
    }

}
