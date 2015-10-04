<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\TradeRequest;
use App\Http\Controllers\Controller;
use App\User;
use App\Trade;
use App\Site;
use App\Labor;
use App\Attendance;

class TradesController extends Controller
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
        $trades = Trade::all();
        return view('pages.index_trade',compact('trades'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $trade = Trade::where('name','=',$id)->first();
        if($trade == null) return redirect('trades');
        return view('pages.edit_trade',compact('trade'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(TradeRequest $request, $id)
    {
        $trade = Trade::where('name','=',$id)->first();
        if($trade == null) return redirect('trades');
        $trade->update($request->all());
        return redirect('trades');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function add()
    {
        return view('pages.add_trade');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(TradeRequest $request)
    {
        Trade::create($request->all());
        return redirect('trades');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $trade = Trade::where('name','=',$id)->first();
        if($trade == null) return redirect('trades');
        $trade->delete();
        return redirect('trades');
    }
}
