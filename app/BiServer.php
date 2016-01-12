<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BiServer extends Model
{
    protected $table = 'bi_server';
    protected $fillable = ['name','password','mode','winners','start'];
    public $timestamps = false;

    public function setPasswordAttribute($password){
    	$this->attributes['password'] = bcrypt($password);
    }

    public function biitems(){
        return $this->belongsToMany('App\BiItems','bi_balls','server_id','item_id');
    }

    public function biplayers(){
        return $this->hasMany('App\BiPlayers','server_id');
    }

    public function bicards(){
        return $this->hasManyThrough('App\BiCards', 'App\BiPlayers','server_id','player_id');
    }
}

