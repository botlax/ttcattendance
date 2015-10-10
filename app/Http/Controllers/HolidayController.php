<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Holiday;

class HolidayController extends Controller
{
    
    $this->middleware('auth');
    $this->middleware('role',['except' => ['showSearchID','searchID','addAttendance','storeAttendance','updateAttendance','editAttendance','lockAttendance']]);

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $holidays = Holiday::all();
        return view('pages.index_holiday',compact('holidays'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
         return view('pages.add_holiday');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'holidate' => 'unique:holiday'
        ]);
        Holiday::create($request->all());
        flash('Successfully added.');
        return redirect('holiday');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($date)
    {
        $holiday = Holiday::where('holidate',$date)->first();
        return view('pages.edit_holiday',compact('holiday'));
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
        Holiday::find($id)->update($request->all());
        flash()->success('Successfully Updated');
        return redirect('holiday');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($date)
    {   
        $holiday = Holiday::where('holidate',$date)->first();
        $holiday->delete();
        flash('Successfully deleted');
        return redirect('holiday');
    }
}
