<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Video;
class DigitalExerciseLibrary extends Model
{
    use HasFactory;
    protected $guarded =[];
    public function video()
    {
        return $this->hasMany(Video::class,'category_id');
    }
}
