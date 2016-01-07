<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Input;
use Image;
use App\Http\Requests;
use App\Http\Requests\LaborRequest;
use App\Http\Requests\AddLoanRequest;
use App\Http\Requests\LaborSearchRequest;
use App\Http\Requests\LaborEditRequest;
use App\Http\Controllers\Controller;
use App\User;
use App\Loan;
use App\LoanMonths;
use App\Trade;
use App\Site;
use App\Labor;
use App\Attendance;
use Carbon\Carbon;

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
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function fix()
    {
        $x = 1;

        $labors = Labor::orderBy('employee_no','ASC')->get();
        foreach ($labors as $labor) {
            $user = $labor;
            $user->employee_no = $x;
            $user->name = 'Employee '.strval($x);
            $user->save();
            $x++;
        }
        
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
        $site_id = 0;$trade_id=0;
       if(Site::where('code', 'LIKE', '%'.$id.'%')->orWhere('name', 'LIKE', '%'.$id.'%')->first() != null){
            $site_id = Site::where('code', 'LIKE', '%'.$id.'%')->orWhere('name', 'LIKE', '%'.$id.'%')->first()->id;
       }
       
       if(Trade::where('name', 'LIKE', '%'.$id.'%')->first() != null){
            $trade_id = Trade::where('name', 'LIKE', '%'.$id.'%')->first()->id;
       }
       
        $labors = Labor::where('employee_no','=',$id)->orWhere('name','LIKE', '%'.$id.'%')->orWhere('site_id','=',$site_id)->orWhere('trade_id','=',$trade_id)->where('deleted','=','false')->paginate(20);
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

    /**
     * View employees with loan
     *
     * @param  int  $id
     * @return Response
     */
    public function withLoan()
    {
        $labors = Labor::where('deleted','false');
        
        $labors = $labors->where(function($query){
            foreach(Loan::all() as $loan){
                $labor = $loan->labor()->first();
                if($labor != null){
                    $query->orWhere('employee_no',$labor->employee_no);
                }
            }
        });

        $labors = $labors->paginate(20);
        //dd($labor);
        return view('pages.index_labor',compact('labors'));
    }

    /**
     * View employees with loan
     *
     * @param  int  $id
     * @return Response
     */
    public function indexLoan($id)
    {   
        $labor_id = Labor::where('employee_no',$id)->first()->id;
        $loans = Loan::where('labor_id',$labor_id)->get();
        $loanDetails = [];
        foreach($loans as $loan){
            $monthCounter = 0;
            $totalMonth = count(Labor::find($labor_id)->LoanMonths()->where('loan_id',$loan->id)->get()->toArray());
            $thisMonth = intval(Carbon::today()->format('ym'));
            foreach(Labor::find($labor_id)->LoanMonths()->where('loan_id',$loan->id)->get() as $loanMonth){
                if(intval($loanMonth->deduction_date->format('ym')) < $thisMonth){
                    $monthCounter++;
                }
            }
            $loanDetails[$loan->id]['months_left'] = $totalMonth - $monthCounter;
            $loanDetails[$loan->id]['amount_left'] = floatval($loan->deduction * ($totalMonth - $monthCounter));
            $loanDetails[$loan->id]['deducted'] = $loan->deduction * $monthCounter;
            $loanDetails[$loan->id]['next_month'] = Carbon::today()->format('F, Y');
        }
        //dd($loans);
        //dd($loans->first()->starting_date);
        return view('pages.index_loan',compact('loans','id','loanDetails'));
    }

    /**
     * View employees with loan
     *
     * @param  int  $id
     * @return Response
     */
    public function updateLoan(AddLoanRequest $request,$id)
    {   

        $loan_id = $request->input('id');
        $loan = Loan::find($loan_id)->update($request->except('id'));
        $labor_id = Labor::where('employee_no',$id)->first()->id;

        $interval = intval($request->input('interval'));
        $noOfMonths = intval($request->input('months-to-pay'));
        $start_date = Carbon::parse($request->input('starting_date'));
        $loanId = $loan_id;

        Labor::where('employee_no',$id)->first()->LoanMonths()->where('loan_id',$loanId)->delete();

        for($noOfMonths, $start_date ; $noOfMonths > 0 ; $noOfMonths--, $start_date->addMonth($interval)){
            $loan = new LoanMonths;
            $loan->deduction_date = $start_date;
            $loan->loan_id = $loanId;
            $loan->save();
        }

        $loans = Loan::where('labor_id',$labor_id)->get();
        flash()->success('Loan successfully updated.');
        return view('pages.index_loan',compact('loans','id'));
    }

    /**
     * View employees with loan
     *
     * @param  int  $id
     * @return Response
     */
    public function deleteLoan($id,$loanId)
    {   
        Labor::where('employee_no',$id)->first()->LoanMonths()->where('loan_id',$loanId)->delete();
        Loan::find($loanId)->delete();
        flash()->success('Successfully deleted.');
        return redirect('employees/'.$id.'/edit');
    }

    /**
     * View employees with loan
     *
     * @param  int  $id
     * @return Response
     */
    public function addLoan($id)
    {   
        return view('pages.add_loan',compact('id'));
    }

    /**
     * View employees with loan
     *
     * @param  int  $id
     * @return Response
     */
    public function storeLoan(AddLoanRequest $request,$id)
    {   
        $interval = intval($request->input('interval'));
        $noOfMonths = intval($request->input('months-to-pay'));
        $labor = Labor::where('employee_no',$id)->first();
        $loanEntry = $labor->loan()->save(new Loan($request->all()));
        $loanEntry->deduction = intval($request->input('amount'))/$noOfMonths;
        $loanEntry->save();

        $loanId = $loanEntry->id;
        $start_date = Carbon::parse($request->input('starting_date'));

        //dd($noOfMonths);
        for($noOfMonths, $start_date ; $noOfMonths > 0 ; $noOfMonths--, $start_date->addMonth($interval)){
            $loan = new LoanMonths;
            $loan->deduction_date = $start_date;
            $loan->loan_id = $loanId;
            $loan->save();
        }

        flash()->success('Loan successfully added.');
        return redirect('employees/'.$id.'/edit');    
        
    }
}
