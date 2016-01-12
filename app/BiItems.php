<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BiItems extends Model
{
    protected $table = 'bi_items';
    protected $fillable = ['item'];
    public $timestamps = false;

    public function bicards(){
        return $this->belongsToMany('App\BiCards','bi_card_items','item_id','card_id')->withPivot('thicked');
      }

	public function biserver(){
        return $this->belongsToMany('App\BiServer','bi_balls','item_id','server_id');
      }
}

