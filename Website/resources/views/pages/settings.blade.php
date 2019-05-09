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
        <li><a href="/employee">Employee<span class="glyphicon glyphicon-list-alt pull-right"></span></a></li>
        <li class="active"><a href="/settings">Settings<span class="glyphicon glyphicon-cog pull-right"></span></a></li>
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
        
  <span><h2>Settings</h2><br>
    <form action="{{{ url("settings/settoken") }}}" method="GET">
    <span class="pull-left"><label>Token:</label>
    <input type="text" name="token" id="myInput" style="width: 300px; padding-left: 5px;"></span>
    <button  class="btn btn-primary pull-right" data-toggle="modal" data-target="#addemployee" style="margin-right: 5px;">Set Token</button>
        </span>
    </form>
  <hr>           
  <table class="table table-striped table-responsive">
	  
    </div>
    <hr>
    
@else

<div class="jumbotron text-center">
	<h2>This page is confidential</h2>
	<p><a href="/login">Click here to login</a></p>

</div>

@endif


    


  </div>
</div>

@endsection
