<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helper\Helper;
use App\Models\Question;
use App\Models\Answer;
use App\Models\Option;
use Symfony\Component\HttpFoundation\Response;
class QuizController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($page)
    {
        $itemperpage=5;
        $offset =($page-1)*$itemperpage;
        $question =Question::with(['options','answer'])->offset($offset)->limit($itemperpage)->orderBy('id','DESC')->get();
        return response()->json([
            'success' => true,
            'data' => $question
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
        $question_id =Question::create(['question'=>$request->question]);
        foreach ($request->option as $key=> $option){
    Option::create(['option'=>$key ,'answer'=>$option,'question_id'=>$question_id->id]);
        }
        Answer::create(['answer'=>$request->correct_option,'question_id'=>$question_id->id]);
        return response()->json([
            'success' => true,
            'message' => 'Question Created Successfully'
        ], Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($skip)
    {
        $limit = 10;
        $question =Question::with(['options','answer'])->skip($skip)->take($limit)->get();
        return response()->json([
            'success' => true,
            'data' => $question
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
