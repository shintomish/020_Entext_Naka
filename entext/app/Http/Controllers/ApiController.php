<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\Stuff;
use App\Models\StuffAttendance;
use DateTime;

use Illuminate\Http\Request;
use Twilio\Rest\Client; // 追加
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{
    /**
     * APIのログイン処理
     */
    public function login(Request $request)
    {
        Log::info('api login START');

        DB::beginTransaction();
        Log::info('api login beginTransaction - start');
        try{
            if (!Auth::attempt(request(['email', 'password']))) {
                Log::info('api login END-Error');
                return response()->json([
                    'message' => 'Unauthorized'
                ], 401);
            }

            $accessToken = Auth::user()->createToken('authToken')->plainTextToken;
            Log::info('api login END-Success');

            DB::commit();
        }
        catch(\QueryException $e) {
            Log::error('api login exception : ' . $e->getMessage());
            DB::rollback();
            Log::info('api login beginTransaction - end(rollback)');
        }

        // Log::debug('api login accessToken = ' . print_r($accessToken,true));
        Log::info('api login END');

        return response()->json([
            'access_token' => $accessToken,
        ]);
    }

    /**
     * APIのログアウト処理
     */
    public function logout(Request $request)
    {
        Log::info('api logout START');

        $result = array();
        DB::beginTransaction();
        Log::info('api logout beginTransaction - start');
        try{
            $user = $request->user();
            // Log::debug('api logout user = ' . print_r($user,true));

            if( $user ){
                // 全てのトークンを削除
                $user->tokens()->delete();
                $result = array(  'error_code' => 0
                , 'message'    => 'You have successfully logged off.' );
            }
            else{
                $result = array(  'error_code' => 421
                , 'message'    => 'The user for the token is not logged in.' );
            }
            DB::commit();
        }
        catch( Exception $e ){
            $result = array(  'error_code' => 422
                            , 'message'    => 'An exception error occurred during logoff processing.' );
            Log::error('api logout exception : ' . $e->getMessage());
            DB::rollback();
            Log::info('api logout beginTransaction - end(rollback)');
        }

        $json = response()->json([ compact('result') ]);
        // Log::debug('json = ' . print_r($json,true));

        Log::info('api logout END');
        return $json;
    }

    /**
     * [TEST用]ログインユーザー情報取得
     */
    public function test_user(Request $request)
    {
        Log::info('api test_user START');

        $user = $request->user();
        Log::debug('api test_user user = ' . print_r($user,true));

        $json = response()->json( $user );
        Log::debug( 'api test_user $json = ' . print_r($json,true) );
        Log::info('api test_user END');
        return $json;
    }

    /**
     * 生徒の入室処理
     */
    public function student_attend_in(Request $request)
    {
        Log::info('student_attend_in START');

        $result = $this->student_attend($request, true);

        // $strOutRet = $result["status"];
        // if ($strOutRet == "1")
        // {
        // Log::debug( 'student_attend_in $strOutRet = ' . print_r($strOutRet,true) );
        //     if(isset($result["student_info"]["phone_1"])){
        //         $phone_1 = '+81' . $result["student_info"]["phone_1"];
        // Log::debug( 'student_attend_in $phone_1 = ' . print_r($phone_1,true) );
        //         $message = "";
        //         $message = $message . "\nAizen\n";
        //         $message = $message . "◆パソコン教室◆\n";
        //         $message = $message . "\n";
        //         $message = $message . $result["student_info"]["last_name"];
        //         $message = $message . $result["student_info"]["first_name"];
        //         $message = $message . " さんが、\n";
        //         $message = $message . $result["student_info"]["entrytime"];
        //         $message = $message . "\n";
        //         $message = $message . "に、入室しました。";
        //         $this->sendSms( $message, $phone_1);
        //     }
        // }

        $json = response()->json( $result );
        // Log::debug( 'student_attend_in $json = ' . print_r($json,true) );
        Log::info('student_attend_in END');
        return $json;
    }

    /**
     * 生徒の退室処理
     */
    public function student_attend_out(Request $request)
    {
        Log::info('student_attend_out START');

        $result = $this->student_attend($request, false);

        // $strOutRet = $result["status"];
        // if ($strOutRet == "1")
        // {
        //     if(isset($result["student_info"]["phone_1"])) {
        //         $phone_1 = '+81' . $result["student_info"]["phone_1"];
        //         $message = "";
        //         $message = $message . "\nAizen\n";
        //         $message = $message . "◆パソコン教室◆\n";
        //         $message = $message . "\n";
        //         $message = $message . $result["student_info"]["last_name"];
        //         $message = $message . $result["student_info"]["first_name"];
        //         $message = $message . " さんが、\n";
        //         $message = $message . $result["student_info"]["exittime"];
        //         $message = $message . "\n";
        //         $message = $message . "\n に、退室しました。";
        //         $this->sendSms( $message, $phone_1);
        //     }
        // }

        $json = response()->json( $result );
        // Log::debug( 'student_attend_out $json = ' . print_r($json,true) );
        Log::info('student_attend_out END');
        return $json;
    }

    /**
     *
     */
    protected function student_attend(Request $request, $in = true)
    {
        Log::info('student_attend START');
        $result = array();

        DB::beginTransaction();
        Log::info('student_attend beginTransaction - start');
        try{
            // request_dataから必要情報抽出
            $user      = $request->user();      // ログインユーザー情報
            $ic_number = $request->ic_number;   // ICカード情報
            $overwrite = $request->overwrite;   // 上書き
            $overwrite = ( is_null($overwrite) ) ? true : $overwrite;
            // Log::debug( '$user      = ' . print_r($user,true) );
            Log::debug( 'student_attend $ic_number = ' . $ic_number );
            // Log::debug( '$overwrite = ' . $overwrite );
            // Log::debug( '$in        = ' . $in );

            // ICカード情報から生徒取得
            // $student = Student::where('organization_id',$user->organization_id)
            $student = Student::where('ic_number',$ic_number)
                            // ->where('ic_number',$ic_number)
                            ->first();

            if( $student == null ){
                // Log::debug( 'student_attend $student = null');
                $result['status']   = 3;    // ICカード番号の登録無し
                $result['message']  = 'The specified IC card number is not registered.';
            }
            else{
                // 生徒の出退勤状況確認
                $nowDate = new DateTime('now');
                $date_now = $nowDate->format('Y-m-d');
                $time_now = $nowDate->format('H:i:s');

                // Log::debug('student_attend $date_now = ' . $date_now );
                // Log::debug('student_attend $time_now = ' . $time_now );

                $studentattendances = DB::table('studentattendances')
                                    // ->where('organization_id', $student->organization_id)
                                    ->where('student_id'     , $student->id)
                                    ->where('eventdate'      , $date_now)
                                    ->first();
                // Log::debug('student_attend $studentattendancess = ' . print_r($studentattendances,true) );

                if (true == $in) {
                    // Log::debug('student_attend true == $in');

                    //-------------------------------------------------------------------------
                    //- 出席処理
                    //-------------------------------------------------------------------------
                    if ( $studentattendances === null ) {
                        // Log::debug('student_attend $studentattendances === null');

                        // 新規作成
                        $studentattendances_id = StudentAttendance::insertGetId(
                            ['organization_id' => $student->organization_id
                            ,'student_id'      => $student->id
                            ,'eventdate'       => $date_now ]);
                        $studentattendances = StudentAttendance::find($studentattendances_id);
                        // Log::debug('student_attend $studentattendances2 = ' . print_r($studentattendances,true) );
                    }

                    if (!empty($studentattendances->exittime) && 1 <= $studentattendances->status) {
                        // Log::debug('student_attend !empty($studentattendances->exittime)');

                        // 既に退室処理済み
                        $result['status']   = 4;                   // メソッドの戻り値 - 退室済み
                        $result['message']
                            = 'The nursery student has already left the room.';  // 処理結果メッセージ
                    }
                    else if (empty($studentattendances->status) || $overwrite == true ) {
                        // Log::debug('student_attend empty($studentattendances->status) || $overwrite == true ');

                        // 生徒の出席処理実施
                        $update = [];
                        $update['status']     = 1;
                        $update['entrytime']  = $time_now;
                        // $update['created_at'] = date('Y-m-d H:i:s');
                        $update['updated_at'] = date('Y-m-d H:i:s');
                        StudentAttendance::where('id', $studentattendances->id)->update($update);
                        $studentattendances = StudentAttendance::where('id', $studentattendances->id)->first();
                        // Log::debug('student_attend $studentattendances3 = ' . print_r($studentattendances,true) );

                        // 戻り値の設定
                        $result['status']   = 1;                   // メソッドの戻り値 - 新規登録
                        $result['message']
                            = 'A new entry record has been added.';  // 処理結果メッセージ
                    }
                    else {
                        // Log::debug('student_attend already been registered');

                        // 1:通常(出席), 2:遅刻, 3:早退, 4:休暇

                        // 既に出席処理済み
                        $result['status']   = 2;                      // メソッドの戻り値 - 登録済み
                        $result['message']
                            = 'Entry information has already been registered.';  // 処理結果メッセージ
                    }

                    $student_info['last_name']  = $student->last_name;
                    $student_info['first_name'] = $student->first_name;
                    $student_info['phone_1']    = $student->phone_1;
                    $student_info['eventdate']  = $studentattendances->eventdate;
                    $student_info['entrytime']  = $studentattendances->entrytime;
                    $student_info['exittime']   = $studentattendances->exittime;
                    $student_info['status']     = $studentattendances->status;
                    $result['student_info']     = $student_info;
                }
                else{
                    // Log::debug('student_attend false == $in');
                    //-------------------------------------------------------------------------
                    //- 退席処理
                    //-------------------------------------------------------------------------
                    if ($studentattendances === null) {
                        // Log::debug('student_attend not entered the room today');
                        // 出席処理していない
                        $result['status']   = 2;
                        $result['message'] = 'The member has not entered the room today.';
                    }
                    else{
                        // Log::debug('student_attend $studentattendances is not null');
                        $update = [];
                        $isDo = false;

                        if( isset($studentattendances->exittime) ){
                            if($overwrite){
                                // Log::debug('student_attend if($overwrite)');
                                // 上書きのため退席処理を行なう
                                $isDo = true;
                            }
                            else{
                                // Log::debug('student_attend already left');
                                // 退席時刻は、打刻済みのため退席済みなので何もしない
                                $result['status']   = 4;
                                $result['message'] = 'The member has already left.';
                            }
                        }
                        elseif ( $studentattendances->status == 0 ) {
                            Log::debug('student_attend $studentattendances->status == 0');
                            // 0:出席していないため退席処理は行わない
                            $result['status']   = 2;
                            $result['message'] = "The member has not entered the room today.";
                        }
                        elseif ( $studentattendances->status == 1 ) {
                            Log::debug('student_attend $studentattendances->status == 1');
                            // 1:通常(出席)済みのため退席処理を行なう
                            $isDo = true;
                        }
                        elseif ( $studentattendances->status == 2 ) {
                            Log::debug('student_attend $studentattendances->status == 2');
                            // 2:遅刻(出席済み)のため退席処理を行なう
                            $isDo = true;
                        }
                        elseif ( $studentattendances->status == 3 ) {
                            Log::debug('student_attend $studentattendances->status == 3');
                            // 3:早退(出席済み)のため退席処理を行なう
                            $isDo = true;
                        }
                        elseif ( $studentattendances->status == 4 ) {
                            Log::debug('student_attend $studentattendances->status == 4');
                            // 4:休暇のため退席処理は行わない
                            $result['status']   = 4;
                            $result['message'] = 'The designated student is a rest day.';
                        }
                        else{
                            Log::debug('student_attend $studentattendances->status unknown');
                            // studentattendances.statusが不明
                            $result['status']   = -1;
                            $result['message'] = 'studentattendances.status is an unexpected value.';
                        }

                        if( $isDo == true ){
                            // Log::debug('student_attend $isDo == true');

                            //----------------------------------------------------------------
                            //- 退席処理の実施
                            //----------------------------------------------------------------
                            $update['exittime']   = $time_now;
                            $update['updated_at'] = date('Y-m-d H:i:s');
                            StudentAttendance::where('id', $studentattendances->id)->update($update);
                            $studentattendances = StudentAttendance::where('id', $studentattendances->id)->first();
                            // Log::debug('student_attend $studentattendances4 = ' . print_r($studentattendances,true) );

                            $result['status']   = 1;
                            $result['message'] = 'The student leaving work was processed.';
                        }

                        $student_info['last_name']  = $student->last_name;
                        $student_info['first_name'] = $student->first_name;
                        $student_info['phone_1']    = $student->phone_1;
                        $student_info['eventdate']  = $studentattendances->eventdate;
                        $student_info['entrytime']  = $studentattendances->entrytime;
                        $student_info['exittime']   = $studentattendances->exittime;
                        $student_info['status']     = $studentattendances->status;
                        $result['student_info']     = $student_info;
                    }
                }
            }
            DB::commit();
            Log::info('student_attend beginTransaction - end');
        }
        catch(\QueryException $e) {
            Log::error('exception : ' . $e->getMessage());
            DB::rollback();
            Log::info('student_attend beginTransaction - end(rollback)');
        }
        // Log::debug('student_attend $result = ' . print_r($result,true));

        Log::info('student_attend END');
        return $result;
    }

    /**
     * 職員の入室処理
     */
    public function stuff_attend_in(Request $request)
    {
        Log::info('stuff_attend_in START');

        $result = $this->stuff_attend($request, true);

        // $strOutRet = $result["status"];
        // if ($strOutRet == "1")
        // {
        // Log::debug( 'stuff_attend_in $strOutRet = ' . print_r($strOutRet,true) );
        //     if(isset($result["stuff_info"]["phone_1"])){
        //         $phone_1 = '+81' . $result["stuff_info"]["phone_1"];
        // Log::debug( 'stuff_attend_in $phone_1 = ' . print_r($phone_1,true) );
        //         $message = "";
        //         $message = $message . "\nAizen\n";
        //         $message = $message . "◆パソコン教室◆\n";
        //         $message = $message . "\n";
        //         $message = $message . $result["stuff_info"]["last_name"];
        //         $message = $message . $result["stuff_info"]["first_name"];
        //         $message = $message . " さんが、\n";
        //         $message = $message . $result["stuff_info"]["entrytime"];
        //         $message = $message . "\n";
        //         $message = $message . "に、入室しました。";
        //         $this->sendSms( $message, $phone_1);
        //     }
        // }

        $json = response()->json( $result );
        // Log::debug( 'stuff_attend_in $json = ' . print_r($json,true) );
        Log::info('stuff_attend_in END');
        return $json;
    }

    /**
     * 職員の退室処理
     */
    public function stuff_attend_out(Request $request)
    {
        Log::info('stuff_attend_out START');

        $result = $this->stuff_attend($request, false);

        // $strOutRet = $result["status"];
        // if ($strOutRet == "1")
        // {
        //     if(isset($result["stuff_info"]["phone_1"])) {
        //         $phone_1 = '+81' . $result["stuff_info"]["phone_1"];
        //         $message = "";
        //         $message = $message . "\nAizen\n";
        //         $message = $message . "◆パソコン教室◆\n";
        //         $message = $message . "\n";
        //         $message = $message . $result["stuff_info"]["last_name"];
        //         $message = $message . $result["stuff_info"]["first_name"];
        //         $message = $message . " さんが、\n";
        //         $message = $message . $result["stuff_info"]["exittime"];
        //         $message = $message . "\n";
        //         $message = $message . "\n に、退室しました。";
        //         $this->sendSms( $message, $phone_1);
        //     }
        // }

        $json = response()->json( $result );
        // Log::debug( 'stuff_attend_out $json = ' . print_r($json,true) );
        Log::info('stuff_attend_out END');
        return $json;
    }

    /**
     *
     */
    protected function stuff_attend(Request $request, $in = true)
    {
        Log::info('stuff_attend START');
        $result = array();

        DB::beginTransaction();
        Log::info('stuff_attend beginTransaction - start');
        try{
            // request_dataから必要情報抽出
            $user      = $request->user();      // ログインユーザー情報
            $ic_number = $request->ic_number;   // ICカード情報
            $overwrite = $request->overwrite;   // 上書き
            $overwrite = ( is_null($overwrite) ) ? true : $overwrite;
            // Log::debug( '$user      = ' . print_r($user,true) );
            Log::debug( 'stuff_attend $ic_number = ' . $ic_number );
            // Log::debug( '$overwrite = ' . $overwrite );
            // Log::debug( '$in        = ' . $in );

            // ICカード情報から職員取得
            // $stuff = Stuff::where('organization_id',$user->organization_id)
            $stuff = Stuff::where('ic_number',$ic_number)
                            // ->where('ic_number',$ic_number)
                            ->first();

            if( $stuff == null ){
                // Log::debug( 'stuff_attend $stuff = null');
                $result['status']   = 3;    // ICカード番号の登録無し
                $result['message']  = 'The specified IC card number is not registered.';
            }
            else{
                // 職員の出退勤状況確認
                $nowDate = new DateTime('now');
                $date_now = $nowDate->format('Y-m-d');
                $time_now = $nowDate->format('H:i:s');

                // Log::debug('stuff_attend $date_now = ' . $date_now );
                // Log::debug('stuff_attend $time_now = ' . $time_now );

                $stuffattendances = DB::table('stuffattendances')
                                    // ->where('organization_id', $stuff->organization_id)
                                    ->where('stuff_id'     , $stuff->id)
                                    ->where('eventdate'      , $date_now)
                                    ->first();
                // Log::debug('stuff_attend $stuffattendancess = ' . print_r($stuffattendances,true) );

                if (true == $in) {
                    // Log::debug('stuff_attend true == $in');

                    //-------------------------------------------------------------------------
                    //- 出勤処理
                    //-------------------------------------------------------------------------
                    if ( $stuffattendances === null ) {
                        // Log::debug('stuff_attend $stuffattendances === null');

                        // 新規作成
                        $stuffattendances_id   = StuffAttendance::insertGetId(
                            ['organization_id' => $stuff->organization_id
                            ,'stuff_id'        => $stuff->id
                            ,'eventdate'       => $date_now ]);
                        $stuffattendances = StuffAttendance::find($stuffattendances_id);
                        // Log::debug('stuff_attend $stuffattendances2 = ' . print_r($stuffattendances,true) );
                    }

                    if (!empty($stuffattendances->exittime) && 1 <= $stuffattendances->status) {
                        // Log::debug('stuff_attend !empty($stuffattendances->exittime)');

                        // 既に退室処理済み
                        $result['status']   = 4;                   // メソッドの戻り値 - 退室済み
                        $result['message']
                            = 'The nursery Stuff has already left the room.';  // 処理結果メッセージ
                    }
                    else if (empty($stuffattendances->status) || $overwrite == true ) {
                        // Log::debug('stuff_attend empty($stuffattendances->status) || $overwrite == true ');

                        // 職員の出勤処理実施
                        $update = [];
                        $update['status']     = 1;
                        $update['entrytime']  = $time_now;
                        // $update['created_at'] = date('Y-m-d H:i:s');
                        $update['updated_at'] = date('Y-m-d H:i:s');
                        StuffAttendance::where('id', $stuffattendances->id)->update($update);
                        $stuffattendances = StuffAttendance::where('id', $stuffattendances->id)->first();
                        // Log::debug('stuff_attend $stuffattendances3 = ' . print_r($stuffattendances,true) );

                        // 戻り値の設定
                        $result['status']   = 1;                   // メソッドの戻り値 - 新規登録
                        $result['message']
                            = 'A new entry record has been added.';  // 処理結果メッセージ
                    }
                    else {
                        // Log::debug('stuff_attend already been registered');

                        // 1:通常(出勤), 2:遅刻, 3:早退, 4:休暇

                        // 既に出勤処理済み
                        $result['status']   = 2;                      // メソッドの戻り値 - 登録済み
                        $result['message']
                            = 'Entry information has already been registered.';  // 処理結果メッセージ
                    }

                    $stuff_info['last_name']  = $stuff->last_name;
                    $stuff_info['first_name'] = $stuff->first_name;
                    $stuff_info['phone_1']    = $stuff->phone_1;
                    $stuff_info['eventdate']  = $stuffattendances->eventdate;
                    $stuff_info['entrytime']  = $stuffattendances->entrytime;
                    $stuff_info['exittime']   = $stuffattendances->exittime;
                    $stuff_info['status']     = $stuffattendances->status;
                    $result['stuff_info']     = $stuff_info;
                }
                else{
                    // Log::debug('stuff_attend false == $in');
                    //-------------------------------------------------------------------------
                    //- 退勤処理
                    //-------------------------------------------------------------------------
                    if ($stuffattendances === null) {
                        // Log::debug('stuff_attend not entered the room today');
                        // 出勤処理していない
                        $result['status']   = 2;
                        $result['message'] = 'The member has not entered the room today.';
                    }
                    else{
                        // Log::debug('stuff_attend $stuffattendances is not null');
                        $update = [];
                        $isDo = false;

                        if( isset($stuffattendances->exittime) ){
                            if($overwrite){
                                // Log::debug('stuff_attend if($overwrite)');
                                // 上書きのため退勤処理を行なう
                                $isDo = true;
                            }
                            else{
                                // Log::debug('stuff_attend already left');
                                // 退室時刻は、打刻済みのため退室済みなので何もしない
                                $result['status']   = 4;
                                $result['message'] = 'The member has already left.';
                            }
                        }
                        elseif ( $stuffattendances->status == 0 ) {
                            Log::debug('stuff_attend $stuffattendances->status == 0');
                            // 0:出勤していないため退勤処理は行わない
                            $result['status']   = 2;
                            $result['message'] = "The member has not entered the room today.";
                        }
                        elseif ( $stuffattendances->status == 1 ) {
                            Log::debug('stuff_attend $stuffattendances->status == 1');
                            // 1:通常(出勤)済みのため退勤処理を行なう
                            $isDo = true;
                        }
                        elseif ( $stuffattendances->status == 2 ) {
                            Log::debug('stuff_attend $stuffattendances->status == 2');
                            // 2:遅刻(出勤済み)のため退勤処理を行なう
                            $isDo = true;
                        }
                        elseif ( $stuffattendances->status == 3 ) {
                            Log::debug('stuff_attend $stuffattendances->status == 3');
                            // 3:早退(出勤済み)のため退勤処理を行なう
                            $isDo = true;
                        }
                        elseif ( $stuffattendances->status == 4 ) {
                            Log::debug('stuff_attend $stuffattendances->status == 4');
                            // 4:休暇のため退勤処理は行わない
                            $result['status']   = 4;
                            $result['message'] = 'The designated Stuff is a rest day.';
                        }
                        else{
                            Log::debug('stuff_attend $stuffattendances->status unknown');
                            // stuffattendances.statusが不明
                            $result['status']   = -1;
                            $result['message'] = 'stuffattendances.status is an unexpected value.';
                        }

                        if( $isDo == true ){
                            // Log::debug('stuff_attend $isDo == true');

                            //----------------------------------------------------------------
                            //- 退勤処理の実施
                            //----------------------------------------------------------------
                            $update['exittime']   = $time_now;
                            $update['updated_at'] = date('Y-m-d H:i:s');
                            StuffAttendance::where('id', $stuffattendances->id)->update($update);
                            $stuffattendances = StuffAttendance::where('id', $stuffattendances->id)->first();
                            // Log::debug('stuff_attend $stuffattendances4 = ' . print_r($stuffattendances,true) );

                            $result['status']   = 1;
                            $result['message'] = 'The Stuff leaving work was processed.';
                        }

                        $stuff_info['last_name']  = $stuff->last_name;
                        $stuff_info['first_name'] = $stuff->first_name;
                        $stuff_info['phone_1']    = $stuff->phone_1;
                        $stuff_info['eventdate']  = $stuffattendances->eventdate;
                        $stuff_info['entrytime']  = $stuffattendances->entrytime;
                        $stuff_info['exittime']   = $stuffattendances->exittime;
                        $stuff_info['status']     = $stuffattendances->status;
                        $result['stuff_info']     = $stuff_info;
                    }
                }
            }
            DB::commit();
            Log::info('stuff_attend beginTransaction - end');
        }
        catch(\QueryException $e) {
            Log::error('exception : ' . $e->getMessage());
            DB::rollback();
            Log::info('stuff_attend beginTransaction - end(rollback)');
        }
        // Log::debug('stuff_attend $result = ' . print_r($result,true));

        Log::info('stuff_attend END');
        return $result;
    }

    public function sendSms( $message, $number)
    {
        Log::info('sendSms  START');
        // Your Account SID and Auth Token from twilio.com/console
        $sid    = env( 'TWILIO_SID' );
        $token  = env( 'TWILIO_TOKEN' );

        $client = new Client( $sid, $token );

        $client->messages->create(
            $number,
            [
                'from' => env( 'TWILIO_FROM' ),
                'body' => $message,
            ]
        );
        Log::info('sendSms  END');

        return;
    }

}
