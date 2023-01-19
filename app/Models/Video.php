<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $guarded =[];

    public function comment(){
        return $this->hasMany(VideoComment::Class);
    }
    public function category(){
        return $this->belongsTo(DigitalExerciseLibrary::Class,'category_id');
    }
    public function user(){
        return $this->belongsTo(User::Class);
    }
}
