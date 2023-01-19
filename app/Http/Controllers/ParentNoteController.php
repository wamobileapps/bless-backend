<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ParentNote;
use App\Models\Note;
use Symfony\Component\HttpFoundation\Response;
use Auth;
class ParentNoteController extends Controller
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
       $parentNote = ParentNote::create($request->all()+['user_id'=>Auth::user()->id]);
        return response()->json([
            'success' => true,
            'data' => $parentNote
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
        ParentNote::find($id)->update($request->all());
        $parent =ParentNote::find($id);
        return response()->json([
            'success' => true,
            'data' => $parent,
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
        Note::where('parent_id',$id)->delete();
        ParentNote::find($id)->delete();
        return response()->json([
            'success' => true,
            'message' => 'Deleted Successfully',
        ], Response::HTTP_OK);
    }

    public function getNoteByFolder($id)
    {
        $note =Note::where('parent_id',$id)->get();
        return response()->json([
            'success' => true,
            'data' => $note,
        ], Response::HTTP_OK);
    }
}
