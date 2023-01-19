<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Video;
use App\Models\VideoLike;
use App\Models\BuildMyWorkoutVideo;
use Auth;
use Storage;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use FFMpeg\Filters\Video\VideoFilters;
use FFMpeg;
use FFMpeg\Coordinate\Dimension;
use FFMpeg\Format\Video\X264;
class VideoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $detail =Video::all();
        foreach ($detail as $vid) {
            if (VideoLike::where('user_liked_id', Auth::user()->id)->where('video_id',$vid->id)->exists()) {

                $vid ->islike = True;
            } else {
                $vid->islike = false;
            }
            if( BuildMyWorkoutVideo::where('video_id',$vid->id)->where('user_id',Auth::user()->id)->exists()){
                $vid ->isBuildMyWorkout = true;
            }
            else{
                $vid ->isBuildMyWorkout = false;
            }
        }
        return response()->json([
            'success' => true,
            'data' => $detail
        ], Response::HTTP_OK);
    }
    public function videoByCategoryId($id)
    {
        $detail = Video::where('category_id',$id)->get();
        foreach ($detail as $vid) {
            if (VideoLike::where('user_liked_id', Auth::user()->id)->where('video_id',$vid->id)->exists()) {

                $vid ->islike = True;
            } else {
                $vid->islike = false;
            }
            if( BuildMyWorkoutVideo::where('video_id',$vid->id)->where('user_id',Auth::user()->id)->exists()){
                $vid ->isBuildMyWorkout = true;
            }
            else{
                $vid ->isBuildMyWorkout = false;
            }
        }
        return response()->json([
            'success' => true,
            'data' => $detail
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


        $path = $request->file('video')->store('video');
        $data = $request->only('category_id');
        $detail =Video::create($data+['user_id'=>Auth::user()->id,'video'=>$path]);
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
        $data =Video::find($id)->with('comment')->get();
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
