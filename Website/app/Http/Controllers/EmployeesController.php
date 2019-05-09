<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Employee;
use App\Location;
use Session;

class EmployeesController extends Controller
{


    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'home']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $emp = Employee::orderBy('created_at','desc');
        return view('pages.employee')->with('emp', $emp);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)


    {
        $this->validate($request, [
            'fname' => 'required',
            'lname' => 'required',
            'email' => 'required',
            'contactnumber' => 'required',
            'address' => 'required'

        ]);


        $employee = new Employee;
        $employee->fname = $request->input('fname');
        $employee->lname = $request->input('lname');
        $employee->email = $request->input('email');
        $employee->contactnumber = $request->input('contactnumber');
        $employee->address = $request->input('address');
        $employee->employer_id = \Auth::user()->id;
        $employee->save();

        $location = new Location;
        $location->accuracy = 0;
        $location->latitude = 0;
        $location->longitude = 0;
        $location->employee_id = $employee->id;
        $location->save();

        return redirect('/employee')->with('success', 'Employee added');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = DB::select('select * from employees where id = ?',[$id]);
        return view('employee',['user'=>$user]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $emp = Employee::find($id); 
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)

    
    {

        $this->validate($request, [
            'fname' => 'required',
            'lname' => 'required',
            'email' => 'required',
            'contactnumber' => 'required',
            'address' => 'required'

        ]);
        $emp = Employee::find($id);
        $emp->fname =$request->input('fname');
        $emp->lname =$request->input('lname');
        $emp->email =$request->input('email');
        $emp->address =$request->input('address');
        $emp->contactnumber =$request->input('contactnumber');
        $emp->save();

        return redirect('/employee')->with('success','Employee Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = Employee::find($id);
         
        $data->delete();
            return redirect('/employee')->with('success','Employee Removed');
    }
}
