<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return redirect('attendance');
});

Route::get('/home', function () {
    return redirect('attendance');
});


//attendance
Route::get('/attendance',['uses' => 'AttendanceController@showFilterOptions']);
Route::get('/attendance/filter',['uses' => 'AttendanceController@filterAttendance', 'as' => 'filterAttendance']);
//Route::get('/attendance/create','AttendanceController@createSheet');
Route::get('/attendance/list', ['uses' => 'AttendanceController@showSearchID']);
Route::post('/attendance/list', ['uses' => 'AttendanceController@searchID', 'as' => 'searchID']);
Route::get('/attendance/list/{id}', ['uses' => 'AttendanceController@addAttendance']);
Route::post('/attendance/list/{id}', ['uses' => 'AttendanceController@storeAttendance', 'as' => 'storeAttendance']);
Route::post('/attendance/lock/{id}', ['uses' => 'AttendanceController@lockAttendance', 'as' => 'lockAttendance']);
Route::post('/attendance/update/{id}', ['uses' => 'AttendanceController@updateEntry','as'=>'updateEntry']);
Route::get('/attendance/list/{id}/edit', ['uses' => 'AttendanceController@editAttendance']);
Route::post('/attendance/list/{id}/edit', ['uses' => 'AttendanceController@updateAttendance', 'as' => 'updateAttendance']);
Route::get('/attendance/{date}/{id}/{field}', ['uses' => 'AttendanceController@editEntry']);

//users
Route::get('/users',['uses' => 'UsersController@index']);
Route::get('/users/{id}',['uses' => 'UsersController@show']);
Route::get('/users/{id}/edit',['uses' => 'UsersController@edit']);
Route::get('/users/{id}/delete',['uses' => 'UsersController@destroy']);
Route::post('/users/{id}',['uses' => 'UsersController@update', 'as' => 'updateUser']);

//sites
Route::get('/sites',['uses' => 'SitesController@index']);
Route::get('/sites/add',['uses' => 'SitesController@add']);
Route::post('/sites/add',['uses' => 'SitesController@store', 'as' => 'storeSite']);
Route::get('/sites/{id}/edit',['uses' => 'SitesController@edit']);
Route::get('/sites/{id}/delete',['uses' => 'SitesController@destroy']);
Route::post('/sites/{id}',['uses' => 'SitesController@update', 'as' => 'updateSite']);

//trades
Route::get('/trades',['uses' => 'TradesController@index']);
Route::get('/trades/add',['uses' => 'TradesController@add']);
Route::post('/trades/add',['uses' => 'TradesController@store', 'as' => 'storeTrade']);
Route::get('/trades/{id}/edit',['uses' => 'TradesController@edit']);
Route::get('/trades/{id}/delete',['uses' => 'TradesController@destroy']);
Route::post('/trades/{id}',['uses' => 'TradesController@update', 'as' => 'updateTrade']);

//labors
Route::get('/employees',['uses' => 'LaborController@index']);
Route::post('/employees',['uses' => 'LaborController@search']);
Route::get('/employees/deleted',['uses' => 'LaborController@indexDeleted']);
Route::post('/employees/deleted',['uses' => 'LaborController@searchDeleted']);
Route::post('/employees/deleted/{id}',['uses' => 'LaborController@undeleteLabor','as'=>'undeleteLabor']);
Route::get('/employees/add',['uses' => 'LaborController@add']);
Route::post('/employees/add',['uses' => 'LaborController@store', 'as' => 'storeLabor']);
Route::get('/employees/{id}/edit',['uses' => 'LaborController@edit']);
Route::get('/employees/{id}/delete',['uses' => 'LaborController@destroy']);
Route::post('/employees/{id}',['uses' => 'LaborController@update', 'as' => 'updateLabor']);

// Authentication routes...
Route::get('user/login', ['uses'=>'Auth\AuthController@getLogin']);
Route::post('user/login', 'Auth\AuthController@postLogin');
Route::get('user/logout', 'Auth\AuthController@getLogout');

// Registration routes...
Route::get('user/register', ['uses'=>'Auth\AuthController@getRegister']);
Route::post('user/register', 'Auth\AuthController@postRegister');
