<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BuildMyWorkoutModel;
use App\Models\User;
use Auth;
use DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
class BuildMyWorkoutController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {


        $sharedvideo = DB::table('share_workouts')
            ->join('videos','share_workouts.video_id','=','videos.id')
            ->where('share_workouts.video_id', '!=','')
            ->where('share_workouts.client_id',Auth::user()->id)
       ->get();




        $build =User::select('users.name','users.id','users.image')
            ->with(['buildmyworkout.category'=>function($query){$query->select('id','category');}])
            ->with(['shareworkout.build_my_workout.category'=>function($query){$query->select('id','category');}])
            ->where('id',Auth::user()->id)->get();

        $final =array();
        foreach ($build as $bu){

            foreach ($bu->buildmyworkout as $myworkout){
                $myworkout->status =false;
            $final[] =$myworkout;
            }
            foreach ($bu->shareworkout as $myworkout){
                if($myworkout->build_my_workout) {
                    $myworkout->build_my_workout->status = true;
                    $final[] = $myworkout->build_my_workout;
                }

            }
        }
        return response()->json([
            'success' => true,
            'data' => $final,
            'sharedvideo'=>$sharedvideo
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
        $build =BuildMyWorkoutModel::create($request->all()+['user_id'=>Auth::user()->id]);
        return response()->json([
            'success' => true,
            'data' => $build
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
         BuildMyWorkoutModel::find($id)->update($request->all());
        $build=  BuildMyWorkoutModel::find($id);
        return response()->json([
            'success' => true,
            'data' => $build
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
        BuildMyWorkoutModel::find($id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Deleted Successfully'
        ], Response::HTTP_OK);
    }
}
