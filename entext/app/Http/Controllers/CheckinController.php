<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CheckinController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // ログイン情報
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Log::info('top index IP = ' . $_SERVER['REMOTE_ADDR']);
        $this->accsess_info();

        $common_no = '00_5';
        $compacts = compact( 'common_no' );

        return view('checkin.index', $compacts );
    }
    public function post(Request $request,$id)
    {
        $url = 'checkpo/'.$id.'/statistics';
        return redirect()->route($url);

    }
}
