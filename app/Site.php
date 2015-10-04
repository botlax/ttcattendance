<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Site extends Model
{

	protected $table = 'site';
	protected $fillable = ['code','name','user_id'];
	public $timestamps = false;
    public function labor(){
    	return $this->hasMany('App\Labor');
    }

    public function user(){
    	return $this->belongsTo('App\User');
    }
}
