<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingSlout extends Model
{
    use HasFactory;

    protected $guarded =[];


    public function user(){
        return $this->belongsTo(User::class)->select('id','name','email','image','cover_image','phone_number');
    }
    public function trainer(){
        return $this->belongsTo(User::class,'trainer_id')->select('id','name','email','image','phone_number');
    }
}
