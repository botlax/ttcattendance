<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BiCards extends Model
{
    protected $table = 'bi_cards';
    protected $fillable = ['player_id'];
    public $timestamps = false;

    public function biplayers(){
        return $this->belongsTo('App\BiPlayers','player_id');
    }

    public function biitems(){
        return $this->belongsToMany('App\BiItems','bi_card_items','card_id','item_id')->withPivot('thicked');
      }
}

