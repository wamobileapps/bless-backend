<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserType;
use App\Models\UserPrefrence;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;
class UserTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $usertype = UserType::all();
        return response()->json([
            'success' => true,
            'data' => $usertype
        ], Response::HTTP_OK);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->hasFile('image')) {
            $path = $request->file('image')->store('type');
        }
        else{
            $path ='';
        }
        $data = $request->only('title','role_id');
       $usertype = UserType::create($data+['images'=>$path]);
        return response()->json([
            'success' => true,
            'data' => $usertype
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

    public function get_user_by_user_type(Request $request){

        $data= UserPrefrence::whereIn('type_specialties_id',$request->ids)->with('user')->get();


        return response()->json([
            'success' => true,
            'data' => $data
        ], Response::HTTP_OK);
    }
}
