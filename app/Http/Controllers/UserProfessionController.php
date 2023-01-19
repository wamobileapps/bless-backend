<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserPrefrence;
use Illuminate\Http\Request;
use App\Models\UserProfessionalDetails;
use Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
class UserProfessionController extends Controller
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

     
        $validator = Validator::make($request->all(), [
            'certificate_number' => 'required|unique:user_professional_details|numeric',
            'certificate_image' => 'required',
            'license_id' => 'required|unique:user_professional_details|numeric',
            'specialities'=>'required'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            $message = [
                'message' => $validator->errors()->first()
            ];
            return response()->json($message,500);
        }

        $path = $request->file('certificate_image')->store('public/certificate_image');
        $data = $request->only('profession_id','certificate_number','license_id','description');

        $detail =UserProfessionalDetails::create($data+['user_id'=>Auth::user()->id,'certificate_image'=>$path]);
        foreach ($request->specialities as $spec){
            UserPrefrence::create(['user_id'=>Auth::user()->id,'type_specialties_id'=>$spec]);
        }
          User::where('id',Auth::user()->id)->update(['role'=>1]);
           if($detail){
              $status= User::find(Auth::user()->id);
           $status->verify_status= 1;
           $status->save();
           }
        return response()->json([
            'success' => true,
            'data' => $detail,
            'status'=>Auth::user()->verify_status
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
}
