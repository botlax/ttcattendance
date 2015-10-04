<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    //
	protected $table = 'attendance';
	public $timestamps = false;
	protected $dates = ['att_date'];

	public function labor(){
		return $this->belongsToMany('App\Labor','labor_attendance')->withPivot('attended','ot','bot','locked');
	}

}

