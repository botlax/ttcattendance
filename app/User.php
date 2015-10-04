<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract,
                                    AuthorizableContract,
                                    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'employee_no', 'password','role_id'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];
    public $timestamps = false;
    

    public function labor(){
        return $this->hasManyThrough('App\Labor', 'App\Site');
    }

    public function site(){
        return $this->hasMany('App\Site');
    }

    public function role(){
         return $this->belongsTo('App\Role');
    }

    public function isAdmin(){
        if(\Auth::user()->role->role == 'Admin'){
            return true;
        }
        else{
            return false;
        }
    }

    public function isNotAdmin(){
        if(\Auth::user()->role->role == 'Site In-charge'){
            return true;
        }
        else{
            return false;
        }
    }
}
