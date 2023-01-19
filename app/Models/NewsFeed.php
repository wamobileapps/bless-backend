<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsFeed extends Model
{
    use HasFactory;
    protected $guarded =[];

    public function user(){
        return $this->belongsTo(User::Class,'user_id');
    }
    public function comment(){
        return $this->hasMany(NewsFeedComment::class,'news_feed_id');
    }

}
