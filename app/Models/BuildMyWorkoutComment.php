<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuildMyWorkoutComment extends Model
{
    use HasFactory;
    protected $guarded =[];

    public function user(){
        return $this->belongsTo(User::class);
    }

}
