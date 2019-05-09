<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


/*
Route::get('/user/{id}/{name}', function ($id , $name){
    return 'This is my id ' .$id . ' and my name is ' . $name;
});

*/
Route::get('/', 'PagesController@index');
Route::get('/about', 'PagesController@about');
Route::get('/employee', 'PagesController@employee');
Route::get('/gmap/{id}', 'LocationController@ShowGoogleMap');
Route::get('/location/track/{id}', 'LocationController@TrackEmployee');
// 	

Route::resource('employees','EmployeesController'); 


// Route::get('/about', function (){
//     return view('pages.about');
// });


Auth::routes();

Route::get('employee', function () {

    $user = DB::select('select * from employees');
      return view('pages.employee',['user'=>$user]);
});

Route::get('home', 'HomeController@index');
Route::get('/settings', 'SettingsController@ShowSettings');
Route::get('/settings/settoken', 'SettingsController@SetToken');
