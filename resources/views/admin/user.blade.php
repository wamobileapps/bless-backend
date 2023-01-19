@extends('admin.master')

@section('content')
<style type="text/css">
  .navigation-nre{
    margin-bottom: 35px;
    text-align: center;
  }

  .navigation-nre span svg{
     width: 2%
  }
   .navigation-nre nav div:first-child{
     display: none;
  }

  .block-and-rd{
/*    display: flex;
    gap:20px;*/
  }

.switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 34px;
}

.switch input { 
  opacity: 0;
  width: 0;
  height: 0;
}

   .toggle.android { border-radius: 0px;}
  .toggle.android .toggle-handle { border-radius: 0px; }

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 26px;
  width: 26px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

input:checked + .slider {
  background-color: #2196F3;
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}

/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}

</style>
<section class="content-wrapper">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6">
            <h2 class="m-0">{{$title['title']}}</h2>

                     @if(session()->has('message'))
                  <div class="alert alert-success">
                      {{ session()->get('message') }}
                  </div>
              @endif
          </div><!-- /.col -->
          <div class="col-12">
            <div class="card">
              <form method="GET" action="{{ route('users') }}">
                                             <div class="py-2 flex">
                                                   <div class="overflow-hidden flex pl-4">
                                                     <input type="search" name="search" value="{{ request()->input('search') }}" class="rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="Search">
                                                     <button type='submit' class='ml-4 inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150'>
                                                       {{ __('Search') }}
                                                     </button>
                                                   </div>
                                             </div>
                                                </form>
              <!-- /.card-header -->
              <div class="card-body">
                <table class="table table-bordered table-hover">
                  <thead>
                  <tr>
                    <th>S.No</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Age</th>
                    <th>Image</th>
                    <th>Status</th>
                  </tr>
                  </thead>
                  <tbody>
                    
     @foreach($users as $key => $user)

        <tr>
          <td>{{ ($users->currentpage()-1) * $users->perpage() + $key + 1 }}</td>
          <td>{{$user->name}}</td>
          <td>{{$user->email}}</td>
          <td>{{$user->age}}</td>
          <td>
          <a target="_blank" href="{{URL::asset("/storage/User/$user->image")}}">
          <img style ="height:81px; width: 150px;"src="{{URL::asset("/storage/User/$user->image")}}"> </a></td>
          
           <td>
               <input type="checkbox" checked data-toggle="toggle" data-style="android" data-onstyle="info">
           </td>

        </tr>
    @endforeach
    </tbody>
                </table>
              </div>
             <div class="col-md-12">
           <div class="navigation-nre">
              {{$users->links()}}
           </div>
            </div>
          </div>
        </div>
      </div>   
    </section>

@endsection