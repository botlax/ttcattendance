<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'role';
    public $timestamps = false;
     public function user(){
    	return $this->hasMany('App\User');
    }
}
