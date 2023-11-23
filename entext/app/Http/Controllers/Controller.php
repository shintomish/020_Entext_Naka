<?php

namespace App\Http\Controllers;

use DateTime;
use App\Models\Organization;
use App\Models\User;
use App\Models\Visitor;
use App\Models\Holiday;
use Carbon\Carbon;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    //-------------------------------------------------------------------------------------------------
    //-- システム関連
    //-------------------------------------------------------------------------------------------------
    /**
     * ログインユーザーの組織オブジェクトを取得する
     */
    public function auth_user_organization()
    {
        Log::info('auth_user_organization START');

        $organization_id = auth::user()->organization_id;
        $ret_val = Organization::find($organization_id);

        // Log::debug('auth_user_organization ret_val = ' . print_r(json_decode($ret_val),true));
        Log::info('auth_user_organization END');
        return $ret_val;
    }

     /**
     * ログインユーザーの組織IDを取得する
     */
    public function auth_user_organization_id()
    {
        Log::info('auth_user_organization_id START');

        $ret_val = auth::user()->organization_id;
        // Log::debug('auth_user_organization_id ret_val = ' . $ret_val);

        Log::info('auth_user_organization_id END');
        return $ret_val;
    }
    /**
     * ログインユーザーのユーザー情報Userを取得する
     */
    public function auth_user_info()
    {
        Log::info('auth_user_info START');

        $id = auth::user()->id;
        $ret_val = User::find($id);

        // Log::debug('auth_user_info ret_val = ' . print_r(json_decode($ret_val),true));
        Log::info('auth_user_info END');
        return $ret_val;
    }

    /**
     * 訪問者のIPアドレスを取得し、Visitorsに設定する
     */
    public function accsess_info()
    {
        Log::info('accsess_info START');
        Log::info('accsess_info IP = ' . $_SERVER['REMOTE_ADDR']);

        $dt = now();

        $visitor            = new Visitor();
        $visitor->ipaddress = $_SERVER['REMOTE_ADDR'];
        $visitor->year      = $dt->year;
        $visitor->month     = $dt->month;
        $visitor->day       = $dt->day;
        $visitor->hour      = $dt->hour;
        $visitor->minute    = $dt->minute;
        $visitor->second    = $dt->second;
        $visitor->save();

        Log::info('accsess_info END');
        // return $ret_val;
    }
    /**
     * 祝日チェック
     */
    function is_holiday($organization_id, $date)
    {
        Log::info('is_holiday START');
        Log::debug('$organization_id=' . $organization_id . ', $date=' . $date);

        // 存在チェック
        $exist = Holiday::whereNull('deleted_at')
                        ->where('organization_id', $organization_id)
                        ->where('date', $date)
                        ->exists();

        Log::debug('ret_val = ' . $exist);
        Log::info('is_holiday END');
        return $exist;
    }

    /**
    * 今月の月を取得
    * @return string
    */
    public function get_now_month(): string
    {
        return DATE_FORMAT(Carbon::now(),'m');
    }
    /**
    * 今年の年を取得
    * @return string
    */
    public function get_now_year(): string
    {
        return DATE_FORMAT(Carbon::now(),'Y');
    }

}
