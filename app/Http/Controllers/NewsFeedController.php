<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\NewsFeed;
use App\Models\User;
use App\Models\TagPost;
use App\Models\Follow;
use App\Models\LikeNewsFeed;
use App\Models\NewsFeedCommentLike;
use App\Models\NewsFeedComment;
use Auth;
use App\Helper\Helper;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class NewsFeedController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

//        $follow =Follow::where('user_id',Auth::user()->id)->get('trainer_id');
//        $trainer_id =array();
//        foreach ($follow as $trainer){
//            $trainer_id[] =$trainer->trainer_id;
//        }

        $feeds =NewsFeed::with(['user.userprefrence.typespecialtis'=>function($query){$query->select('id','title','description');}])
            ->with(['tag.user'=> function($query){$query->select('id','username','image','role','cover_image');}])->get();
        
        foreach ($feeds as $feed){
            if(LikeNewsFeed::where('news_feed_id',$feed->id)->where('user_id',Auth::user()->id)->exists()){
                $feed->islike = true;
            }
            else{
                $feed->islike =false;
            }
            $feed->likecount =LikeNewsFeed::where('news_feed_id',$feed->id)->count();
            $feed->commentcount =NewsFeedComment::where('news_feed_id',$feed->id)->count();
        }
//
//        $myfeeds =NewsFeed::with(['user'=>function($query){$query->select('id','username','image');}])
//            ->where('user_id',Auth::user()->id)->get();
//        foreach ($myfeeds as $feedsss){
//            if(LikeNewsFeed::where('news_feed_id',$feedsss->id)->where('user_id',Auth::user()->id)->exists()){
//                $feedsss->islike = true;
//            }
//            else{
//                $feedsss->islike =false;
//            }
//            $feedsss->likecount =LikeNewsFeed::where('news_feed_id',$feedsss->id)->count();
//            $feedsss->commentcount =NewsFeedComment::where('news_feed_id',$feedsss->id)->count();
//        }
//
//
//        $tagpost=TagPost::with(['neewsfeed.user'=>function($query){$query->select('id','name','email','image');}])->where('trainer_id',Auth::user()->id)->get();
//        $object = new \stdClass();
//        foreach ($tagpost as $tagpost){
//            $object->post[] =$tagpost->neewsfeed;
//        }
//        foreach ($object->post as $tag){
//            if(LikeNewsFeed::where('news_feed_id',$tag->id)->where('user_id',Auth::user()->id)->exists()){
//                $tag->islike = true;
//            }
//            else{
//                $tag->islike =false;
//            }
//            $tag->likecount =LikeNewsFeed::where('news_feed_id',$tag->id)->count();
//            $tag->istag =true;
//            $tag->commentcount =NewsFeedComment::where('news_feed_id',$tag->id)->count();
//        }



//        $data= array_merge((json_decode(json_encode($myfeeds), true)),(json_decode(json_encode($feeds), true)),(json_decode(json_encode($object->post), true)));
//       $data= $this->pc_array_shuffle($data);
        return response()->json([
            'success' => true,
            'data' => $feeds,
        ], Response::HTTP_OK);
    }
//    function pc_array_shuffle($array) {
//        $i = count($array); while(--$i) {
//            $j = mt_rand(0, $i); if ($i != $j) {
//                // swap elements
//                $tmp = $array[$j]; $array[$j] = $array[$i]; $array[$i] = $tmp; }
//        } return $array; }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $path = $request->file('picture')->store('picture');
        $data = $request->only('description');
        $detail = NewsFeed::create($data + ['user_id' => Auth::user()->id, 'picture' => $path]);
        if ($request->tag) {
            foreach ($request->tag as $tag) {

                TagPost::create(['user_id' => Auth::user()->id, 'news_feed_id' => $detail->id, 'trainer_id' => $tag]);
                $title ="Tag Post";
                $message = Auth::user()->name.' '.'Tag you in a post ';
                $userfcm= User::find($tag);
                $fcm_token = $userfcm->fcm_token;
                Helper::sendPushNotification($title,$message,$fcm_token,);

            }
        
    }
        return response()->json([
            'success' => true,
            'data' => $detail
        ], Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $feeds = NewsFeed::Where('user_id',$id) ->with(['tag.user'=> function($query){$query->select('id','username','image','role','cover_image');}])->get();
        foreach ($feeds as $feed){
            $feed->comment =NewsFeedComment::where('news_feed_id',$feed->id)->count();
            $feed->like =LikeNewsFeed::where('news_feed_id',$feed->id)->count();
            if(LikeNewsFeed::where('news_feed_id',$feed->id)->where('user_id',Auth::user()->id)->exists()){

                $feed->islike = true;
            }
            else{
                $feed->islike =false;
            }
        }
        return response()->json([
            'success' => true,
            'data' => $feeds
        ], Response::HTTP_OK);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $data = $request->only('description');

        if ($request->has('picture')){
            $path = $request->file('picture')->store('picture');
            $data['picture'] =$path;
        }

       $feed = NewsFeed::find($id)->update($data+["updated_at"=>Now()]);
        if ($request->tag) {
            TagPost::where('news_feed_id',$id)->delete();
            foreach ($request->tag as $tag) {

                TagPost::create(['user_id' => Auth::user()->id, 'news_feed_id' => $id, 'trainer_id' => $tag]);
                $title ="Tag Post";
                $message = Auth::user()->name.' '.'Tag you in a post ';
                $userfcm= User::find($tag);
                $fcm_token = $userfcm->fcm_token;
                Helper::sendPushNotification($title,$message,$fcm_token,);
            }
        }
        return response()->json([
            'success' => true,
            'data' => "Updated Successfully"
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        NewsFeed::find($id)->comment()->delete();
        NewsFeed::find($id)->like()->delete();
        NewsFeed::find($id)->tag()->delete();
        NewsFeed::find($id)->delete();
        return response()->json([
            'success' => true,
            'data' => "Deleted Successfully"
        ], Response::HTTP_OK);
    }

    /**
     * Like News Feed.
     *
     * @param  int  $request
     * @return \Illuminate\Http\Response
     */
    public function like(Request $request)
    {
        if(LikeNewsFeed::where('news_feed_id',$request->news_feed_id)->where('user_id',Auth::user()->id)->exists()){
            LikeNewsFeed::where('news_feed_id',$request->news_feed_id)->where('user_id',Auth::user()->id)->delete();
            $message ="Unlike Successfully";
        }
        else {
            LikeNewsFeed::create($request->all() + ['user_id' => Auth::user()->id]);
           $getuserbyfeedid =NewsFeed::with('user')->where('id',$request->news_feed_id)->first();
            $title ="like News Feed";
            $message ="Like By ". Auth::user()->name;
            $fcm_token = $getuserbyfeedid->user->fcm_token;
            Helper::sendPushNotification($title,$message,$fcm_token,);
            $message ="Liked Successfully";
        }
        return response()->json([
            'success' => true,
            'message' => $message
        ], Response::HTTP_OK);
    }

    /**
     * Comment News Feed.
     *
     * @param  int  $request
     * @return \Illuminate\Http\Response
     */
    public function comment(Request $request)
    {
        $video  =NewsFeedComment::create($request->all()+['user_id'=>Auth::user()->id]);
        $getuserbyfeedid =NewsFeed::with('user')->where('id',$request->news_feed_id)->first();
        $title ="Comment News Feed";
        $message =$request->comment." comment On Your News Feed By ". Auth::user()->name;
        $fcm_token = $getuserbyfeedid->user->fcm_token;
        Helper::sendPushNotification($title,$message,$fcm_token,);
        return response()->json([
            'success' => true,
            'data' => $video
        ], Response::HTTP_OK);
    }

    /**
     * Comment News Feed likes.
     *
     * @param  int  $request
     * @return \Illuminate\Http\Response
     */
    public function likecount($newsfeedid)
    {
        $video  =LikeNewsFeed::where('news_feed_id',$newsfeedid)->count();
        return response()->json([
            'success' => true,
            'likes' => $video
        ], Response::HTTP_OK);
    }
    
    public function getCommentByNewsId($id){
        $feeds  =NewsFeedComment::with('news_feed_comment_like')->where('news_feed_id',$id)->with(['user'=> function($query){$query->select('id','username','image');}])->get();


               foreach ($feeds as $comment){
                   if(count($comment->news_feed_comment_like)>0) {
                       foreach ($comment->news_feed_comment_like as $comentlike) {
                           if (NewsFeedCommentLike::where(['news_feed_comment_id' => $comentlike->news_feed_comment_id, 'user_id' => Auth::user()->id])->exists()) {
                               $comment->isLike = true;
                           } else {
                               $comment->isLike = false;
                           }
                       }
                   }else{
                       $comment->isLike = false;
                   }
                   $comment->news_feed_comment_like_count =count($comment->news_feed_comment_like);



       }

        return response()->json([
            'success' => true,
            'data' => $feeds
        ], Response::HTTP_OK);
    }

    public function getNewsFeedLikedUser($id){
        $feeds  =LikeNewsFeed::select('news_feed_id','user_id')->where('news_feed_id',$id)->with(['user'=> function($query){$query->select('id','username','image');}])->get();
        return response()->json([
            'success' => true,
            'data' => $feeds
        ], Response::HTTP_OK);
    }

    public function news_feed_comment_like($id){

        if(NewsFeedCommentLike::where(['user_id'=>Auth::user()->id,'news_feed_comment_id'=>$id])->exists()){
            NewsFeedCommentLike::where(['user_id'=>Auth::user()->id,'news_feed_comment_id'=>$id])->delete();
        $message ='Disliked Successfully';
        }
        else{
            NewsFeedCommentLike::create(['user_id'=>Auth::user()->id,'news_feed_comment_id'=>$id]);
            $message ='Liked Successfully';
        }


        return response()->json([
            'success' => true,
            'message' => $message
        ], Response::HTTP_OK);
    }
    public function newsfeedByPage($page)
    {

        $itemperpage=5;
        $offset =($page-1)*$itemperpage;

        $feeds =NewsFeed::with(['user.userprefrence.typespecialtis'=>function($query){$query->select('id','title','description');}])
            ->with(['tag.user'=> function($query){$query->select('id','username','image','role','cover_image');}])->offset($offset)->limit($itemperpage)->orderBy('id','DESC')->get();

        foreach ($feeds as $feed){
            if(LikeNewsFeed::where('news_feed_id',$feed->id)->where('user_id',Auth::user()->id)->exists()){
                $feed->islike = true;
            }
            else{
                $feed->islike =false;
            }
            $feed->likecount =LikeNewsFeed::where('news_feed_id',$feed->id)->count();
            $feed->commentcount =NewsFeedComment::where('news_feed_id',$feed->id)->count();
        }

        return response()->json([
            'success' => true,
            'data' => $feeds,
        ], Response::HTTP_OK);
    }

}
