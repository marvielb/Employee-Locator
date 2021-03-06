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
                            <li class="active"><a href="#section1">Home<span class="glyphicon glyphicon-home pull-right"></span></a></li>
                            <li><a href="/employee">Employee<span class="glyphicon glyphicon-user pull-right"></span></a></li>
                            <li><a href="#section3">Settings<span class="glyphicon glyphicon-cog pull-right"></span></a></li>
                            <li><a href="#section3">About<span class="glyphicon glyphicon-tag pull-right"></span></a></li>
                          </ul><br>
                              <hr>
                                  
                           </div>
                    
                        <div class="col-sm-9">
                            <span class="glyphicon glyphicon-user"><span>@if(!Auth::guest()) {{ Auth::user()->name }} @else ADMIN_NAME @endif</span></span><p class="pull-right" id="demo" style="color: green"></p>
                            <script>
                            var d = new Date();
                            document.getElementById("demo").innerHTML = d;
                            </script>
                    
                            <div class="jumbotron text-center">
                                <h1>WELCOME TO EMPCATOR</h1>
                                <p> Track your employee using our GPS technology </p>
                                <p><a class="btn btn-primary btn-lg" href="/login" role="button">Track now</a></a></p>
                            </div>
                    
                    
                    <!-- 
                          <h4><small>RECENT POSTS</small></h4>
                          <hr>
                          <h2>I Love Food</h2>
                          <h5><span class="glyphicon glyphicon-time"></span> Post by Jane Dane, Sep 27, 2015.</h5>
                          <h5><span class="label label-danger">Food</span> <span class="label label-primary">Ipsum</span></h5><br>
                          <p>Food is my passion. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
                          <br><br>
                          
                          <h4><small>RECENT POSTS</small></h4>
                          <hr>
                          <h2>Officially Blogging</h2>
                          <h5><span class="glyphicon glyphicon-time"></span> Post by John Doe, Sep 24, 2015.</h5>
                          <h5><span class="label label-success">Lorem</span></h5><br>
                          <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
                          <hr>
                    
                          <h4>Leave a Comment:</h4>
                          <form role="form">
                            <div class="form-group">
                              <textarea class="form-control" rows="3" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-success">Submit</button>
                          </form>
                          <br><br>
                          
                          <p><span class="badge">2</span> Comments:</p><br>
                          
                          <div class="row">
                            <div class="col-sm-2 text-center">
                              <img src="bandmember.jpg" class="img-circle" height="65" width="65" alt="Avatar">
                            </div>
                            <div class="col-sm-10">
                              <h4>Anja <small>Sep 29, 2015, 9:12 PM</small></h4>
                              <p>Keep up the GREAT work! I am cheering for you!! Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
                              <br>
                            </div>
                            <div class="col-sm-2 text-center">
                              <img src="bird.jpg" class="img-circle" height="65" width="65" alt="Avatar">
                            </div>
                            <div class="col-sm-10">
                              <h4>John Row <small>Sep 25, 2015, 8:25 PM</small></h4>
                              <p>I am so happy for you man! Finally. I am looking forward to read about your trendy life. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
                              <br>
                              <p><span class="badge">1</span> Comment:</p><br>
                              <div class="row">
                                <div class="col-sm-2 text-center">
                                  <img src="bird.jpg" class="img-circle" height="65" width="65" alt="Avatar">
                                </div>
                                <div class="col-xs-10">
                                  <h4>Nested Bro <small>Sep 25, 2015, 8:28 PM</small></h4>
                                  <p>Me too! WOW!</p>
                                  <br>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div> -->
                      </div>
                </div>
    
@endsection
