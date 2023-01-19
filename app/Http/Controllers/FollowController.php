<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Follow;
use App\Models\User;
use Auth;
use App\Helper\Helper;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
class FollowController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        if(Follow::where('trainer_id',$request->trainer_id)->where('user_id',Auth::user()->id)->exists()){
            Follow::where('trainer_id',$request->trainer_id)->where('user_id',Auth::user()->id)->delete();
            $message ="Unfollowed Successfully";
        }else {
            Follow::create(['trainer_id' => $request->trainer_id, 'user_id' => Auth::user()->id]);
            $user =User::find($request->trainer_id);
            $title ="Follow";
            $message ="Followed By ". Auth::user()->name;
            $fcm_token = $user->fcm_token;
            Helper::sendPushNotification($title,$message,$fcm_token,);
            $message ="Followed Successfully";
        }
        return \response()->json(['status'=>200,'message'=>$message]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($trainer_id)
    {
       return \response()->json(['status'=>200,'followers'=>Follow::with(['user'=> function($query){$query->select('id','username','image');}])->where('trainer_id',$trainer_id)->get()]);

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
