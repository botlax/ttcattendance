<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Input;
use Image;
use App\Http\Requests;
use App\Http\Requests\LaborRequest;
use App\Http\Requests\LaborSearchRequest;
use App\Http\Requests\LaborEditRequest;
use App\Http\Controllers\Controller;
use App\User;
use App\Trade;
use App\Site;
use App\Labor;
use App\Attendance;

class LaborController extends Controller
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
        $labors = Labor::where('deleted','=','false')->orderBy('employee_no')->paginate(20);
        //dd($labor);
        return view('pages.index_labor',compact('labors'));
        
    }

    /**
     * Display deleted users
     *
     * @return Response
     */
    public function indexDeleted()
    {
        $labors = Labor::where('deleted','=','true')->orderBy('employee_no')->paginate(20);
        return view('pages.show_deleted_labor',compact('labors'));
    }

    public function searchDeleted(Request $request)
    {   
        $id = $request->input('id');
        $site_id = 0;
       if(Site::where('code', 'LIKE', '%'.$id.'%')->orWhere('name', 'LIKE', '%'.$id.'%')->first() != null){
            $site_id = Site::where('code', 'LIKE', '%'.$id.'%')->orWhere('name', 'LIKE', '%'.$id.'%')->first()->id;
       }
        $labors = Labor::where('employee_no','=',$id)->orWhere('site_id','=',$site_id)->where('deleted','=','false')->paginate(20);
        //dd($labors->toArray()['total']);
        return view('pages.show_deleted_labor',compact('labors'));
    }

    public function undeleteLabor(Request $request,$id)
    {   
        $labor = Labor::where('employee_no',$id)->first();
        $labor->deleted = 'false';
        $labor->save();
        flash('Successfully undeleted');
        return redirect('employees/deleted');
    }

     /**
     * Display search
     *
     * @return Response
     */
    public function search(Request $request)
    {   
        $id = $request->input('id');
        $site_id = 0;
       if(Site::where('code', 'LIKE', '%'.$id.'%')->orWhere('name', 'LIKE', '%'.$id.'%')->first() != null){
            $site_id = Site::where('code', 'LIKE', '%'.$id.'%')->orWhere('name', 'LIKE', '%'.$id.'%')->first()->id;
       }
        $labors = Labor::where('employee_no','=',$id)->orWhere('name','LIKE', '%'.$id.'%')->orWhere('site_id','=',$site_id)->where('deleted','=','false')->paginate(20);
        //dd($labors->toArray()['total']);
        return view('pages.index_labor',compact('labors'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function add()
    {
        $trades = Trade::all()->lists('name','id')->toArray();
        $sites = Site::all()->lists('code','id')->toArray();
        return view('pages.add_labor',compact('sites','trades'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(LaborEditRequest $request)
    {
        $file = Input::file('labor_photo');
        if(!is_null($file)){
            if ($file->isValid()) {
                $destinationPath = 'images';
                $extension = $file->getClientOriginalExtension(); 
                $fileName = $request->input('employee_no').'.'.$extension; 
                $file->move($destinationPath, $fileName);
    
                $img = Image::make('images/'.$fileName);
                // now you are able to resize the instance
                $img->resize(100, 100);
                // finally we save the image as a new file
                $img->save('images/'.$fileName);
            }
            else {
              flash('uploaded file is not valid');
              return redirect('employees/add');
            }
        }
        $labor = Labor::create($request->all());
        $labor->deleted = 'false';
        $labor->save();
        return redirect('employees');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $labor = Labor::where('employee_no','=',$id)->first();
        $trades = Trade::all()->lists('name','id')->toArray();
        $sites = Site::all()->lists('code','id')->toArray();
        //dd($trades);
        return view('pages.edit_labor',compact('labor','sites','trades'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(LaborEditRequest $request, $id)
    {
        if(!is_null(Input::file('labor_photo'))){
            $file = Input::file('labor_photo');
            if ($file->isValid()) {
                $destinationPath = 'images';
                $extension = $file->getClientOriginalExtension(); 
                $fileName = $request->input('employee_no').'.'.$extension; 
                $file->move($destinationPath, $fileName);

                $img = Image::make('images/'.$fileName);
                // now you are able to resize the instance
                $img->resize(70, 70);
                // finally we save the image as a new file
                $img->save('images/'.$fileName);
            }
            else {
                flash('uploaded file is not valid');
                return redirect('employees/'.$request->input('employee_no').'/edit');
            }
        }

        $labor = Labor::where('id','=',$id)->first();
        $labor->update($request->all());
        flash('Successfully updated');
        return redirect('employees');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $labor = Labor::where('id','=',$id)->first();
        $labor->site_id = 1;
        $labor->deleted = 'true';
        $labor->save();
        return redirect('employees');
    }
}
