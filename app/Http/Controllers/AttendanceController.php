<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Requests\FilterRequest;
use App\Http\Requests\AttRequest;
use App\Http\Controllers\Controller;
use App\User;
use App\Holiday;
use App\Trade;
use App\Site;
use App\Labor;
use App\Attendance;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role',['except' => ['filterAjaxAttendance','lock','viewAjaxAllFilled','viewAjaxAllUnfilled','addAjaxAtt','editAjaxAtt','deleteAjaxAtt','searchAjaxUnfilledLabor','searchAjaxFilledLabor','showSites','showSearch','addAttendance','storeAttendance','updateAttendance','editAttendance','lockAttendance']]);
        $this->middleware('notAdmin',['only' => ['lock','viewAjaxAllFilled','viewAjaxAllUnfilled','addAjaxAtt','editAjaxAtt','deleteAjaxAtt','searchAjaxUnfilledLabor','searchAjaxFilledLabor','showSites','showSearch','addAttendance','storeAttendance','updateAttendance','editAttendance','lockAttendance']]);
    }

    /**
     * Display options for filtering attendance list.
     *
     * @return Response
     */
    public function showFilterOptions()
    {/*
        $att_id = 2;
        foreach(Labor::all() as $labor){
            Attendance::where('id',$att_id)->first()->labor()->attach($labor->id);
            $att = Attendance::where('id',$att_id)->first()->labor()->where('id',$labor->id)->first()->pivot;
            $att->attended = 1;
            $att->ot = 0;
            $att->bot = 0;
            $att->site = $labor->site->code;
            $att->locked = 'true';
            $att->save();
        }
        */
        $sites = Site::where('id','>',1)->get()->lists('code','id')->toArray();
        $trades = Trade::all()->lists('name','id')->toArray();
        array_unshift($trades, "");
        //dd($trades);
        return view('pages.filteroptions',compact('sites','trades'));
    }

    /**
     * Display attendance based on filter
     *
     * @return Response
     */
    public function filterAttendance(Request $request)
    {   
        //dd(Site::all()->lists('code','code')->toArray());
        $this->validate($request,[
            'employee_no' => 'numeric'
        ]);

        $viewDeleted = $request->get('view-deleted');
        $sites = Site::where('id','>',1)->get()->lists('code','id')->toArray();
        $months = ['1'=>'January','2'=>'February','3'=>'March','4'=>'April','5'=>'May','6'=>'June','7'=>'July','8'=>'August','9'=>'September','10'=>'October','11'=>'November','12'=>'December'];
        $years = [];
        for($y=2015;$y<=intval(date('Y'));$y++){
            $years[$y] = $y; 
        }
        $yearI = $request->get('year');
        $monthI = $request->get('month');

        $dEnd = $this->daysCount($monthI,$yearI);
        

        $dateFrom = Carbon::parse('1-'.$monthI.'-'.$yearI);
        $dateTo = Carbon::parse($dEnd.'-'.$monthI.'-'.$yearI)->addDay();
        //dd(Labor::where('employee_no',1201)->first()->attendance()->where('att_date','>',$dateFrom)->where('att_date','<',$dateTo)->first());

        $month = $dateFrom->format('M');
        $year = $dateFrom->format('Y');
        $employee_no = $request->get('employee_no');
        $site = $request->get('site_list');
        $showAbsent = is_null($request->get('view-absent'))?false:true;
        $labors = Labor::where('deleted','false')->orderBy('employee_no')->get();
        if($viewDeleted){
            $labors = Labor::orderBy('employee_no')->get();
        }

        if(!empty($employee_no) && empty($site)){
            $labors = Labor::where('employee_no',$employee_no)->where('deleted','false')->orderBy('employee_no')->get();
            if($viewDeleted){
                $labors = Labor::where('employee_no',$employee_no)->orderBy('employee_no')->get();
            }
        }
        elseif(empty($employee_no) && !empty($site)){
            $labors = Labor::where('deleted','false')->where('site_id',$site[0]);

            if($viewDeleted){
                $labors = Labor::where('site_id',$site[0]);
            }

            if(count($site) > 1){
                foreach($site as $s){
                        $labors = $labors->orWhere('site_id',$s);
                }
            }
            $labors = $labors->orderBy('employee_no')->get();
        }
        elseif(!empty($employee_no) && !empty($site)){
            $labors = Labor::where('deleted','false')->where('employee_no',$employee_no);
            if($viewDeleted){
                $labors = Labor::where('employee_no',$employee_no);
            }
            foreach($site as $s){
                $labors = $labors->orWhere('site_id',$s);
            }
            
            $labors = $labors->orderBy('employee_no')->get();
        }
        $labor_att = [];
        $total = [];
        
        foreach($labors as $labor){

            $att_count = 0;
            $ot_count = 0.00;
            $bot_count = 0.00;

            for($dateFrom;$dateFrom<$dateTo;$dateFrom->addDay()){

                $att_entry = $labor->attendance->where('string_date',$dateFrom->format('Ymd'))->first();

                if(!is_null($att_entry) ){
                    if($showAbsent && $att_entry->pivot->attended == '1'){
                        $labor_att[$labor->employee_no]['attended'][$dateFrom->format('Y-m-d')] = '—';
                    }
                    else{
                        $labor_att[$labor->employee_no]['attended'][$dateFrom->format('Y-m-d')] = $att_entry->pivot->attended;
                        $att_count += intval($labor_att[$labor->employee_no]['attended'][$dateFrom->format('Y-m-d')]);
                    }
                }
                else{
                    $labor_att[$labor->employee_no]['attended'][$dateFrom->format('Y-m-d')] = '—';
                }   

                if(!is_null($att_entry) && $att_entry->pivot->attended == '1'){
                    if($showAbsent){
                        $labor_att[$labor->employee_no]['ot'][$dateFrom->format('Y-m-d')] = '—';
                        $labor_att[$labor->employee_no]['bot'][$dateFrom->format('Y-m-d')] = '—';
                        $labor_att[$labor->employee_no]['site'][$dateFrom->format('Y-m-d')] = '—';
                    }
                    else{
                        $labor_att[$labor->employee_no]['ot'][$dateFrom->format('Y-m-d')] = $att_entry->pivot->ot;
                        $labor_att[$labor->employee_no]['bot'][$dateFrom->format('Y-m-d')] = round($att_entry->pivot->bot,2);
                        $labor_att[$labor->employee_no]['site'][$dateFrom->format('Y-m-d')] = $att_entry->pivot->site;

                        $ot_count += floatval($att_entry->pivot->ot);
                        $bot_count += intval($att_entry->pivot->bot);
                    }
                }
                elseif(is_null($att_entry) || $att_entry->pivot->attended == '0'){
                    $labor_att[$labor->employee_no]['attended'][$dateFrom->format('Y-m-d')] = '—';
                    $labor_att[$labor->employee_no]['ot'][$dateFrom->format('Y-m-d')] = '—';
                    $labor_att[$labor->employee_no]['bot'][$dateFrom->format('Y-m-d')] = '—';
                    $labor_att[$labor->employee_no]['site'][$dateFrom->format('Y-m-d')] = '—';
                }
               
                $total[$labor->employee_no]['attended'] = round($att_count,2);
                $total[$labor->employee_no]['ot'] = $ot_count;
                $total[$labor->employee_no]['bot'] =  $bot_count;
                
                //------total salary computation
                $basic_salary = intval($labor->basic_salary);
                $allowance = intval($labor->allowance);
                $gross = intval($labor->basic_salary) + intval($labor->allowance);
                $total_days = intval($dEnd);
                
                $salary[$labor->employee_no]['attended'] = round(($gross / $total_days) * $att_count,2);
                $salary[$labor->employee_no]['ot'] = round(((($basic_salary / $total_days) / 8)*1.25) * $ot_count,2);
                $salary[$labor->employee_no]['bot'] = round(((($basic_salary / $total_days) / 8)*1.25) * $bot_count,2);
                
                $salary[$labor->employee_no]['total'] = $salary[$labor->employee_no]['attended'] + $salary[$labor->employee_no]['ot'] + $salary[$labor->employee_no]['bot'];
            }
            $dateFrom = Carbon::parse('1-'.$month.'-'.$year);
        }

        if($request->input('makesheet')){
            \Excel::create('Attendance', function($excel) use($month,$year,$labors,$labor_att,$total,$salary){
                $excel->setTitle('Attendance');
                $excel->setCreator('www.ttc-attendance.tk')
                      ->setCompany('Talal Trading & Contracting Co.');

                $excel->sheet('Sheetname', function($sheet) use($month,$year,$labors,$labor_att,$total,$salary){

                    //data
                    $heading = ['ID','Name','Trade','Date'];
                    for($x=1;$x <= $this->daysCount($month,$year);$x++){
                        $heading[] = $x;
                    }
                    $heading[] = 'Total';
                    $heading[] = 'Salary';

                    //initial setup
                    $sheet->setOrientation('landscape');
                    $sheet->setPageMargin(0.25);
                    //$sheet->protect('1121');

                    //sheet manipulation
                    $mergeRow = 5;
                    $row = 4;
                    $sheet->row($row, $heading);
                    $row++;
                    foreach($labors as $labor){
                        $data = [$labor->employee_no,$labor->name,$labor->trade->name];
                        foreach($labor_att[$labor->employee_no]['attended'] as $key => $attended){
                            if($attended == '—'){
                                $data[] = '';
                            }
                            else{
                                $data[] = $attended;
                            }
                        }
                        $data[] = $total[$labor->employee_no]['attended'];
                        $data[] = $salary[$labor->employee_no]['attended'];
                        //$data[3] = 'Attended';
                        array_splice($data, 3,0,'Attended');
                        $sheet->row($row, $data);
                        $row++;$data = ['','','','Overtime (OT)'];

                        foreach($labor_att[$labor->employee_no]['ot'] as $key => $ot){
                            if($labor_att[$labor->employee_no]['attended'][$key] == '1'){
                                $data[] = $ot;
                            }
                            else{
                                $data[] = '';
                            }
                        }
                        $data[] = $total[$labor->employee_no]['ot'];
                        $data[] = $salary[$labor->employee_no]['ot'];
                        //$data[3] = 'Attended';
                        $sheet->row($row, $data);
                        $row++;$data = ['','','','Bonus OT'];

                        foreach($labor_att[$labor->employee_no]['bot'] as $key => $bot){
                            if($labor_att[$labor->employee_no]['attended'][$key] == '1'){
                                $data[] = $bot;
                            }
                            else{
                                $data[] = '';
                            }
                        }
                        $data[] = $total[$labor->employee_no]['bot'];
                        $data[] = $salary[$labor->employee_no]['bot'];
                        //$data[3] = 'Attended';
                        $sheet->row($row, $data);
                        $row++;$data = ['','','','Site'];

                        foreach($labor_att[$labor->employee_no]['site'] as $key => $site){
                            if($labor_att[$labor->employee_no]['attended'][$key] == '1'){
                                $data[] = $site;
                            }
                            else{
                                $data[] = '';
                            }
                        }
                        $data[] = '';
                        $data[] = $salary[$labor->employee_no]['total'];
                        //$data[3] = 'Attended';
                        $sheet->row($row, $data);
                        $row++;

                        //merge employee details cells
                        $colA='A'.$mergeRow.':A'.($mergeRow+3); 
                        $colB='B'.$mergeRow.':B'.($mergeRow+3); 
                        $colC='C'.$mergeRow.':C'.($mergeRow+3);

                        $sheet->mergeCells($colA);
                        $sheet->mergeCells($colB);
                        $sheet->mergeCells($colC);
                        
                        $mergeRow += 4;
                    }


                    $sheet->setWidth(array(
                        'A' => 8, 'B' => 20, 'C' => 15, 'D' => 15, 'E' => 5, 'F' => 5, 'G' => 5,
                        'H' => 5, 'I' => 5, 'J' => 5, 'K' => 5, 'L' => 5, 'M' => 5, 'N' => 5,
                        'O' => 5, 'P' => 5, 'Q' => 5, 'R' => 5, 'S' => 5, 'T' => 5, 'U' => 5,
                        'V' => 5, 'W' => 5, 'X' => 5, 'Y' => 5, 'Z' => 5, 'AA' => 5, 'AB' => 5,
                        'AC' => 5, 'AD' => 5, 'AE' => 5, 'AF' => 5, 'AG' => 5, 'AH' => 5, 'AI' => 5,
                        'AJ' => 5, 'AK' => 5, 'AK' => 10,
                    ));
                    //cell styling
                    $sheet->cells('A4:AK4', function($cells) {

                        $cells->setFontWeight('bold');
                        //$cells->setBorder('medium', 'medium', 'medium', 'medium');
                        $cells->setAlignment('center');
                        $cells->setValignment('middle');
                        //$cells->setBackground('#DDDDDA');

                    });
                    $sheet->cells('A5:A2000', function($cells) {

                        $cells->setAlignment('center');
                        $cells->setValignment('top');
                    });
                    $sheet->cells('B5:B2000', function($cells) {

                        $cells->setAlignment('center');
                        $cells->setValignment('top');
                    });
                    $sheet->cells('C5:C2000', function($cells) {

                        $cells->setAlignment('center');
                        $cells->setValignment('top');
                    });

                });
            })->download('xlsx');
        }
        //dd($total);
        $request->flash();
        return view('pages.filteroptions',compact('salary','total','labors','sites','months','years','dateTo','dateFrom','month','year','labor_att'));
    }

    /**
     * Select date to edit
     *
     * @return Response
     */
    public function editEntry($date,$id,$field)
    {   

        $dateF = Carbon::parse($date);
        //dd($dateT);
        $labor = Labor::where('employee_no',$id)->first();
        $entry = $labor->attendance()->where('att_date',$dateF)->first()->pivot;
        if($field == 'site'){
            $sites = Site::all()->lists('code','code')->toArray();
        }
        $pulled = array_pull($sites,'');
        return view('pages.edit_attendance_admin',compact('labor','field','entry','dateF','sites'));
    }

    /**
     * update entry admin view
     *
     * @return Response
     */
    public function updateEntry(Request $request, $id)
    {   
        $this->validate($request, [
            'ot' => 'numeric',
            'bot' => 'numeric'
        ]);

        $dateF = Carbon::parse($request->input('date'));

        $entry = Labor::find($id)->attendance()->where('att_date',$dateF)->first();
        //dd($request->input('attended'));
        if($request->input('attended') != null){

            $entry->pivot->attended = $request->input('attended');
            if($request->input('attended') == '0'){
                $entry->pivot->ot = 0;
                $entry->pivot->bot = 0;
            }
        }
        elseif($request->input('ot') != null){
            if($request->input('ot') != ""){
                if($entry->holiday == 1){
                    $ot = intval($request->input('ot')) * 1.2;
                }
                else{
                    $ot = intval($request->input('ot'));
                }
            }
            else{
                $ot = 0;
            }
            $entry->pivot->ot = $ot;
        }
        elseif($request->input('bot') != null){
            $bot = $request->input('bot') == ""?0:$request->input('bot');
            $entry->pivot->bot = $bot;
        }
        elseif($request->input('site') != null){
            $entry->pivot->site = $request->input('site');
        }

        if((isset($ot) && $ot != 0) || (isset($bot) && $bot != 0)){
            $entry->pivot->attended = 1;
        }

        $entry->pivot->save();
        return redirect('attendance');
    }


//---------------------------------------ajax methods

    /**
     * Display attendance based on filter
     *
     * @return Response
     */
    public function filterAjaxAttendance()

    {   
        
        $filterComplete = 'false';
        $skip = intval(\Input::get('skip'));
        $take = intval(\Input::get('take'));
        $viewDeleted = \Input::get('view_deleted');
        $dateFrom = \Input::get('date_from');
        $dateTo = \Input::get('date_to');
        $employee_no = \Input::get('employee_no');
        $site = \Input::get('site_list');
        $trade = \Input::get('trade_list');
        $dateFromCarbon = Carbon::parse($dateFrom);
        $dateToCarbon = Carbon::parse($dateTo);
        $sites = Site::where('id','>',1)->get()->lists('code','id')->toArray();

        $monthFrom = $dateFromCarbon->format('n');
        $monthTo = $dateToCarbon->format('n');
        $yearFrom = $dateFromCarbon->format('Y');
        $yearTo = $dateToCarbon->format('Y');

        $total_days = 0;
        if($monthFrom != $monthTo && $yearFrom == $yearTo){
            $counter = 0;
            for($monthInt = intval($monthFrom);$monthInt <= intval($monthTo);$monthInt++){
                $counter += 1;
                $total_days += intval($this->daysCount(''.$monthInt,$yearFrom));
            }
            $total_days /= $counter;
        }
        elseif($monthFrom == $monthTo && $yearFrom == $yearTo){
            $total_days += intval($this->daysCount($monthFrom,$yearFrom));
        }

        $showAbsent = \Input::get('view_absent') == 1?true:false;
        $labors = Labor::where('deleted','false')->orderBy('employee_no')->skip($skip)->take($take)->get();

        //if deleted employees included
        if($viewDeleted){
            $labors = Labor::orderBy('employee_no')->skip($skip)->take($take)->get();
        }

        //if filter by employee number
        if(!empty($employee_no) && empty($site) && empty($trade)){
            $labors = Labor::where('employee_no',$employee_no)->where('deleted','false')->orderBy('employee_no')->skip($skip)->take($take)->get();
            if($viewDeleted){
                $labors = Labor::where('employee_no',$employee_no)->orderBy('employee_no')->skip($skip)->take($take)->get();
            }
        }

        //if filter by site
        elseif(empty($employee_no) && !empty($site) && empty($trade)){
            $labors = Labor::where('deleted','false')->where('site_id',$site[0]);

            if($viewDeleted){
                $labors = Labor::where('site_id',$site[0]);
            }

            if(count($site) > 1){
                foreach($site as $s){
                        $labors = $labors->orWhere('site_id',$s);
                }
            }
            $labors = $labors->orderBy('employee_no')->skip($skip)->take($take)->get();
        }

        //if filter by trade
        elseif(empty($employee_no) && empty($site) && !empty($trade)){
            $labors = Labor::where('deleted','false')->where('trade_id',$trade[0]);

            if($viewDeleted){
                $labors = Labor::where('trade_id',$trade[0]);
            }

            if(count($trade) > 1){
                foreach($trade as $t){
                        $labors = $labors->orWhere('trade_id',$t);
                }
            }
            $labors = $labors->orderBy('employee_no')->skip($skip)->take($take)->get();
        }

        //if filter by site and employee_no
        elseif(!empty($employee_no) && !empty($site) && empty($trade)){
            $labors = Labor::where('deleted','false')->where('employee_no',$employee_no);
            if($viewDeleted){
                $labors = Labor::where('employee_no',$employee_no);
            }

            $labors = $labors->where(function($query)use($site){
                foreach($site as $s){
                    $query->orWhere('site_id',$s);
                }
            });
            
            
            $labors = $labors->orderBy('employee_no')->skip($skip)->take($take)->get();
        }

        //if filter by trade and employee_no
        elseif(!empty($employee_no) && empty($site) && !empty($trade)){
            $labors = Labor::where('deleted','false')->where('employee_no',$employee_no);
            if($viewDeleted){
                $labors = Labor::where('employee_no',$employee_no);
            }
            $labors = $labors->where(function($query)use($trade){
                foreach($trade as $t){
                    $query->orWhere('trade_id',$t);
                }
            });
            
            $labors = $labors->orderBy('employee_no')->skip($skip)->take($take)->get();
        }

        //if filter by trade and site
        elseif(empty($employee_no) && !empty($site) && !empty($trade)){
            $labors = Labor::where('deleted','false');
            if($viewDeleted){
                $labors = Labor::all();
            }
            $labors = $labors->where(function($query)use($site){
                foreach($site as $s){
                    $query->orWhere('site_id',$s);
                }
            });
            $labors = $labors->where(function($query)use($trade){
                foreach($trade as $t){
                    $query->orWhere('trade_id',$t);
                }
            });
            
            $labors = $labors->orderBy('employee_no')->skip($skip)->take($take)->get();
        }

        //if filter by trade, site and employee_no
        elseif(!empty($employee_no) && !empty($site) && !empty($trade)){
            $labors = Labor::where('deleted','false')->where('employee_no',$employee_no);
            if($viewDeleted){
                $labors = Labor::where('employee_no',$employee_no);
            }
            $labors = $labors->where(function($query)use($site){
                foreach($site as $s){
                    $query->orWhere('site_id',$s);
                }
            });
            $labors = $labors->where(function($query)use($trade){
                foreach($trade as $t){
                    $query->orWhere('trade_id',$t);
                }
            });
            
            $labors = $labors->orderBy('employee_no')->skip($skip)->take($take)->get();
        }

        $filterComplete = empty($labors->toArray())?'true':'false';
        $labor_att = [];
        $total = [];
        $salary = [];
        $trades = [];
        foreach($labors as $labor){

            $att_count = 0;
            $ot_count = 0.00;
            $bot_count = 0.00;
            $trades[] = $labor->trade->name;
            for($dateFromCarbon;$dateFromCarbon<=$dateToCarbon;$dateFromCarbon->addDay()){

                $att_entry = $labor->attendance()->where('att_date',$dateFromCarbon->format('Y-m-d H:i:s'))->first();

                if(!is_null($att_entry) ){
                    if($showAbsent && $att_entry->pivot->attended == '1'){
                        $labor_att[$labor->employee_no]['attended'][$dateFromCarbon->format('Y-m-d')] = '—';
                    }
                    else{
                        $labor_att[$labor->employee_no]['attended'][$dateFromCarbon->format('Y-m-d')] = $att_entry->pivot->attended;
                        $att_count += intval($labor_att[$labor->employee_no]['attended'][$dateFromCarbon->format('Y-m-d')]);
                    }
                }
                else{
                    $labor_att[$labor->employee_no]['attended'][$dateFromCarbon->format('Y-m-d')] = '—';
                }

                if(!is_null($att_entry) && $att_entry->pivot->attended == '1'){
                    if($showAbsent){
                        $labor_att[$labor->employee_no]['ot'][$dateFromCarbon->format('Y-m-d')] = '—';
                        $labor_att[$labor->employee_no]['bot'][$dateFromCarbon->format('Y-m-d')] = '—';
                        $labor_att[$labor->employee_no]['site'][$dateFromCarbon->format('Y-m-d')] = '—';
                    }
                    else{
                        $labor_att[$labor->employee_no]['ot'][$dateFromCarbon->format('Y-m-d')] = $att_entry->pivot->ot;
                        $labor_att[$labor->employee_no]['bot'][$dateFromCarbon->format('Y-m-d')] = $att_entry->pivot->bot;
                        $labor_att[$labor->employee_no]['site'][$dateFromCarbon->format('Y-m-d')] = $att_entry->pivot->site;

                        $ot_count += floatval($att_entry->pivot->ot);
                        $bot_count += intval($att_entry->pivot->bot);
                    }
                }
                elseif(!is_null($att_entry) && $att_entry->pivot->attended == '0'){
                    if($showAbsent){
                        $labor_att[$labor->employee_no]['attended'][$dateFromCarbon->format('Y-m-d')] = $att_entry->pivot->attended;
                        $labor_att[$labor->employee_no]['ot'][$dateFromCarbon->format('Y-m-d')] = $att_entry->pivot->ot;
                        $labor_att[$labor->employee_no]['bot'][$dateFromCarbon->format('Y-m-d')] = $att_entry->pivot->bot;
                        $labor_att[$labor->employee_no]['site'][$dateFromCarbon->format('Y-m-d')] = $att_entry->pivot->site;
                    }
                    else{
                        $labor_att[$labor->employee_no]['attended'][$dateFromCarbon->format('Y-m-d')] = '—';
                        $labor_att[$labor->employee_no]['ot'][$dateFromCarbon->format('Y-m-d')] = '—';
                        $labor_att[$labor->employee_no]['bot'][$dateFromCarbon->format('Y-m-d')] = '—';
                        $labor_att[$labor->employee_no]['site'][$dateFromCarbon->format('Y-m-d')] = '—';
                    }
                }
                elseif(is_null($att_entry)){
                    $labor_att[$labor->employee_no]['attended'][$dateFromCarbon->format('Y-m-d')] = '—';
                    $labor_att[$labor->employee_no]['ot'][$dateFromCarbon->format('Y-m-d')] = '—';
                    $labor_att[$labor->employee_no]['bot'][$dateFromCarbon->format('Y-m-d')] = '—';
                    $labor_att[$labor->employee_no]['site'][$dateFromCarbon->format('Y-m-d')] = '—';
                }
               
                $total[$labor->employee_no]['attended'] = round($att_count,2);
                $total[$labor->employee_no]['ot'] = $ot_count;
                $total[$labor->employee_no]['bot'] =  $bot_count;
                
                //------total salary computation
                $basic_salary = intval($labor->basic_salary);
                $allowance = intval($labor->allowance);
                $gross = intval($labor->basic_salary) + intval($labor->allowance);
                
                $salary[$labor->employee_no]['attended'] = round(($gross / $total_days) * $att_count,2);
                $salary[$labor->employee_no]['ot'] = round(((($basic_salary / $total_days) / 8)*1.25) * $ot_count,2);
                $salary[$labor->employee_no]['bot'] = round(((($basic_salary / $total_days) / 8)*1.25) * $bot_count,2);
                
                $salary[$labor->employee_no]['total'] = $salary[$labor->employee_no]['attended'] + $salary[$labor->employee_no]['ot'] + $salary[$labor->employee_no]['bot'];
            }
            $dateFromCarbon = Carbon::parse($dateFrom);
        }

        $response = ['trade'=>$trades,'salary'=>$salary,'total'=>$total,'labor_att'=>$labor_att,'labor'=>$labors,'dateFrom'=>$dateFromCarbon->format('Y-m-d'),'dateTo'=>$dateToCarbon->format('Y-m-d'),'filterComplete'=>$filterComplete];
        echo json_encode($response);
    }



    public function getSelectOptions()
    {
        $field = \Input::get('field');
        if($field == 'site'){
            $response = Site::all()->lists('code','code')->toArray();
        }
        else{
            $response = [1=>"YES",0=>"NO"];
        }

        echo json_encode($response);
    }

    public function updateAjaxEntry()
    {   

        $field = \Input::get('field');
        $dateF = Carbon::parse(\Input::get('date'));
        $id = \Input::get('id');
        $input = \Input::get('entry');
        $entry = Labor::find($id)->attendance()->where('att_date',$dateF->format('Y-m-d H:i:s'))->first();
        $att_date = Attendance::where('att_date',$dateF->format('Y-m-d H:i:s'))->first();
        $result = 2;

        if(is_null($att_date))/*if date is not initialized*/{
            if($dateF > Carbon::today())/*if date is in the future*/{

                //return an error
                $response = ['result'=>5,'field'=>$field,'date'=>$dateF->format('Y-m-d'),'en'=>$id,'entry'=>$input];
                echo json_encode($response);
                die();
            }
            else/*date is in the past (VALID)*/{

                //initialize the date
                $date_init = new Attendance;
                $date_init->att_date = $dateF;
                $holiday = 0;
                if($dateF->format('l') == 'Friday' || Holiday::where('holidate',$dateF)->first() != null){
                    $holiday = 1;
                }
                $date_init->holiday = $holiday;
                $date_init->save();
            }
        }

        if(is_null($entry))/*if do not entry exists*/{

            //initialize entry
            $att_date = Attendance::where('att_date',$dateF->format('Y-m-d H:i:s'))->first();
            $att_date->labor()->attach($id);
            $att_entry = $att_date->labor()->find($id);
            $att_entry->pivot->attended = 0;
            $att_entry->pivot->ot = 0;
            $att_entry->pivot->bot = 0;
            $att_entry->pivot->site = '—';
            $att_entry->pivot->locked = 'true';
            $att_entry->pivot->save();
        }

        $entry = Labor::find($id)->attendance()->where('att_date',$dateF->format('Y-m-d H:i:s'))->first();

        if($field == 'attended'){

            $entry->pivot->attended = $input;
            if($input == '0'){
                $input = '—';
                $entry->pivot->ot = 0;
                $entry->pivot->bot = 0;
                $result = 0;
            }
        }
        elseif($field == 'ot'){
            if($input != ""){
                if($entry->holiday == 1){
                    $ot = intval($input) * 1.2;
                }
                else{
                    $ot = intval($input);
                }
            }
            else{
                $ot = 0;
            }
            $entry->pivot->ot = $ot;
        }
        elseif($field == 'bot'){
            $bot = $input == ""?0:$input;
            $entry->pivot->bot = $bot;
        }
        elseif($field == 'site'){
            if($input == ''){
                $entry->pivot->site = '—';
                $result = 3;
            }
            else{
                $entry->pivot->site = $input;
                $result = 6;
            }
        }

        if((isset($ot) && $ot != 0) || (isset($bot) && $bot != 0)){
            $entry->pivot->attended = 1;
            $result = 1;
        }
        $entry->pivot->save();
        
        $response = ['result'=>$result,'field'=>$field,'date'=>$dateF->format('Y-m-d'),'en'=>$id,'entry'=>$input];
        echo json_encode($response);
        die();
        
    }

    /**
     * Ajax labor search
     *
     * @return Response
     */
    public function searchAjaxUnfilledLabor()
    {   
        $employees = [];
        $input =  \Input::get('input');
        $labors = Labor::where('employee_no','LIKE',$input.'%')->orWhere('name','LIKE','%'.$input.'%')->orderBy('employee_no','ASC')->get();
        foreach($labors as $labor){
            if($labor->attendance()->where('id',$this->getDateId())->first() == null){
                $employees[$labor->employee_no] = $labor->name;
            }
        }
        echo json_encode($employees);
        die();
    }

     /**
     * Ajax labor search
     *
     * @return Response
     */
    public function searchAjaxFilledLabor()
    {   
        $employees = [];
        $input =  \Input::get('input');
        $input = 
        $labors = Labor::where('employee_no','LIKE',$input.'%')->orWhere('name','LIKE','%'.$input.'%')->orderBy('employee_no','ASC')->get();
        foreach($labors as $labor){
            $att = $labor->attendance()->where('id',$this->getDateId())->first();
            if($att != null && $att->pivot->site == session()->get('site')){
                $employees[$labor->employee_no] = ['name'=>$labor->name,'ot'=>intval($att->pivot->ot),'bot'=>intval($att->pivot->bot)];
            }
        }
        echo json_encode($employees);
        die();
    }

    /**
     * Ajax add attendance
     *
     * @return Response
     */
    public function addAjaxAtt()
    {   

        $employee_id = Labor::where('employee_no',\Input::get('id'))->first()->id;
        $site = \input::get('site');
        $ot = \input::get('ot') == '' ? 0 : \input::get('ot');
        $bot = \input::get('bot') == '' ? 0 : \input::get('bot');
        //echo $employee_id.'-'.$site.'-'.$ot.'-'.$bot.'-'.$att;
    
        Attendance::latest('att_date')->first()->labor()->attach($employee_id);
        $entry = Attendance::latest('att_date')->first()->labor()->find($employee_id)->pivot;
        $entry->bot = $bot;
        $entry->ot = $ot;
        $entry->site = $site;
        $entry->attended = 1;
        $entry->locked = 'false';
        $entry->save();
        echo \Input::get('id');
    }

    /**
     * Ajax add attendance
     *
     * @return Response
     */
    public function editAjaxAtt()
    {   

        $employee_id = Labor::where('employee_no',\Input::get('id'))->first()->id;
        $ot = \input::get('ot') == '' ? 0 : \input::get('ot');
        $bot = \input::get('bot') == '' ? 0 : \input::get('bot');
        //echo $employee_id.'-'.$site.'-'.$ot.'-'.$bot.'-'.$att;
    
        $entry = Attendance::latest('att_date')->first()->labor()->find($employee_id)->pivot;
        $entry->bot = $bot;
        $entry->ot = $ot;
        $entry->save();
    }

    /**
     * Ajax add attendance
     *
     * @return Response
     */
    public function deleteAjaxAtt()
    {   
        $employee_id = Labor::where('employee_no',\Input::get('id'))->first()->id;
        Attendance::latest('att_date')->first()->labor()->detach($employee_id);
    }

    /**
     * Ajax add attendance
     *
     * @return Response
     */
    public function viewAjaxAllUnfilled()
    {   
        $employees = [];
        foreach(Labor::orderBy('employee_no','ASC')->get() as $labor){
            if($labor->attendance()->where('id',$this->getDateId())->first() == null){
                $employees[$labor->employee_no] = $labor->name;
            }
        }
        echo json_encode($employees);
        die();
    }

    /**
     * Ajax add attendance
     *
     * @return Response
     */
    public function viewAjaxAllFilled()
    {   
        $employees = [];
        foreach(Labor::orderBy('employee_no','ASC')->get() as $labor){
            $att = $labor->attendance()->where('id',$this->getDateId())->first();
            if($att != null && $att->pivot->site == session()->get('site')){
                $employees[$labor->employee_no] = ['name'=>$labor->name,'ot'=>intval($att->pivot->ot),'bot'=>intval($att->pivot->bot)];
            }
        }
        echo json_encode($employees);
        die();
    }

    /**
     * Ajax add attendance
     *
     * @return Response
     */
    public function lock($site)
    {
        $num = 1;
        foreach(Attendance::latest('att_date')->first()->labor()->get() as $labor){
            $att = $labor;
            if($att->pivot->site == $site){
                $num++;
                $att->pivot->locked = 'true';
                $att->pivot->save();
            }
        }
        if($num == 1){
            flash()->error('Attendance is empty.');
            return redirect('attendance/list/'.$site);
        }
        else{
            flash()->success('Attendance successfully submitted!');
            return redirect('attendance/list');
        }
    }

    /**
     * Display list of labors under the signed in user
     *
     * @return Response
     */
    public function showSites()
    {   
        if(!$this->initialized()){
            $currentDate = new Attendance;
            $currentDate->att_date = Carbon::today();
            $holiday = 0;
            if(Carbon::today()->format('l') == 'Friday' || Holiday::where('holidate',Carbon::today())->first() != null){
                $holiday = 1;
            }
            $currentDate->holiday = $holiday;
            $currentDate->save();
        }

        //$labors = Labor::all()->lists('name','employee_no')->toArray();
        //dd($labors);

        $userID = \Auth::user()->id;
        $dateId = $this->getDateId();

        $user = \Auth::user();
        $sites = $user->site;
        return view('pages.attendance_list',compact('sites','dateId','userID'));
    }

    /**
     * Display list of labors under the signed in user
     *
     * @return Response
     */
    public function showSearch($site)
    {   
        
        if(!$this->siteIsLocked($site)){
            $labors = Site::where('code',$site)->first()->labor()->get();
            session()->put('site', $site);
            return view('pages.add_attendance',compact('labors','site'));
        }
        else{
            flash()->error('Attendance for this site has been already submitted.');
            return redirect('attendance/list');
        }
    }

    
    /*
    public function showSearch(Request $request)
    {   
        
        $siteID = $request->input('site');
        $this->validate($request,[
            'id'.$siteID => 'required|numeric'
        ]);
        
        $employeeID = $request->input('id'.$siteID);
        $labor = Labor::where('site_id',$siteID)->where('employee_no',$employeeID)->first();
                
        if($labor != null){
            if($labor->attendance->where('id',$this->getDateId())->first() == null){
                $holiday = $this->todayIsHoliday();
                return view('pages.add_attendance',compact('labor','holiday'));
            }
            else{
                flash("Employee ID ".$employeeID." attendance has been already filled up.");
                return redirect('attendance/list');
            }
        }
        else{
            flash('Sorry, the Employee ID you entered does not belong to this site.');
            return redirect('attendance/list');
        }

        
    }
*/


    /**
     * Add Attendance to labor
     *
     * @return Response
     */
    public function addAttendance($id)
    {   

        $labor = Labor::where('employee_no','=',$id)->first();
        $holiday = $this->todayIsHoliday();
        //dd($labor);

        return view('pages.add_attendance',compact('labor','holiday'));
    }

    /**
     * Store Attendance
     *
     * @return Response
     */
    public function storeAttendance(AttRequest $request, $id)
    {   
        Attendance::latest('att_date')->first()->labor()->attach($id);
        $site = Labor::find($id)->site->code;
        foreach(Labor::find($id)->attendance()->where('id','=',$this->getDateId())->get() as $attendance){
            $attendance->pivot->attended = $request->input('present');
            if($attendance->holiday == 1){
                $attendance->pivot->ot = intval($request->input('overtime')) * 1.2;
            }
            else{
                $attendance->pivot->ot = $request->input('overtime');
            }
            $attendance->pivot->bot = $request->input('bonus_ot');
            $attendance->pivot->site = $site;
            $attendance->pivot->locked = 'false';
            $attendance->pivot->save();
            //dd($attendance->pivot->attended);
        }

        return redirect('attendance/list');
    }

    /**
     * Submit and lock Attendance
     *
     * @return Response
     */
    public function lockAttendance($id)
    {   
        $user = User::find($id);
        foreach($user->labor as $labor){
            if($labor->attendance()->where('id',$this->getDateId())->first() != null){
                $labor->attendance()->where('id',$this->getDateId())->first()->pivot->update(['locked' => 'true']);
            }
            else{
                Attendance::latest('att_date')->first()->labor()->attach($labor->id);
                $att = $labor->attendance()->where('id',$this->getDateId())->first();
                $att->pivot->locked = 'true';
                
                $att->pivot->attended = $att->holiday == 1 ? 1 : 0;
    
                $att->pivot->ot = 0;
                $att->pivot->bot = 0;
                $att->pivot->site = '—';
                $att->pivot->save();
            }
        }

        $sites = [];$siteLink = '';
        foreach($user->site as $userSite){
            $sites[] = $userSite;
            $siteLink .= '&site_list%5B%5D='.$userSite->id;
        }
        $date = Attendance::latest('att_date')->first();
        $link = url('attendance/filter?employee_no=&month='.$date->att_date->format('m').'&year='.$date->att_date->format('Y').$siteLink);
        
        $data = ['date'=>$date->att_date->format('M d, Y'),
                'siteInCharge'=>$user->name,
                'sites'=>$sites,
                'link'=>$link];
        \Mail::send('email.notify',$data,function($mail){
            $mail->from('attendance@talalcontracting.com','TTC-Attendance');
            $mail->to('attendance@talalcontracting.com','Admin');
            $mail->subject('Site Attendance Notification');
        });

        flash("Attendance Submitted! Thank you!");
        return redirect('attendance/list');
    }

    /**
     * Display Edit form for attendance
     *
     * @return Response
     */
    public function editAttendance($id)
    {   
        //dd(\Auth::user()->labor()->where('employee_no','=',$id)->first());
        $filledUp = $this->isFilledUp($id);
        if(!$filledUp) 
        return redirect('attendance/list');

        $labor = \Auth::user()->labor()->where('employee_no','=',$id)->first();
        //dd($labor->attendance()->where('id','=',$this->getDateId())->first()->pivot->ot);
        return view('pages.edit_attendance',compact('labor'));
    }

    /**
     * Update attendance of the specified labor
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function updateAttendance(AttRequest $request, $id)
    {
        foreach(Labor::find($id)->attendance()->where('id','=',$this->getDateId())->get() as $attendance){
            $attendance->pivot->attended = $request->input('present');
            if($attendance->holiday == 1){
                $attendance->pivot->ot = intval($request->input('overtime')) * 1.2;
            }
            else{
                $attendance->pivot->ot = $request->input('overtime');
            }
            $attendance->pivot->bot = $request->input('bonus_ot');
            $attendance->pivot->save();
            //dd($attendance->pivot->attended);
        }
        
        return redirect('attendance/list');
    }

    

    /*
    *
    *
    *Functions
    *
    */
    public function initialized(){

        $dbToday = null;
        if(!empty(Attendance::all()->toArray())){
         $dbToday = Attendance::latest('att_date')->first()->att_date;
        }
        return $dbToday->isSameDay(Carbon::now());
    }

    public function hasEntry(){
        $user = \Auth::user();
        $have = false;
        foreach($user->site as $site){
            if(!empty(Attendance::latest('att_date')->first()->labor->where('site_id',$site->id)->toArray())){
                $have = true;
            }
        }
        return $have;
    }

    public function siteIsLocked($site){
        $labor = Attendance::latest('att_date')->first()->labor();
        if(!empty($labor->get()->toArray())){
            foreach($labor->get() as $att){
                if($att->pivot->site == $site){
                    return $att->pivot->locked == 'true'?true:false;
                    break;
                }
            }
        }
        else{
            return false;
        }
    }

    public function isFilledUp($id){
        if(is_null(\Auth::user()->labor()->where('employee_no','=',$id)->first()->attendance()->where('id','=',$this->getDateId())->first())){
            return false;
        }
        else{
            return true;
        }
    }

    public function getDateId(){
        return Attendance::latest('att_date')->first()->id;
    }

    public function isLeapYear($year){
        return ((($year % 4) == 0) && ((($year % 100) != 0) || (($year %400) == 0)));
    }

    public function todayIsHoliday(){
        if(Carbon::today()->format('l') == 'Friday' || Holiday::where('holidate',Carbon::today())->first() != null){
            return true;    
        }
        else{
            return false;
        }
    }

    public function daysCount($month,$year){
        if($month == '4' || $month == '6' || $month == '9' || $month == '11'){
            return '30';
        }
        elseif($month == '2'){
            if($this->isLeapYear(intval($year))){
                return '29';
            }
            else{
                return '28';
            }
        }
        else{
            return '31';
        }
    }
}
