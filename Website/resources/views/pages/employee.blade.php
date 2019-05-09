@extends('layouts.app')

@section('content')

    <div class="container">
  <div class="row content">
    <div class="col-sm-3 sidenav">
      <h3 class="text-center">Empcator<span class="glyphicon glyphicon-send"></h3>
       <div class="input-group">
        <input type="text" class="form-control" placeholder="Search Employee..">
        <span class="input-group-btn">
          <button class="btn btn-default" type="button">
            <span class="glyphicon glyphicon-search"></span>
          </button>
        </span>
      </div>
      <br>
      
      <ul class="nav nav-pills nav-stacked">
        <li><a href="/">Home<span class="glyphicon glyphicon-home pull-right"></span></a></li>
        <li  class="active"><a href="/employee">Employee<span class="glyphicon glyphicon-list-alt pull-right"></span></a></li>
        <li><a href="/settings">Settings<span class="glyphicon glyphicon-cog pull-right"></span></a></li>
        <li><a href="#section3">About<span class="glyphicon glyphicon-tag pull-right"></span></a></li>
      </ul><br>
      	<hr>
      		
       </div>

    <div class="col-sm-9">
    	<span class="glyphicon glyphicon-user"></span><span style="font-size: 20px;">@if(!Auth::guest()) {{ Auth::user()->name }} @else ADMIN_NAME @endif</span></span><p class="pull-right" id="demo" style="color: green"></p>
    	<script>
		var d = new Date();
		document.getElementById("demo").innerHTML = d;
		</script>
		@if(!Auth::guest())
		
	<div>
  <span><h2>EMPLOYEE LIST</h2><br><span class="pull-left"><label>Search:</label><input type="text" name="" id="myInput" style="width: 300px; padding-left: 5px;"></span><button  class="btn btn-primary pull-right" data-toggle="modal" data-target="#addemployee" style="margin-right: 5px;"><span class="glyphicon glyphicon-plus"></span>Add employee</button>
        	<div class="modal fade" id="addemployee" role="dialog">
    			<div class="modal-dialog">
    
      <!-- Modal content-->
      			<div class="modal-content">
        		<div class="modal-header">
          		<button type="button" class="close" data-dismiss="modal">&times;</button>
          		<h4 class="modal-title">Add employee</h4>
       			</div>
        	    <div class="modal-body">
         		 
         		 <div class="form-group">
					{!! Form::open(['action' => 'EmployeesController@store', 'method' => 'POST']) !!}
					<div class="form-group">
						{{Form::label('fname', 'First name')}}
						{{Form::text('fname', '', ['class' => 'form-control', 'placeholder' => 'firstname', 'required'])}}
					</div>
					<div class="form-group">
						{{Form::label('lname', 'Last name')}}
						{{Form::text('lname', '', ['class' => 'form-control', 'placeholder' => 'lastname', 'required'])}}
					</div><div class="form-group">
						{{Form::label('email', 'Email')}}
						{{Form::email('email', '', ['class' => 'form-control', 'placeholder' => 'email', 'required'])}}
					</div><div class="form-group">
						{{Form::label('contactnumber', 'Contact number')}}
						{{Form::text('contactnumber', '', ['class' => 'form-control', 'placeholder' => 'contact number', 'required'])}}
					</div>
					<div class="form-group">
						{{Form::label('address', 'Address')}}
						{{Form::text('address', '', ['class' => 'form-control', 'placeholder' => 'address', 'required'])}}
					</div>
					{{Form::submit('Submit', ['class' => 'btn btn-primary pull-right'])}}
				{!! Form::close() !!}
         		</div>
        		</div>
        		<div class="modal-footer">
          	
        		</div>
      			</div>
      
    			</div>
  			</div>
		</span>
  <hr>           
  <table class="table table-striped table-responsive">
	  
    <thead>
      <tr>
      	<th>Emp ID</th>
        <th>Firstname</th>
        <th>Lastname</th>
        <th>Email</th>
        <th>Address</th>
        <th>Contact#</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody id="myTable">
			
			
			<?php  $c = 0; ?>
				@foreach($user as $data)
					<tr>    
					  <th>{{$data->id}}</th>
					  <th>{{$data->fname}}</th>
					  <th>{{$data->lname}}</th>
					  <th>{{$data->email}}</th>
					  <th>{{$data->address}}</th>
					  <th>{{$data->contactnumber}}</th>   
					  <th>
							<!-- orig -->
							<a href="/gmap/{{$data->id}}" class="btn btn-primary pull-left" style="margin-left:10px; width:130px;">Show Location</a>  
							<a href="/location/track/{{$data->id}}" class="btn btn-default pull-left" style="margin-left:10px; width:130px;">Track</a>

							
							
							{{-- <button data-toggle="modal" data-target="#myModal" style="margin-right: 5px; border: none; background:none;"><span class="glyphicon glyphicon-trash" style="color: red"></span></button>
						<div class="modal fade" id="myModal" role="dialog">
							<div class="modal-dialog">
				
				  <!-- Modal content-->
							  <div class="modal-content">
							<div class="modal-header">
							  <button type="button" class="close" data-dismiss="modal">&times;</button>
							  <h4 class="modal-title">For security purposes</h4>
							   </div>
							<div class="modal-body">
							  
							  <div class="text-center">
							  <label for="passwordconfirm">Enter your password : </label><input type="text" name="passwordconfirm">
							 </div>
							</div>
							<div class="modal-footer">
							  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
							</div>
							  </div>
				  
							</div>
						  </div>
			   --}}
						<button data-toggle="modal" data-target="#myModal{{$c}}" class="btn btn-success pull-left" style="margin-left:10px; margin-right:10px; width:130px;">Edit</a></button>
						<div class="modal fade" id="myModal{{$c}}" role="dialog">
							<div class="modal-dialog">
				
				  <!-- Modal content-->
							  <div class="modal-content">
							<div class="modal-header">
							  <button type="button" class="close" data-dismiss="modal">&times;</button>
							  <h4 class="modal-title">Edit employee</h4>
							   </div>
							<div class="modal-body">
							  
							  <div class="form-group">
								 
									   
									{!! Form::open(['action' => ['EmployeesController@update', $data->id], 'method' => 'POST']) !!}
									<input type = "hidden" name = "_token" value = "<?php echo csrf_token(); ?>">
									<div class="form-group">
										{{Form::label('fname', 'First name')}}
										{{Form::text('fname', $data->fname, ['class' => 'form-control', 'placeholder' => 'firstname','required'])}}
									</div>
									<div class="form-group">
											{{Form::label('lname', 'Last name')}}
											{{Form::text('lname',$data->lname, ['class' => 'form-control', 'placeholder' => 'lastname','required'])}}
										</div>
										<div class="form-group">
												{{Form::label('email', 'Email')}}
												{{Form::text('email', $data->email, ['class' => 'form-control', 'placeholder' => 'email','required'])}}
											</div>
											<div class="form-group">
													{{Form::label('address', 'Address')}}
													{{Form::text('address', $data->address, ['class' => 'form-control', 'placeholder' => 'address','required'])}}
												</div>
												<div class="form-group">
														{{Form::label('contactnumber', 'Contact Number')}}
														{{Form::text('contactnumber', $data->contactnumber, ['class' => 'form-control', 'placeholder' => 'contactnumber','required'])}}
									{{Form::hidden('_method','PUT')}}
									{{Form::submit('Submit', ['class' => 'btn btn-primary'])}}
								{!! Form::close() !!}
							
							 </div>
							</div>
							<div class="modal-footer">
						  
							</div>
							  </div>
				  
							</div>
						  </div></div>
						  
						  {!!Form::open(['action' => ['EmployeesController@destroy', $data->id], 'method' => 'POST', 'class' => 'pull-left'])!!}
							{{Form::hidden('_method', 'DELETE')}}
							{{Form::submit('Delete', ['class' => 'btn btn-danger deletebtn'])}}
				
						  {!!Form::close()!!}
						
					</th>              
					</tr>
					<?php $c++; ?>
				@endforeach
			
				
	  
    	</tbody>
 	 </table>
	</div>
    <hr>
    <script>
$(document).ready(function(){
  $("#myInput").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#myTable tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
});
</script>
@else

<div class="jumbotron text-center">
	<h2>This page is confidential</h2>
	<p><a href="/login">Click here to login</a></p>

</div>

@endif


    


  </div>
</div>

@endsection
