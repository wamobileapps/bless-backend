<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeSpecialist extends Model
{
    use HasFactory;
    protected $guarded =[];


    public function usertype(){
        return $this->belongsTo(UserType::class,'user_type_id');
    }
}
