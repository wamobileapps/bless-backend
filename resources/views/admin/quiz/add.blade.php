@extends('admin.master')

@section('content')

<section class="content-wrapper">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6">
            <h2 class="m-0">Add Quiz Question</h2>

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
                <form action="{{route('quiz.store')}}" method="post">
                  <div class="form-group">
                    @csrf()
                    <label for="exampleInputEmail1">Question</label>
                    <input type="text" class="form-control" name="question" placeholder="Enter Name Of Tournament">
                   
                  </div>
                    <div class="form-group">
                    <label for="exampleInputEmail1">Option A</label>
                    <input type="text" class="form-control" name="option[A]" placeholder="Enter Name Of Tournament">
              </div>
                    <div class="form-group">
                    <label for="exampleInputEmail1">Option B</label>
                    <input type="text" class="form-control" name="option[B]" placeholder="">
              </div>
                    <div class="form-group">
                    <label for="exampleInputEmail1">Option C</label>
                    <input type="text" class="form-control" name="option[C]" placeholder="">
              </div>
                    <div class="form-group">
                    <label for="exampleInputEmail1">Option D</label>
                    <input type="text" class="form-control" name="option[D]" placeholder="">
              </div>

                     <div class="form-group" id="items">

                     </div>
                    <button id="add" onclick="appenddiv()" class="btn add-more button-yellow uppercase" type="button">+ Add Option</button> <button class="delete btn button-white uppercase" type="button">Remove Option</button>

                    <div class="form-group">
                        <label for="exampleInputEmail1">Answer</label>
                   <select class="form-control" name ="answer" id="option">
                       <option value ="">Select Answer</option>
                       <option value="A">A</option>
                       <option value="B">B</option>
                       <option value="C">C</option>
                       <option value="D">D</option>
                   </select>

                    </div>

                    <button type="submit" class="btn btn-primary">Create</button>
                </form>
              </div>
             <div class="col-md-12">
           <div class="navigation-nre">

           </div>
            </div>
          </div>
        </div>
      </div>   
    </section>

<script>

    $(document).ready(function() {
        var arr = $.map(Array(22), function(value, index) {
            return String.fromCharCode(69 + index);
        });
        var newarr=[]
        var  firstElement;
        $(".delete").hide();
        //when the Add Field button is clicked
        $("#add").click(function(e) {
            $(".delete").fadeIn("1500");
            //Append a new row of code to the "#items" div


            appenddiv();
            function appenddiv(){
                console.log(arr)
                while(arr[0]==undefined){
                    arr.shift()
                }
              firstElement = arr.shift();
newarr.push(firstElement)
                let nameindex=`option[${firstElement}]`;
            $("#items").append(

                '<div class="row next-referral"><label for="exampleInputEmail1">Option '+firstElement+' </label><input id="textinput" name='+ nameindex +' type="text" placeholder="" class="form-control input-md"> </div>'
            );

                $("#option").append(
                    '<option class="optionremove" value='+firstElement+'>'+firstElement+'</option>'
                )
        }
        });

        $("body").on("click", ".delete", function(e) {

            let latestEle=newarr.pop()

            firstElement=latestEle

            arr.unshift( firstElement);

            $(".next-referral").last().remove();
            $(".optionremove").last().remove();

        });
    });

</script>
@endsection