<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Follow extends Model
{
    use HasFactory;
    protected $guarded =[];
    public function user(){
        return $this->belongsTo(User::class,'user_id')->select('id','username','name','image');
    }   public function userfollow(){
        return $this->belongsTo(User::class,'trainer_id')->select('id','username','name','image');
    }
}
