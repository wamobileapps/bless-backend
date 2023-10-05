<?php

namespace App\Http\Controllers;


use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\BookAppointment;
use App\Models\BookingSlout;
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
   
        $time =array();
           foreach ($request->time as $timeSlout){
               $book= BookAppointment::create($request->except('time')+['slout_id'=>$timeSlout]);
             BookingSlout::whereId($timeSlout)->update(['status'=>1,'user_id'=>Auth::user()->id]);
             $time[]=BookingSlout::whereId($timeSlout)->first('slout');
           }
        $slout = json_encode($time);
           
       $trainer =User::find($request->trainer_id);
       $title ="Book Appointment";
       $bookid =$book->id;
        $message ="Appointment Booked By"." ". Auth::user()->name .' '."Appointment Date".' '.$book->date.' '."Appointment Time".' '.$book->time;
        $fcm_token =$trainer->fcm_token;

        Helper::sendPushNotification($title,$message,$fcm_token,$bookid,$slout);
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
       $bookAppointmentslouts = BookAppointment::find($bookAppointment);
       $bookAppointmentslouts = json_decode($bookAppointmentslouts->slout_id);

             BookingSlout::whereId($bookAppointmentslouts)->update(['status'=>1,'user_id'=>Auth::user()->id]);


        $book = BookAppointment::find($bookAppointment);

        if($request->status == 1){
            $message ="Appointment Accepeted By"." ".Auth::user()->name;
            $title ="Appointment Accepeted";
        }else{
            $message ="Appointment Rejected By"." ".Auth::user()->name;
            $title ="Appointment Rejected";
        }
        Notification::where('reciever_id',Auth::user()->id)->where('title',"=","Book Appointment")->where('book_id',$bookAppointment)->update(['read'=>1]);
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


    public function getAppointmentOfTrainer($id){
        $todayappointments =BookAppointment::where('trainer_id',$id)
            ->whereDate('date', '>=', Carbon::today())
            ->get();

        return \response()->json(['status'=>true,'data'=>$todayappointments]);
    }
    public function addSlout(Request $request)
    {
        BookingSlout::where(['trainer_id'=>Auth::user()->id,'date'=>$request->date,])->delete();
      foreach ($request->slout as $slout){
          BookingSlout::create(['date'=>$request->date,'slout'=>$slout,'trainer_id'=>Auth::user()->id]);
      }
        return \response()->json([
            'status'=>true,
            'message' => 'Slout Added Successfully'
        ]);
    }
    public function getSlout($date ,$trainerid,$type)
    {
if($type == 'client'){
    $slouts = BookingSlout::with('trainer')->where(['user_id'=>$trainerid,'date'=>$date])->get();

}
else{
    $slouts = BookingSlout::with('user')->where(['trainer_id'=>$trainerid,'date'=>$date])->get();

}
        return \response()->json([
            'status'=>true,
            'data'=>$slouts
        ]);
    }
}
