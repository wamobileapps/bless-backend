<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\Answer;
use App\Models\Option;
use Illuminate\Support\Facades\Validator;

class QuizController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $question =Question::with(['options','answer'])->paginate(10);

        return view('admin.quiz.list',compact('question'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.quiz.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'question' => 'required|max:255',
            'answer' => 'required',
        ]);
//print_r($request->all());die('herr');

        $question_id =Question::create(['question'=>$request->question]);
        foreach ($request->option as $key=> $option){
            try {
                Option::create(['option' => $key, 'answer' => $option, 'question_id' => $question_id->id]);
            }
            catch (\Exception $e) {
                // Handle the exception
                return redirect()->back()->withErrors('Make Sure All Option Are Filled');
            }
        }
        Answer::create(['answer'=>$request->answer,'question_id'=>$question_id->id]);
       return \redirect()->route('quiz.index')->with('message','Question Added Successfully');
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
        Option::where('question_id',$id)->delete();
        Answer::where('question_id',$id)->delete();
        Question::whereId($id)->delete();
        return redirect()->route('quiz.index')->with('message','Question has been deleted successfully');

    }
}
