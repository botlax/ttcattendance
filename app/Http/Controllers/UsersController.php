<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\UserRequest;
use App\Http\Controllers\Controller;
use App\User;
use App\Role;
use App\Trade;
use App\Site;
use App\Labor;
use App\Attendance;

class UsersController extends Controller
{   

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role');
    }
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $users = User::all();
        return view('pages.users',compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $user = User::where('employee_no','=',$id)->first();
        return view('pages.show_users',compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $roles = Role::all()->lists('role','id');
        $user = User::where('employee_no','=',$id)->first();
        return view('pages.edit_user',compact('user','roles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(UserRequest $request, $id)
    {
        //dd($request->input('password') == '');
        $user = User::find($id);
        $user->role_id = $request->input('role_id');
        $user->name = $request->input('name');
        $user->employee_no = $request->input('employee_no');

        if($request->input('password') != ''){
            $user->password = bcrypt($request->input('password'));
        }
        
        $user->save();
        return redirect('users');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $user = User::destroy($id);
        return redirect('users');
    }
}
