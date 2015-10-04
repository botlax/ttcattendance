<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Trade extends Model
{
    //
	protected $table = 'trade';
	protected $fillable = ['name'];
	public $timestamps = false;
    public function labor(){
    	return $this->hasMany('App\Labor');
    }
}
