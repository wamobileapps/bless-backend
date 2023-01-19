<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Note;
use App\Models\ParentNote;
use App\Models\ShareNote;
use App\Helper\Helper;
use App\Models\User;
use Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $note = User::with('share_note','parentnote')->with(['note'=> function($query){$query->where('parent_id',0);}])->where('id',Auth::user()->id)->get();


      $notes = array();
          $parentnote = array();
            foreach ($note as $not){
        foreach ($not->share_note as $sharenote){
            if($sharenote->note_id !='') {
                $note =Note::where('id',$sharenote->note_id)->where('parent_id',null)->first();
                if($note) {
                    $note->user =User::whereId($note->user_id)->first()->name;
                    $note->status = true;
                    $notes[] = $note;
                }
            }
            if($sharenote->parent_note_id !=''){

                $parent=ParentNote::where('id',$sharenote->parent_note_id)->first();
                if($parent) {
                    $parent->user =User::whereId($parent->user_id)->first()->name;
                    $parent->status = true;
                    $parentnote[] = $parent;
                }

            }
        }
                foreach ($not->note as $sharenote){
                    $sharenote->user =User::whereId($sharenote->user_id)->first()->name;
                    $sharenote->status =false;
                    $notes[] =$sharenote;
                }
                foreach ($not->parentnote as $sharenote){
                    $sharenote->user =User::whereId($sharenote->user_id)->first()->name;
                    $sharenote->status =false;
                    $parentnote[] =$sharenote;
                }



            }
            $data= array('note'=>$notes,'parent'=>$parentnote);

        
        return response()->json([
            'success' => true,
            'data' => $data
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
      
       $note = Note::create($request->all()+['user_id'=>Auth::user()->id]);
        return response()->json([
            'success' => true,
            'data' => $note
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

        Note::find($id)->update($request->all());
        $note = Note::find($id);
        return response()->json([
            'success' => true,
            'data' => $note
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
        Note::find($id)->delete();
        return response()->json([
            'success' => true,
            'message' => "Delete Successfully"
        ], Response::HTTP_OK);
    }
    public function share_note(Request $request)
    {
        $share = array();
        if ($request->parent_note_id) {

            foreach ($request->parent_note_id as $workout) {
                if(ShareNote::where('parent_note_id',$workout)->where('client_id',$request->client_id)->exists()){
                    $alreadyexist[] =$workout;

                }else {
                    $share[] = ShareNote::create(['parent_note_id' => $workout, 'client_id' => $request->client_id]);
                }
            }
        }
        if ($request->note_id) {
            foreach ($request->note_id as $workout) {
                if(ShareNote::where('note_id',$workout)->where('client_id',$request->client_id)->exists()){
                    $alreadyexist[] =$workout;

                }else {
                    $share[] = ShareNote::create(['note_id' => $workout, 'client_id' => $request->client_id]);
                }
            }
        }
        if(!empty($share)){
            $user =User::find($request->client_id);
            $title ="Share Note";
            $message ="Note Shared By". Auth::user()->name;
            $fcm_token = $user->fcm_token;
            Helper::sendPushNotification($title,$message,$fcm_token,);

        }
        return response()->json([
            'success' => true,
            'response' => $share,
            'allreadyexist'=>@$alreadyexist
        ], Response::HTTP_OK);
    }
        public function get_share_note(){

            $share =ShareNote::where('client_id',Auth::user()->id)->get();

            return response()->json([
                'success' => true,
                'response' => $share
            ], Response::HTTP_OK);
        }

    
}
