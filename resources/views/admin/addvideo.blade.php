@extends('admin.master')

@section('content')

<section class="content-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
                <h2 class="m-0">Add video</h2>

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

                  

                        <form action="{{route('user.adminaddvideo')}}" method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                @csrf


                                <label for="exampleInputEmail1">Category </label>
                                <select class="form-control" name="category_id">
                                    <option selected disabled>select category</option>
                                    @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->category }}</option>

                                    @endforeach
                                </select>

                                <br>
                                <label for="exampleInputEmail1">Video upload</label>
                                <input type="file" id="fileToUpload" name="video" value="">
                            </div>

                    </div>
                    <button type="submit" class="btn btn-primary subme">submit</button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</section>

@endsection