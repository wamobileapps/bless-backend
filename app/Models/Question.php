<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $guarded=[];


    public  function options(){
        return $this->hasMany(Option::class)->select('option','answer','question_id');
    }

    public function answer(){

        return $this->hasOne(Answer::class)->select('answer','question_id');
    }
}
