<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BiPlayers extends Model
{
    protected $table = 'bi_players';
    protected $fillable = ['name','user_id','status','server_id'];
    public $timestamps = false;

    public function biserver(){
        return $this->belongsTo('App\BiServer','server_id');
    }

    public function bicards(){
      	return $this->hasMany('App\BiCards','player_id');
  	}
}

