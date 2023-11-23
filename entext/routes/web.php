<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
    // return view('top.index');
});

Route::group(['middleware' => 'web'], function () {
    //
    Route::auth();
    //
    Route::get('/home', 'App\Http\Controllers\UserController@index');
});

Auth::routes();

//-----------------------------------------------------------------------------------------------
//-  SMS
//-----------------------------------------------------------------------------------------------
Route::get('/sms',  'App\Http\Controllers\SmsController@index');
Route::post('/sms', 'App\Http\Controllers\SmsController@sendSms');

//-----------------------------------------------------------------------------------------------
//-  Student
//-----------------------------------------------------------------------------------------------
// Route::post('stu/store_api',  'App\Http\Controllers\StudentController@store_api')->name('stu.store_api');
Route::post('stu/update_api', 'App\Http\Controllers\StudentController@update_api')->name('stu.update_api');
Route::resource('stu',        'App\Http\Controllers\StudentController');

//-----------------------------------------------------------------------------------------------
//-  StudentAttendance
//-----------------------------------------------------------------------------------------------
Route::get('stu-attend/input',  'App\Http\Controllers\StudentAttendanceController@input')->name('stu-attend.input');
Route::get('stu-attend/serch',  'App\Http\Controllers\StudentAttendanceController@serch_stdattend')->name('stu-attendserch');
Route::get('stu-attend/export', 'App\Http\Controllers\StudentAttendanceController@export')->name('stu-attendexport');
Route::resource('stu-attend',   'App\Http\Controllers\StudentAttendanceController');
Route::post('stu-attend/stu',   'App\Http\Controllers\StudentAttendanceController@getstaffs')->name('stu-attend.get-stu');


//-----------------------------------------------------------------------------------------------
//-  Staff
//-----------------------------------------------------------------------------------------------
// Route::post('stuff/store_api',  'App\Http\Controllers\StuffController@store_api')->name('stuff.store_api');
Route::post('stuff/update_api', 'App\Http\Controllers\StuffController@update_api')->name('stuff.update_api');
Route::resource('stuff',        'App\Http\Controllers\StuffController');

//-----------------------------------------------------------------------------------------------
//-  StaffAttendance
//-----------------------------------------------------------------------------------------------
Route::get('stuff-attend/input',    'App\Http\Controllers\StuffAttendanceController@input')->name('stuff-attend.input');
Route::get('stuff-attend/serch',    'App\Http\Controllers\StuffAttendanceController@serch_stdattend')->name('stuff-attendserch');
Route::get('stuff-attend/export',   'App\Http\Controllers\StuffAttendanceController@export')->name('stuff-attendexport');
Route::post('stuff-attend/staff',   'App\Http\Controllers\StuffAttendanceController@getstuffs')->name('stuff-attend.get-staff');
Route::resource('stuff-attend',     'App\Http\Controllers\StuffAttendanceController');

//-----------------------------------------------------------------------------------------------
//-  Client
//-----------------------------------------------------------------------------------------------
Route::resource('client', 'App\Http\Controllers\ClientController');

//-----------------------------------------------------------------------------------------------
//- 事務所 organization 組織
//-----------------------------------------------------------------------------------------------
Route::resource('organization', 'App\Http\Controllers\OrganizationController');

//-----------------------------------------------------------------------------------------------
//- 事務所 user 利用ユーザー 00_1
//-----------------------------------------------------------------------------------------------
Route::resource('user', 'App\Http\Controllers\UserController');

//-----------------------------------------------------------------------------------------------
//- Top
//-----------------------------------------------------------------------------------------------
Route::get('top',  'App\Http\Controllers\TopController@index')->name('top');

//-----------------------------------------------------------------------------------------------
//- Checkin
//-----------------------------------------------------------------------------------------------
Route::get('checkin',  'App\Http\Controllers\CheckinController@index')->name('checkin');
Route::post('checkin',  'App\Http\Controllers\CheckinController@index')->name('checkinpo');
Route::get('checkpo',  'App\Http\Controllers\CheckinController@post')->name('checkpo');
// Route::resource('check',  'App\Http\Controllers\CheckinController');
Route::get('/sent', function(){
    event(new \App\Events\NewMessage('テストメッセージ'));
});
//-----------------------------------------------------------------------------------------------
//- Chat
//-----------------------------------------------------------------------------------------------
Route::get('chat',         'App\Http\Controllers\ChatController@index')->name('chatin');
Route::get('ajax/chatin',  'App\Http\Controllers\Ajax\ChatController@index')->name('ajaxchatin'); // メッセージ一覧を取得
Route::post('ajax/chatcr', 'App\Http\Controllers\Ajax\ChatController@create')->name('ajaxchatcr'); // チャット登録

// Route::get('post',      'App\Http\Controllers\ChatController@index')->name('chatin');
// Route::get('fetchmessages',  'App\Http\Controllers\ChatController@fetchMessages');
// Route::post('sendmessages', 'App\Http\Controllers\ChatController@sendMessage');

//-----------------------------------------------------------------------------------------------
//- Visitor
//-----------------------------------------------------------------------------------------------
Route::get('access_count',      'App\Http\Controllers\VisitorCountController@index')->name('access_count');
Route::get('ajax/access_count', 'App\Http\Controllers\VisitorCountController@ajax_index');



