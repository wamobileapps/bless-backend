<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Video;
use App\Models\VideoLike;
use App\Helper\Helper;

use Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class LikeVideoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       
        $data =VideoLike::where('user_liked_id',Auth::user()->id)->count();

        return response()->json([
            'success' => true,
            'data' => $data
        ], Response::HTTP_OK);
    }

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
       if(VideoLike::where('video_id',$request->video_id)->where('user_liked_id',Auth::user()->id)->exists()){
           VideoLike::where('video_id',$request->video_id)->where('user_liked_id',Auth::user()->id)->delete();
           $message ="Unlike Successfully";
       }
       else {
         VideoLike::create($request->all() + ['user_liked_id' => Auth::user()->id]);
           $getuserbyvideoid =Video::with('user')->where('id',$request->video_id)->first();
           $title ="Like Video";
           $message ="Video liked By ". Auth::user()->name;
           $fcm_token = $getuserbyvideoid->user->fcm_token;
           Helper::sendPushNotification($title,$message,$fcm_token,);
         $message ="Liked Successfully";
       }
        return response()->json([
            'success' => true,
            'message' => $message
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
        $data =VideoLike::where('video_id',$id)->count();
        return response()->json([
            'success' => true,
            'data' => $data
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
