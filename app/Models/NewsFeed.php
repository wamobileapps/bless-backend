<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsFeed extends Model
{
    use HasFactory;
    protected $guarded =[];

    public function user(){
        return $this->belongsTo(User::Class,'user_id')->select('id','name','email','username','image','role','cover_image');
    }
    public function comment(){
        return $this->hasMany(NewsFeedComment::class,'news_feed_id');
    }
    public function like(){
        return $this->hasMany(LikeNewsFeed::class,'news_feed_id')->select('id','news_feed_id');
    }

    public function tag(){
        return $this->hasMany(TagPost::class,'news_feed_id');
    }

}
