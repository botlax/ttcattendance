<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Labor extends Model
{
    //

	protected $table = 'labor';
	public $timestamps = false;
	protected $fillable = ['name','employee_no','trade_id','site_id','basic_salary','allowance'];
	public function site(){
		return $this->belongsTo('App\Site');
	}
	public function trade(){
		return $this->belongsTo('App\Trade');
	}

	public function Attendance(){
		return $this->belongsToMany('App\Attendance','labor_attendance')->withPivot('attended','ot','bot','locked','site');
	}

	public function getOvertimeAttribute(){
		$dateId = Attendance::latest('att_date')->first()->id;
		return $this->attendance()->where('id','=',$dateId)->first()->pivot->ot;
	}

	public function getBonusOtAttribute(){
		$dateId = Attendance::latest('att_date')->first()->id;
		return $this->attendance()->where('id','=',$dateId)->first()->pivot->bot;
	}

	public function getPresentAttribute(){
		$dateId = Attendance::latest('att_date')->first()->id;
		return $this->attendance()->where('id','=',$dateId)->first()->pivot->attended;
	}

}

