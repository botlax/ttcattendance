<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Loan extends Model
{
    //

	protected $table = 'loan';
	public $timestamps = false;
	protected $fillable = ['amount','months-to-pay','starting_date','interval','labor_id'];
	protected $dates = ['starting_date'];

	public function labor(){
		return $this->belongsTo('App\Labor');
	}
	
	public function setStartingDateAttribute($date){
		$this->attributes['starting_date'] = Carbon::parse($date);
	}

}

