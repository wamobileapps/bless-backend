<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Bless;
use Auth;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $title= array(
            'title' => 'Home',
            'active' =>'home'
        );
        return view('admin.dashboard',compact('title'));
    }
    public function logout()
    {
        Auth::logout();
        return redirect('login');
    }
    public function index_user()
    {
        $title= array(
            'title' => 'Users',
            'active' =>'users'
        );
        $users = User::latest();
        if (request()->has('search')) {
            $users->where('name', 'Like', '%' . request()->input('search') . '%')
                ->orWhere('email', 'Like', '%' . request()->input('search') . '%')
                ->orWhere('age', 'Like', '%' . request()->input('search') . '%');
        }
        $users = $users->paginate (5)->setPath ( '' );
        $users->appends(array(
            'search'=>request()->input('search')
        ));
        return view('admin.user',compact('users','title'));
    }
    public function Activate($id)
    {
        $user=User::find($id);
        $user->verify_status=1;
        $user->save();
        return redirect()->back()->with('message','User activated successfuly');

    }
     public function Inactivate($id)
    {
        $user=User::find($id);
        $user->verify_status=0;
        $user->save();
        return redirect()->back()->with('message','User Inactivated successfuly');

    }
}
