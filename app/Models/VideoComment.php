<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Video;
class VideoComment extends Model
{
    use HasFactory;
    protected $guarded =[];

    public function video()
    {
        return $this->belongsTo(Video::class);
    }

    public function users()
    {
        return $this->belongsTo(User::Class,'user_id');
    }
}
