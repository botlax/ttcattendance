<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    protected $table = 'holiday';
    protected $fillable = ['holidate'];
    public $timestamps = false;
	protected $dates = ['holidate'];
}
