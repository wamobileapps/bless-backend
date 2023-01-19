<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserType;
use App\Models\TypeSpecialist;
use App\Models\UserPrefrence;
use App\Models\UserProfessionalDetails;
use DB;
use App\Models\User;
use App\Models\Follow;
use App\Models\NewsFeed;
use App\Models\LikeNewsFeed;
use App\Models\NewsFeedComment;
use App\Models\BookAppointment;
use Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class UserTypeSpecialties extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $typeSpecialist =TypeSpecialist::all();
        return response()->json([
            'success' => true,
            'data' => $typeSpecialist
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

        $typeSpecialist =TypeSpecialist::create($request->all());
        return response()->json([
            'success' => true,
            'data' => $typeSpecialist
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

    /**
     * Get User By specialities.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getUserBySpecialites(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'specialities' => 'required'

        ]);
        if ($validator->fails()) {
            $message = [
                'message' => $validator->errors()->first()
            ];
            return response()->json($message,500);
        }
          $data =UserPrefrence::whereIn('type_specialties_id',$request->specialities)
            ->with(['typespecialtis'=> function($query){$query->select('id','title');}])
            ->with(['user'=> function($query){$query->select('id','name','username','image','country','state','city','zip_code','fcm_token');}])
           ->get();

        $unique = array();

        foreach ($data as $value)
        {
            $unique[$value->user->id] = $value;
        }

        $data = array_values($unique);
              foreach ($data as $da){

                  $c =DB::table('tbl_countries')->select('name')->where('id',$da->user->country)->first();
                  $s =DB::table('states')->select('name')->where('id',$da->user->state)->first();
                  $ci =DB::table('cities')->select('name')->where('id',$da->user->city)->first();
                  $da->country =@$c->name;
                  $da->state =@$s->name;
                  $da->city =@$ci->name;
                  $da->zip_code =@$da->user->zip_code;
                  $da->user->description =UserProfessionalDetails::where('user_id',$da->user->id)->first()->description;
                  $feeds = NewsFeed::Where('user_id',$da->user->id)->get();
                  foreach ($feeds as $feed){
                  

                      if(LikeNewsFeed::where('news_feed_id',$feed->id)->where('user_id',$da->user->id)->exists()){
                          $feed->islike = true;
                      }
                      else{
                          $feed->islike =false;
                      }
                  }
                  $da->post =$feeds;
                  $da->appointment =BookAppointment::where('client_id',Auth::user()->id)->where('trainer_id',$da->user->id)->get();

                  if(Follow::where('trainer_id',$da->user->id)->where('user_id',$da->user->id)->exists()){
                  $da->isFollow =true;
                  }
                  else{
                      $da->isFollow =false;
                  }

              }

        return response()->json([
            'success' => true,
            'data' => $data
        ], Response::HTTP_OK);
    }
}
