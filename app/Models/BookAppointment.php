<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookAppointment extends Model
{
    use HasFactory;

    protected $guarded =[];

    public function client(){
        return $this->belongsTo(User::Class,'client_id');
    }
}
