@extends('admin.master')

@section('content')

<section class="content-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
                <h2 class="m-0">Edit User</h2>

                @if(session()->has('message'))
                <div class="alert alert-success">
                    {{ session()->get('message') }}
                </div>
                @endif
                @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div><!-- /.col -->
            <div class="col-12">
                <div class="card">

                    <!-- /.card-header -->
                    <div class="card-body">
                        <form action="{{route('user.edit', $user->id)}}" method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                @csrf()
                                <label for="exampleInputEmail1">Name</label>
                                <input type="text" class="form-control" name="name" value="{{$user->name}}">

                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">Email</label>
                                <input type="text" class="form-control" name="email" value="{{$user->email}}">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">Image</label>
                               
                                <input type="file"  id="fileToUpload" name="image" value="{{$user->image}}">
                            </div>
                            <div class="form-group">
                            <img style="height:81px; width: 150px;" src="{{URL::asset("/storage/User/$user->image")}}" />
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">Username</label>
                                <input type="text" class="form-control" disabled value="{{$user->username}}">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">Age</label>
                                <input type="text" class="form-control"  name="age" value="{{$user->age}}">
                            </div>

                            <!-- <div class="form-group">
                                <label for="exampleInputEmail1">Password</label>
                                <input type="text" class="form-control" value="">
                            </div> -->

                            <div class="form-group">
                                <label for="exampleInputEmail1">Phone number</label>
                                <input type="text" class="form-control"  name="phone_number" value="{{$user->phone_number}}">
                            </div>

                            <!-- <div class="form-group">
                                <label for="exampleInputEmail1">city</label>
                                <input type="text" class="form-control" name="city" value="{{$user->city}}">
                            </div>

                            <div class="form-group">
                                <label for="exampleInputEmail1">state</label>
                                <input type="text" class="form-control"  name="state" value="{{$user->state}}">
                            </div>

                            <div class="form-group">
                                <label for="exampleInputEmail1">country</label>
                                <input type="text" class="form-control"  name="country"value="{{$user->country}}">
                            </div> -->

                            <!-- <div class="form-group">
                                <label for="exampleInputEmail1">zip code</label>
                                <input type="text" class="form-control"  name="zip_code" value="{{$user->zip_code}}">
                            </div> -->

                            <div class="form-group" id="items">

                            </div>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </form>
                    </div>
               
                    </div>
                </div>
            </div>
        </div>
</section>

@endsection