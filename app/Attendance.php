<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attendance extends Model
{
    //
	protected $table = 'attendance';
	public $timestamps = false;
	protected $dates = ['att_date'];

	public function labor(){
		return $this->belongsToMany('App\Labor','labor_attendance')->withPivot('attended','ot','bot','locked');
	}

	public function getAttDateAttribute($date){
		return new Carbon($date);
	}

	public function getStringDateAttribute(){
		return $this->att_date->format('Ymd');
	}

}

