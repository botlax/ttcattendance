<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class LoanMonths extends Model
{
    //
	protected $table = 'loan_months';
	public $timestamps = false;
	protected $fillable = ['deduction_date','labor_id','loan_id'];
	protected $dates = ['deduction_date'];

	public function loan(){
		return $this->belongsTo('App\Loan');
	}

	public function getDeductionMonthAttribute(){
		return $this->deduction_date->format('Ym');
	}

}

