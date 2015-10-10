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
        $this->middleware('role',['except' => ['showSearchID','searchID','addAttendance','storeAttendance','updateAttendance','editAttendance','lockAttendance']]);
        $this->middleware('notAdmin',['only' => ['showSearchID','searchID','addAttendance','storeAttendance','updateAttendance','editAttendance','lockAttendance']]);
    }

    /**
     * Display options for filtering attendance list.
     *
     * @return Response
     */
    public function showFilterOptions()
    {
        $sites = Site::where('id','>',1)->get()->lists('code','id')->toArray();
        $months = ['1'=>'January','2'=>'February','3'=>'March','4'=>'April','5'=>'May','6'=>'June','7'=>'July','8'=>'August','9'=>'September','10'=>'October','11'=>'November','12'=>'December'];
        $years = [];
        for($y=2015;$y<=intval(date('Y'));$y++){
            $years[$y] = $y; 
        }
        return view('pages.filteroptions',compact('sites','months','years'));
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
            $ot_count = 0;
            $bot_count = 0;

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
                        $labor_att[$labor->employee_no]['bot'][$dateFrom->format('Y-m-d')] = $att_entry->pivot->bot;
                        $labor_att[$labor->employee_no]['site'][$dateFrom->format('Y-m-d')] = $att_entry->pivot->site;

                        $ot_count += intval($att_entry->pivot->ot);
                        $bot_count += intval($att_entry->pivot->bot);
                    }
                }
                elseif(!is_null($att_entry) && $att_entry->pivot->attended == '0'){
                    $labor_att[$labor->employee_no]['ot'][$dateFrom->format('Y-m-d')] = '0';
                    $labor_att[$labor->employee_no]['bot'][$dateFrom->format('Y-m-d')] = '0';
                    $labor_att[$labor->employee_no]['site'][$dateFrom->format('Y-m-d')] = '0';
                }
                else{
                    $labor_att[$labor->employee_no]['ot'][$dateFrom->format('Y-m-d')] = '—';
                    $labor_att[$labor->employee_no]['bot'][$dateFrom->format('Y-m-d')] = '—';
                    $labor_att[$labor->employee_no]['site'][$dateFrom->format('Y-m-d')] = '—';
                }
                $total[$labor->employee_no]['attended'] = $att_count;
                $total[$labor->employee_no]['ot'] = $ot_count;
                $total[$labor->employee_no]['bot'] =  $bot_count;
                
                //total salary
                $salary[$labor->employee_no]['attended'] = round(((intval($labor->basic_salary) + intval($labor->allowance)) / intval($dEnd)) * $att_count,2);
                $salary[$labor->employee_no]['ot'] = $ot_count;
                $salary[$labor->employee_no]['bot'] =  $bot_count;
            }
            $dateFrom = Carbon::parse('1-'.$month.'-'.$year);
        }

        if($request->input('makesheet')){
            \Excel::create('Attendance', function($excel) use($month,$year,$labors,$labor_att,$total){
                $excel->setTitle('Attendance');
                $excel->setCreator('www.ttc-attendance.tk')
                      ->setCompany('Talal Trading & Contracting Co.');

                $excel->sheet('Sheetname', function($sheet) use($month,$year,$labors,$labor_att,$total){

                    //data
                    $heading = ['ID','Name','Trade','Date'];
                    for($x=1;$x <= $this->daysCount($month,$year);$x++){
                        $heading[] = $x;
                    }
                    $heading[] = 'Total';

                    //initial setup
                    $sheet->setOrientation('landscape');
                    $sheet->setPageMargin(0.25);
                    //$sheet->protect('1121');

                    //sheet manipulation
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
                        //$data[3] = 'Attended';
                        $sheet->row($row, $data);
                        $row++;
                    }


                    $sheet->setWidth(array(
                        'A' => 8, 'B' => 20, 'C' => 15, 'D' => 15, 'E' => 5, 'F' => 5, 'G' => 5,
                        'H' => 5, 'I' => 5, 'J' => 5, 'K' => 5, 'L' => 5, 'M' => 5, 'N' => 5,
                        'O' => 5, 'P' => 5, 'Q' => 5, 'R' => 5, 'S' => 5, 'T' => 5, 'U' => 5,
                        'V' => 5, 'W' => 5, 'X' => 5, 'Y' => 5, 'Z' => 5, 'AA' => 5, 'AB' => 5,
                        'AC' => 5, 'AD' => 5, 'AE' => 5, 'AF' => 5, 'AG' => 5, 'AH' => 5, 'AI' => 5,
                        'AJ' => 5, 'AK' => 5, 'AK' => 5,
                        
                    ));
                    //cell styling
                    $sheet->cells('A4:AK4', function($cells) {

                        $cells->setFontWeight('bold');
                        //$cells->setBorder('medium', 'medium', 'medium', 'medium');
                        $cells->setAlignment('center');
                        $cells->setValignment('middle');
                        //$cells->setBackground('#DDDDDA');

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

        $entry = Labor::find($id)->attendance()->where('att_date',$dateF)->first()->pivot;
        //dd($request->input('attended'));
        if($request->input('attended') != null){

            $entry->attended = $request->input('attended');
            if($request->input('attended') == '0'){
                $entry->ot = 0;
                $entry->bot = 0;
            }
        }
        elseif($request->input('ot') != null){
            $ot = $request->input('ot') == ""?0:$request->input('ot');
            $entry->ot = $ot;
        }
        elseif($request->input('bot') != null){
            $bot = $request->input('bot') == ""?0:$request->input('bot');
            $entry->bot = $bot;
        }
        elseif($request->input('site') != null){
            $entry->site = $request->input('site');
        }

        if((isset($ot) && $ot != 0) || (isset($bot) && $bot != 0)){
            $entry->attended = 1;
        }

        $entry->save();
        return redirect('attendance');
    }

    public function getSelectOptions()
    {
        $field = \Input::get('field');
        if($field == 'site'){
            $response = Site::all()->lists('code','code')->toArray();
        }
        else{
            $response = ["1"=>"YES","0"=>"NO"];
        }

        echo json_encode($response);
    }

    public function updateAjaxEntry()
    {   

        $field = \Input::get('field');
        $dateF = Carbon::parse(\Input::get('date'));
        $id = \Input::get('id');
        $input = \Input::get('entry');
        $entry = Labor::find($id)->attendance()->where('att_date',$dateF)->first()->pivot;
        
        $result = 2;
        if($field == 'attended'){

            $entry->attended = $input;
            if($input == '0'){
                $entry->ot = 0;
                $entry->bot = 0;
                $result = 0;
            }
        }
        elseif($field == 'ot'){
            $ot = $input == ""?0:$input;
            $entry->ot = $ot;
        }
        elseif($field == 'bot'){
            $bot = $input == ""?0:$input;
            $entry->bot = $bot;
        }
        elseif($field == 'site'){
            if($input == ''){
                $entry->site = '—';
                $result = 3;
            }
            else{
                $entry->site = $input;
            }
        }

        if((isset($ot) && $ot != 0) || (isset($bot) && $bot != 0)){
            $entry->attended = 1;
            $result = 1;
        }
        $entry->save();

        $response = ['result'=>$result,'field'=>$field,'date'=>$dateF->format('Y-m-d'),'en'=>$id,'entry'=>$input];
        echo json_encode($response);
        die();
    }

    /**
     * Display list of labors under the signed in user
     *
     * @return Response
     */
    public function showSearchID()
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

        $userID = \Auth::user()->id;
        $dateId = $this->getDateId();
        $locked = $this->todayLocked();

        $user = \Auth::user();
        $sites = $user->site;
        return view('pages.attendance_list',compact('sites','locked','dateId','userID'));
    }

    /**
     * Display list of labors under the signed in user
     *
     * @return Response
     */
    public function searchID(Request $request)
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
            $attendance->pivot->ot = $request->input('overtime');
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
                $att = $labor->attendance()->where('id',$this->getDateId())->first()->pivot;
                $att->locked = 'true';
                $att->attended = 0;
                $att->ot = 0;
                $att->bot = 0;
                $att->site = '—';
                $att->save();
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
            $attendance->pivot->ot = $request->input('overtime');
            $attendance->pivot->bot = $request->input('bonus_ot');
            $attendance->pivot->save();
            //dd($attendance->pivot->attended);
        }
        
        return redirect('attendance/list');
    }

    /**
     * create spreadsheet
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function makeSheet(Request $request)
    {

        $month = $request->input('month');
        $year = $request->input('year');
        
        \Excel::create('Attendance', function($excel) use($month,$year){
            $excel->setTitle('Attendance');
            $excel->setCreator('www.ttc-attendance.tk')
                  ->setCompany('Talal Trading & Contracting Co.');

            $excel->sheet('Sheetname', function($sheet) use($month,$year){

                //data
                $heading = ['ID','Name','Trade','Date'];
                for($x=1;$x <= $this->daysCount($month,$year);$x++){
                    $heading[] = $x;
                }
                $heading[] = 'Total';
               


                //initial setup
                $sheet->setOrientation('landscape');
                $sheet->setPageMargin(0.25);
                $sheet->protect('1121');
                //sheet manipulation
                $sheet->row(4, $heading);

            });
        })->download('xls');
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

    public function todayLocked(){
        $user = \Auth::user();
        if($this->hasEntry()){
            if($user->labor->first()->attendance()->latest('att_date')->first() != null && $user->labor->first()->attendance()->latest('att_date')->first()->pivot->locked == 'true'){
                //dd($user->labor->first()->attendance()->where('att_date',Carbon::now()->first());
                return true;
            }
        }
        else{
            return false;
        }
        //return Attendance::latest('att_date')->first()->locked;
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
