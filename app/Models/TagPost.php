<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TagPost extends Model
{
    use HasFactory;

     protected $guarded =[];


    public function neewsfeed(){
        return $this->belongsTo(NewsFeed::class,'news_feed_id');
    }

}
