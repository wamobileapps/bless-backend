@extends('admin.master')

@section('content')

<section class="content-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
                <h2 class="m-0">Add User</h2>

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
                        <form action="" method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                @csrf()
                                <label for="exampleInputEmail1">Name</label>
                                <input type="text" class="form-control" name="name" value="">

                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">Email</label>
                                <input type="text" class="form-control" name="email" value="">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">Image</label>

                                <input type="file" id="fileToUpload" name="image" value="">
                            </div>
                            <div class="form-group">
                                <img style="height:81px; width: 150px;" />
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">Username</label>
                                <input type="text" class="form-control" name="username" value="">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">Age</label>
                                <input type="text" class="form-control" name="age" value="">
                            </div>

                            <div class="form-group">
                                <label for="exampleInputEmail1">Password</label>
                                <input type="password" class="form-control" name="password" value="">
                            </div>

                            <div class="form-group">
                                <label for="exampleInputEmail1">Phone number</label>
                                <input type="text" class="form-control" name="phone_number" value="">
                            </div>

                            <div class="form-group">
                                <label for="exampleInputEmail1">city</label>
                                <input type="text" class="form-control" name="city" value="">
                            </div>

                            <div class="form-group">
                                <label for="exampleInputEmail1">state</label>
                                <input type="text" class="form-control" name="state" value="">
                            </div>

                            <div class="form-group">
                                <label for="exampleInputEmail1">country</label>
                                <input type="text" class="form-control" name="country" value="">
                            </div>

                            <div class="form-group">
                                <label for="exampleInputEmail1">zip code</label>
                                <input type="text" class="form-control" name="zip_code" value="">
                            </div>
                    </div>

                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>

        </div>
    </div>
    </div>
</section>

@endsection