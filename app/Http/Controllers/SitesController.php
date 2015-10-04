<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\SiteRequest;
use App\Http\Controllers\Controller;
use App\User;
use App\Trade;
use App\Site;
use App\Labor;
use App\Attendance;

class SitesController extends Controller
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
        $sites = Site::where('id','!=',1)->get();
        return view('pages.index_site',compact('sites'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $site = Site::where('code','=',$id)->first();
        if($site == null) return redirect('sites');
        $users = User::all()->lists('name','id')->toArray();
        return view('pages.edit_site',compact('site','users'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(SiteRequest $request, $id)
    {
        $site = Site::where('code','=',$id)->first();
        if($site == null) return redirect('sites');
        $site->update($request->all());
        return redirect('sites');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function add()
    {
        $users = User::all()->lists('name','id')->toArray();
        return view('pages.add_site',compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(SiteRequest $request)
    {
        Site::create($request->all());
        return redirect('sites');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {   
        $site = Site::where('code','=',$id)->first();
         if($site == null) return redirect('sites');
        $site->delete();
        return redirect('sites');
    }
}
