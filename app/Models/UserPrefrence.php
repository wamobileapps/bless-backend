<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPrefrence extends Model
{
    use HasFactory;
    protected $guarded =[];

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function typespecialtis(){
        return $this->belongsTo(TypeSpecialist::class,'type_specialties_id');

    }
    public function follow(){
        return $this->hasMany(Follow::class);
    }

}
