<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BuildMyWorkoutVideo;
use App\Models\BuildMyWorkoutModel;
use App\Models\ShareWorkout;
use App\Models\LikeBuildMyWorkout;
use App\Models\BuildMyWorkoutComment;
use App\Models\Video;
use Auth;
use App\Helper\Helper;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

use Symfony\Component\HttpFoundation\Response;


class BuildMyWorkoutVideoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $detail =BuildMyWorkoutModel::where('user_id',Auth::user()->id)->get();

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
    public function getVideoByWorkoutId($id)
    {
        $video =BuildMyWorkoutVideo::query()
            ->with(['user' => function ($query) {$query->select('id', 'username','image');}])
            ->with(['video'=> function ($query){$query->select('id','video');}])
            ->with(['day'=> function ($query){$query->select('id','name');}])->where('build_my_workout_id',$id)->get();
        foreach ($video as $vid) {
            if (LikeBuildMyWorkout::where('user_liked_id', Auth::user()->id)->where('build_workout_video_id',$vid->id)->exists()) {

                $vid ->islike = True;
            } else {
                $vid->islike = false;
            }
        }
        return response()->json([
            'success' => true,
            'data' => $video,
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $path = $request->file('video')->store('buildmyworkoutvideos');
        $vdata = $request->only('category_id');
        $detail =Video::create($vdata+['user_id'=>Auth::user()->id,'video'=>$path]);
        $data = $request->only('build_my_workout_id','description','day_id');
        $details =BuildMyWorkoutVideo::create($data+['user_id'=>Auth::user()->id,'video_id'=>$detail->id]);
        return response()->json([
            'success' => true,
            'data' => $details
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
        //
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

        BuildMyWorkoutVideo::find($id)->update($request->all());
        $detail=  BuildMyWorkoutVideo::find($id);
        return response()->json([
            'success' => true,
            'data' => $detail
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
        BuildMyWorkoutVideo::find($id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Deleted Successfully'
        ], Response::HTTP_OK);
    }

    public function share_workout(Request $request){

        $share=array();
        $alreadyexist =array();
        if($request->build_my_workout_id) {
            foreach ($request->build_my_workout_id as $workout) {
                if (ShareWorkout::where('build_my_workout_id', $workout)->where('client_id', $request->client_id,"shared_by",Auth::user()->id)->exists()) {
                    $alreadyexist[] = $workout;

                } else {
                    $share[] = ShareWorkout::create(['build_my_workout_id' => $workout, 'client_id' => $request->client_id,"shared_by"=>Auth::user()->id]);
                }


            }
        }
        if($request->video_id) {
            foreach ($request->video_id as $workout) {
                if (ShareWorkout::where('video_id', $workout)->where('client_id', $request->client_id,"shared_by",Auth::user()->id)->exists()) {
                    $alreadyexist[] = $workout;

                } else {
                    $share[] = ShareWorkout::create(['video_id' => $workout, 'client_id' => $request->client_id,"shared_by"=>Auth::user()->id]);
                }


            }
        }

       
        if(!empty($share)){
            $user =User::find($request->client_id);
            $title ="Share Note";
            $message ="Note Shared By". Auth::user()->name;
            $fcm_token = $user->fcm_token;
            Helper::sendPushNotification($title,$message,$fcm_token,);

        }

        return response()->json([
            'success' => true,
            'response' =>$share,
            'allreadyexist'=>$alreadyexist
        ], Response::HTTP_OK);
    }
    public function get_share_workout($id){


        $share=ShareWorkout::with('build_my_workout')->where('client_id',$id)->get();


        return response()->json([
            'success' => true,
            'response' =>$share
        ], Response::HTTP_OK);
    }

//    Like video Of Build My Workout
    public function buildmyworkoutvideolike(Request $request){
        if(LikeBuildMyWorkout::where('build_workout_video_id',$request->build_workout_video_id)->where('user_liked_id',Auth::user()->id)->exists()){
            LikeBuildMyWorkout::where('build_workout_video_id',$request->build_workout_video_id)->where('user_liked_id',Auth::user()->id)->delete();
            $message ="Unlike Successfully";
            $success =false;
        }
        else {
            LikeBuildMyWorkout::create($request->all() + ['user_liked_id' => Auth::user()->id]);
          $getuserbyvideoid =Video::with('user')->where('id',$request->build_workout_video_id)->first();


            $title ="Like BuildMyWorkout";
            $message ="Workout liked By". Auth::user()->name;
            $fcm_token = $getuserbyvideoid->user->fcm_token;
            Helper::sendPushNotification($title,$message,$fcm_token,);
            $message ="Liked Successfully";
            $success =true;
        }
        return response()->json([
            'success' => $success,
            'message' => $message
        ], Response::HTTP_OK);
    }
//Comment For build My Workout video
   public function buildmyworkoutvideocomment(Request $request)
   {
       $video  =BuildMyWorkoutComment::create($request->all()+['user_id'=>Auth::user()->id]);

       $getuserbyvideoid =BuildMyWorkoutVideo::with('user')->where('id',$request->build_my_workout_video_id)->first();

       $title ="Comment BuildMyWorkout";
       $message =$request->comment." comment On Your Workout". Auth::user()->name;
       $fcm_token = $getuserbyvideoid->user->fcm_token;
       Helper::sendPushNotification($title,$message,$fcm_token);
       return response()->json([
           'success' => true,
           'data' => $video
       ], Response::HTTP_OK);
   }

//   Get Comment on Video
    public function getbuildmyworkoutvideocomment($id)
    {
        $comment  =BuildMyWorkoutComment::with(['user' => function ($query) {
            $query->select('id', 'username','image');
        }])->where('build_my_workout_video_id',$id)->get();
        return response()->json([
            'success' => true,
            'data' => $comment
        ], Response::HTTP_OK);
    }

    public function getbuildmyworkoutvideolikes($id)
    {
        $like  =LikeBuildMyWorkout::where('build_workout_video_id',$id)->count();
        return response()->json([
            'success' => true,
            'data' => $like
        ], Response::HTTP_OK);
    }

    public function add_video_build_my_workout(Request $request){
        $validator = Validator::make($request->all(), [
            'day_id' => 'required',

        ]);
        //Send failed response if request is not valid
        if ($validator->fails()) {
            $message = [
                'message' => $validator->errors()->first()
            ];
            return response()->json($message,500);
        }
        $vid =Video::with('category')->where('id',$request->id)->first();

        $build = BuildMyWorkoutModel::where('user_id',Auth::user()->id)->get();
       $bu_id ='';
        foreach ($build as $bu) {
            if ($bu->category_id == $vid->category_id) {
                $bu_id=$bu->id;

            }
        }
        if($bu_id) {
            $data = array();
            $data['video_id'] = $vid->id;
            $data['user_id'] = Auth::user()->id;
            $data['build_my_workout_id'] = $bu_id;
            $data['day_id'] = $request->day_id;

            BuildMyWorkoutVideo::create($data);
        }
            else{
                $build =BuildMyWorkoutModel::create([
                    'category_id'=>$vid->category_id,
                    'title'=>$vid->category->category,
                    'user_id'=>Auth::user()->id
                ]);

                $data =array();
                $data['video_id'] =$vid->id;
                $data['user_id'] =Auth::user()->id;
                $data['build_my_workout_id'] =$build->id;
                $data['day_id'] =$request->day_id;
                BuildMyWorkoutVideo::create($data);
            }


        return response()->json([
            'success' => true,
            'message' => "Video Added to build my workout"
        ], Response::HTTP_OK);

    }


    public function build_my_workout_by_user_id($id){
        $video =BuildMyWorkoutVideo::select('video_id','day_id','created_at')->with('video')->where('user_id',$id)->get();
        return response()->json([
            'success' => true,
            'message' => $video
        ], Response::HTTP_OK);
    }
}
