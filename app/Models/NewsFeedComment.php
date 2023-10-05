<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsFeedComment extends Model
{
    use HasFactory;
    protected $guarded =[];
    public function user(){
        return $this->belongsTo(User::class);
    }

    public function news_feed_comment_like(){
        return $this->hasMany(NewsFeedCommentLike::class,'news_feed_comment_id');
    }
}
