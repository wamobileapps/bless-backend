<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\BookAppointment;
use App\Helper\Helper;
use App\Models\User;
use App\Models\Notification;
use Auth;
use Symfony\Component\HttpFoundation\Response;

class BookAppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return \response()->json([
            "status"=>true,
            "data"=>BookAppointment::with(['client'=>function($query){$query->select('id','name','email','image');}])->where('trainer_id',Auth::user()->id)->where('status',0)->get()
        ]);
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
       $book= BookAppointment::create($request->all());

       $trainer =User::find($request->trainer_id);
       $title ="Book Appointment";
       $bookid =$book->id;
        $message ="Appointment Booked By"." ". Auth::user()->name ."Appointment Date".$book->date."Appointment Time".$book->time;
        $fcm_token =$trainer->fcm_token;

        Helper::sendPushNotification($title,$message,$fcm_token,$bookid);
        return \response()->json([
            'status'=>true,
            'book'=>$book
        ]);
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
    public function update(Request $request,$bookAppointment)
    {

       BookAppointment::find($bookAppointment)->update($request->all());
        $book = BookAppointment::find($bookAppointment);

        if($request->status == 1){
            $message ="Appointment Accepeted By"." ".Auth::user()->name;
            $title ="Appointment Accepeted";
        }else{
            $message ="Appointment Rejected By"." ".Auth::user()->name;
            $title ="Appointment Rejected";
        }
        Notification::where('reciever_id',Auth::user()->id)->where('title',"=","Book Appointment")->update(['read'=>1]);
        $client =User::find($book->client_id);
        
        $title =$title;
        $message =$message;
        $fcm_token =$client->fcm_token;

        Helper::sendPushNotification($title,$message,$fcm_token);
        return \response()->json([
            'status'=>true,
            'status'=>$title
        ]);

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
