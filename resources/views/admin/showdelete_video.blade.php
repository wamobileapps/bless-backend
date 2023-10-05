@extends('admin.master')

@section('content')
<style type="text/css">
  .navigation-nre {
    margin-bottom: 35px;
    text-align: center;
  }

  .navigation-nre span svg {
    width: 2%
  }

  .navigation-nre nav div:first-child {
    display: none;
  }

  .block-and-rd {
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

  .toggle.android {
    border-radius: 0px;
  }

  .toggle.android .toggle-handle {
    border-radius: 0px;
  }

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

  input:checked+.slider {
    background-color: #2196F3;
  }

  input:focus+.slider {
    box-shadow: 0 0 1px #2196F3;
  }

  input:checked+.slider:before {
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


<style>
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

      </div><!-- /.col -->
      <div class="col-12">
        <div class="card">
      
            <div class="py-2 flex">
              <a href="{{route ('user.addvideo') }}"><button  type="button" class="btn btn-primary">Add Video</button></a>
              <div class="overflow-hidden flex pl-4">
              
              </div>
            </div>
          </form>
          <!-- /.card-header -->
          <div class="card-body">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Category</th>
                  <th>Video</th>
              
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>

                 @foreach($showvideodata as $key => $data)
                

                <tr>    
           
                   <td>{{$data->id}}</td> 
                   <td>{{$data->category_id}}</td> 
                  <td>
                  <video width="150" height="100" controls>
  <source src="{{ asset('storage/'.$data->video)}}" type="video/mp4">
  <source src="{{ asset('storage/'.$data->video)}}" type="video/x-flv">
  <source src="{{ asset('storage/'.$data->video)}}" type="video/ogg">
  Your browser does not support the video tag.
</video>
                  
                  </td>

                 <td><a href="{{ route('user.show_video_list_delete', $data->id)}}"><button type="button" onclick="return confirm('Do you want delete this user Video.')" class="btn btn-danger">Delete </button></a></td>
              

                


                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          <div class="col-md-12">
            <div class="navigation-nre">
            
            </div>
          </div>
        </div>
      </div>
    </div>
</section>
<!-- <script>
  $(document).ready(function() {
    $('.changeStatus').click(function() {
      // alert('here');
      // checkBox = document.getElementById('.changeStatus');
      let status = (jQuery(this).is(":checked")) ? 1 : 0;
      // let userId= (jQuery(this).attr('data-id')) ?1:0; 

      var userId = $(this).attr('data-id'); // Set the appropriate status here
      $.ajax({
        type: 'POST',
        url: "{{ route('user.status') }}",
        data: {
          _token: '{{ csrf_token() }}',
          id: userId,
          status: status
        },
        success: function(response) {
          if (response.success) {
            alert('Status Changed');
            var updatedButtonStatus = response.buttonStatus;
          
          } else {
            // Handle any errors if necessary
          }
        },
        error: function(xhr, status, error) {
          // Handle any AJAX errors if necessary
        }
      });
    });
  });
</script> -->
@endsection