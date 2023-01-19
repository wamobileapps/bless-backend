<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Video;
use App\Models\VideoLike;
use App\Models\VideoComment;
use App\Helper\Helper;
use Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
class VideoCommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

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
        $video  =VideoComment::create($request->all()+['user_id'=>Auth::user()->id]);
        $getuserbyvideoid =Video::with('user')->where('id',$request->video_id)->first();
        $title ="Comment Video";
        $message =$request->comment." comment On Your Video By ". Auth::user()->name;
        $fcm_token = $getuserbyvideoid->user->fcm_token;
        Helper::sendPushNotification($title,$message,$fcm_token,);
        return response()->json([
            'success' => true,
            'data' => $video
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
        
        $data =VideoComment::with(['users'=> function($query){ $query->select('id','username','image');}])->where('video_id',$id)->get();
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
