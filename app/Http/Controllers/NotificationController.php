<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\CallNotification;
use App\Models\User;
use Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Helper\Helper;
class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $notification = Notification::select('users.role','book_time','notifications.sender_id as senderId','users.name as title','find_my_professions.category_name', 'users.image', 'notifications.message', 'notifications.book_id', 'notifications.title as types', 'notifications.id', 'notifications.created_at as time', 'notifications.read as Isread')
            ->join("users", 'users.id', '=', "notifications.sender_id")
            ->join("find_my_professions", 'find_my_professions.id', '=', "users.role")
            ->where('notifications.reciever_id', \Auth::user()->id)->get();
     
        return response()->json([
            'success' => true,
            'data' => $notification
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
    
        $notification = CallNotification::create($request->all());
        $user =User::find($request->senderid);
        $title =$request->type;
        $message =$request->type." By ". Auth::user()->name;
        $fcm_token = $user->fcm_token;
        Helper::sendPushNotification($title,$message,$fcm_token,);
        return response()->json([
            'success' => true,
            'data' => $notification
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
    public function update(Request $request)
    {

        Notification::where('reciever_id', Auth::user()->id)->where('title', "!=", "Book Appointment")->update(['read' => 1]);
        return response()->json([
            'success' => true,
            'message' => "Readed",
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
        //
    }
}
