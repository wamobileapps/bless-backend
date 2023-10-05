<?php

namespace App\Http\Controllers;

use App\Models\User;
use Validator;
use App\Models\Bless;
use App\Models\Video;
use App\Models\VideoLike;
use App\Models\DigitalExerciseLibrary;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    public function index()
    {
        $title = array(
            'title' => 'Home',
            'active' => 'home'
        );
        return view('admin.dashboard', compact('title'));
    }
    public function logout()
    {
        Auth::logout();
        return redirect('login');
    }
    public function index_user()
    {
        $title = array(
            'title' => 'Users',
            'active' => 'users'
        );
        $users = User::latest();
        if (request()->has('search')) {
            $users->where('name', 'Like', '%' . request()->input('search') . '%')
                ->orWhere('email', 'Like', '%' . request()->input('search') . '%')
                ->orWhere('age', 'Like', '%' . request()->input('search') . '%');
        }
        $users = $users->paginate(5)->setPath('');
        $users->appends(array(
            'search' => request()->input('search')
        ));
        return view('admin.user', compact('users', 'title'));
    }

    public function updateStatus(Request $request)
    {
        dd($request->all());
    }

    public function Activate($id)
    {
        $user = User::find($id);
        $user->verify_status = 1;
        $user->save();
        return redirect()->back()->with('message', 'User activated successfuly');
    }
    public function Inactivate($id)
    {
        $user = User::find($id);
        $user->verify_status = 0;
        $user->save();
        return redirect()->back()->with('message', 'User Inactivated successfuly');
    }

    // for update user in admin pannel
    public function update($id)
    {
        $user = User::find($id);
        return view('admin.editusers', compact('user'));
    }

    // delete user from admin pannel 
    public function delete(Request $request, $id)
    {
        $userdelete = User::find($id);
        $userdelete->delete();

        return redirect()->back();
    }

    //update user in admin pannel
    public function updateUser(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'username' => 'required|string|unique:users',
            'phone_number' => 'nullable|required|unique:users',
            'age' => 'required|string',
            'password' => 'required|string|min:6',
            'image' => 'required'

        ]);
         $student = User::find($id);

        $student->name = $request->input('name');
        $student->email = $request->input('email');
        $student->age = $request->input('age');
        // $student->username = $request->input('username');
        $student->phone_number = $request->input('phone_number');
        if ($request->hasFile('image')) {
            $fileName = time() . '_' . $request->image->getClientOriginalName();
            $filePath = $request->file('image')->storeAs('User', $fileName, 'public');
            $student->image     = $fileName;
        }
        // dd($request->all());
        // $student->city = $request->input('city');
        // $student->state = $request->input('state');
        // $student->country = $request->input('country');
        // $student->zip_code = $request->input('zip_code');
        $student->update();
        return redirect()->back()->with('status', 'Student Updated Successfully');
    }

    //for the enable diable the user from admin.
    public function status(Request $request)
    {

        $user = User::find($request->id);

        $user->status = $request->status;
        $user->update();

        return response()->json(['success' => 'Status change successfully.']);
    }

    // add user from admin pannel in user table.
    public function adminadduser(Request $request)
    {
        

        $data = $request->only('first_name', 'last_name', 'dob', 'country', 'state', 'city', 'email', 'password', 'password_confirmation');
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'username' => 'required|string|unique:users',
            'phone_number' => 'nullable|required|unique:users',
            'age' => 'required|string',
            'password' => 'required|string|min:6',
            'image' => 'required'

        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            $message = [
                'message' => $validator->errors()->first()
            ];
            return response()->json($message, 500);
        }
        $originalDate = $request->dob;
        $newDate = date("Y-m-d", strtotime($originalDate));
        //Request is valid, create new user
        if ($request->hasFile('image')) {
            $fileName = time() . '_' . $request->image->getClientOriginalName();
            $filePath = $request->file('image')->storeAs('User', $fileName, 'public');
            $image = $fileName;
        }
        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('cover_image');
        }
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'age' => $request->age,
            'phone_number' => $request->phone_number,
            // 'image' => @$image,
            'image' => $image,
            // 'cover_image' => @$path,
            'cover_image' => $path,
            'country' => $request->country,
            'country_code' => $request->country_code,
            'state' => $request->state,
            'fcm_token' => $request->fcm_token,
            'city' => $request->city,
            'zip_code' => $request->zip_code,
            'password' => bcrypt($request->password),

        ]);
        return redirect()->back()->with('status', 'Student Updated Successfully');
    }

    public function addvideo()
    {
        $categories = DigitalExerciseLibrary::select('id', 'category')->get();

    return view('admin.addvideo', compact('categories'));
    }


    // video 
    public function adminaddvideo(Request $request)
    {
      


        
        $this->validate($request, [
          
            // 'video' => 'required|file|mimetypes:video/mp4|mimetypes:video/x-flv'
            'category_id'=>'required',
            'video' => 'required|file|mimetypes:video/x-flv,video/mp4,application/x-mpegURL,video/MP2T,video/3gpp'
      ]);
    
    

      $videodata = new Video;

        // print_r($request->category_id);die('qqq');
      $videodata->user_id = Auth::user()->id;
   
     

    //   print_r($videodata);die('dssadfsd');
      

      if ($request->hasFile('video'))
      {
        
        $path = $request->file('video')->store('videos');

         $videodata->video = $path;
      }
   
      $videodata->category_id=$request->category_id;
      $videodata->save();
    //   $videodata->save();

      return redirect()->route('user.addvideo')
      
      ->with('message', 'Student Updated Successfully');

    // echo"fsdfasfsadf";
    //     // Request is valid, create new user
    //     if ($request->hasFile('video')) {
    //         $fileName = time() . '_' . $request->video->getClientOriginalName();
    //        $filePath = $request->file('video')->storeAs('video', $fileName, 'public');
    //         $image = $fileName;
    //     }
    //     // $user = video::create([
    //     // //    'video' => @$image,
    //     // //     'cat' => $request->id,
    
    //     return redirect()->route('user.addvideo')->with('status', 'Student Updated Successfully');
    // }
    }


    public function show_video_list(Request $request)
    {
        // echo "cdcdc";
       $showvideodata =Video::all();
   
       return view('admin.showdelete_video', compact('showvideodata'));

    }    


    public function show_video_list_delete(Request $request, $id)
    {

        // echo"fdfasdfasdfasdfasdf";
        $userdelete = Video::find($id);
        // print_r($userdelete);
        $userdelete->delete();

        return redirect()->back();
    }
    // delete_show
    
}
