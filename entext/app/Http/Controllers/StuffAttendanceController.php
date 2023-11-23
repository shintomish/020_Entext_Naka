<?php

namespace App\Http\Controllers;

use Validator;
use DateTime;
use Carbon\Carbon;

use App\Models\Stuff;
use App\Models\StuffAttendance;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StuffAttendanceController extends Controller

{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     *
     */
    function batch_save(Request $request)
    {
        Log::info('stuffattendances batch_save START');

        $year       = $request->input('search_year');
        $month      = $request->input('search_month');

        $thisDate = new DateTime();
        $thisDate->setDate($year,$month,1);

        $id         = $request->input('id');
        $eventdate  = $request->input('eventdate');
        $entrytime  = $request->input('entrytime');
        $exittime   = $request->input('exittime');
        $breaktime  = $request->input('breaktime');
        $worktime   = $request->input('worktime');
        $status     = $request->input('status');
        $comment    = $request->input('comment');
        $care_type  = $request->input('Responsible');

        Log::debug('stuffattendances batch_save Sid:' . print_r($id,true));

        if($id == null){
            Log::debug('id:null');

        }

        if (!is_null($id)) {
            DB::beginTransaction();
            Log::info('stuffattendances batch_save beginTransaction - start');
            try {
                for ($i = 0; $i < count($id); $i++) {
                    $update = [
                        'eventdate'  => (is_null($eventdate[$i]) ? null : $eventdate[$i]),
                        'entrytime'  => (is_null($entrytime[$i]) ? null : $entrytime[$i]),
                        'exittime'   => (is_null($exittime[$i])  ? null : $exittime[$i]),
                        'breaktime'  => (is_null($breaktime[$i]) ? null : $breaktime[$i]),
                        'worktime'   => (is_null($worktime[$i])  ? null : $worktime[$i]),
                        'status'     => (is_null($status[$i])    ? null : $status[$i]),
                        'comment'    => (is_null($comment[$i])   ? null : $comment[$i]),
                        'care_type'  => (is_null($care_type[$i]) ? null : $care_type[$i]),
                        'updated_at' => date('Y-m-d H:i:s')
                    ];
                    Stuffattendance::where('id', $id[$i])->update($update);
                    Log::debug('stuffattendances batch_save update:' . print_r($update, true));
                }
                DB::commit();
                Log::info('stuffattendances batch_save beginTransaction - end');
            }
            catch(\QueryException $e) {
                Log::error('stuffattendances batch_save exception : ' . $e->getMessage());
                DB::rollback();
                Log::info('stuffattendances batch_save beginTransaction - end(rollback)');
            }
        }

        Log::info('stuffattendances batch_save END');
    }

    /**
     *
     */
    function create_data( $year, $month, $stuff_id )
    {
        Log::info('create_data START');

        $organization = $this->auth_user_organization();

        // 存在しない場合は作成する
        $startDate = new DateTime();
        $thisDate = new DateTime();
        $startDate->setDate($year,$month,1);
        $thisDate->setDate($year,$month,1);

        DB::beginTransaction();
        Log::info('create_data beginTransaction - start');
        try {
            while( $thisDate->format('n') == $startDate->format('n') ){

                // 存在チェック
                $count = DB::table('stuffattendances')
                            ->where('organization_id',$organization->id)
                            ->where('stuff_id',$stuff_id)
                            ->where('eventdate',$thisDate->format('Y-m-d'))
                            ->count();

                Log::debug('date = '. $thisDate->format('Y-m-d') . ', day of week = ' . $thisDate->format('w') . ', count = ' . $count);
                if( 0 == $count ){
                    $stuff = Stuff::find($stuff_id);

                    $holiday = $this->is_holiday($organization->id, $thisDate->format('Y-m-d'));
                    if( 0 == $thisDate->format('w') || 6 == $thisDate->format('w') || $holiday){
                        // 新規作成(土日祝は出勤/退勤時刻をセットしない)
                        StuffAttendance::insert(
                            [
                                'organization_id' => $organization->id
                                ,'stuff_id'       => $stuff_id
                                ,'eventdate'      => $thisDate->format('Y-m-d')
                                ,'created_at'     => date('Y-m-d H:i:s')
                                ,'updated_at'     => date('Y-m-d H:i:s')
                            ]
                        );
                    }
                    else{
                        // 新規作成
                        StuffAttendance::insert(
                            [
                                'organization_id' => $organization->id
                                ,'stuff_id'       => $stuff_id
                                ,'eventdate'      => $thisDate->format('Y-m-d')
                                ,'entrytime'      => $stuff->entrytime
                                ,'exittime'       => $stuff->exittime
                                ,'created_at'     => date('Y-m-d H:i:s')
                                ,'updated_at'     => date('Y-m-d H:i:s')
                            ]
                        );
                    }
                    Log::debug('created');
                }

                $thisDate->modify('+1 days');
            }
            DB::commit();
            Log::info('beginTransaction - end');
        }
        catch(\QueryException $e) {
            Log::error('exception : ' . $e->getMessage());
            DB::rollback();
            Log::info('beginTransaction - end(rollback)');
        }

        Log::info('END');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function input(Request $request)
    {
        Log::info('START');

        $organization = $this->auth_user_organization();
        $nowDate = new DateTime('now');

        //-------------------------------------------------------------
        //- Request パラメータ
        //-------------------------------------------------------------
        Log::debug('request_all = ' . print_r($request->all(),true));


        if( $request->has('requestData') ){
            $requestData = json_decode( $request->input('requestData') , true ) ;;
            $search_stuff_id  = $requestData['search_stuff_id'];
            $search_status_id   = $requestData['search_status_id'];
            $search_year        = $requestData['search_year'];
            $search_month       = $requestData['search_month'];
        }
        else{
            $search_stuff_id  = $request->input('search_stuff_id');
            $search_status_id   = $request->input('search_status_id');
            $search_year        = $request->input('search_year');
            $search_month       = $request->input('search_month');
        }

        if( is_null($search_stuff_id) ) $search_stuff_id = '0';
        if( is_null($search_status_id)  ) $search_status_id  = '0';
        if( is_null($search_year)       ) $search_year       = $nowDate->format('Y');
        if( is_null($search_month)      ) $search_month      = $nowDate->format('n');

        $submit_type = $request->input('submit_type');

        Log::debug('search_stuff_id = ' . $search_stuff_id);
        Log::debug('search_status_id  = ' . $search_status_id);
        Log::debug('search_year       = ' . $search_year);
        Log::debug('search_month      = ' . $search_month);
        Log::debug('submit_type       = ' . $submit_type);

        if( 0 == strcmp($submit_type,'create') && 0 < $search_stuff_id && 0 < $search_year && 0 < $search_month){
            $this->create_data($search_year,$search_month,$search_stuff_id);
        }
        if( 0 == strcmp($submit_type, 'save') && 0 < $search_stuff_id && 0 < $search_year && 0 < $search_month){
            $this->batch_save($request);
        }

        //-------------------------------------------------------------
        //- 検索プルダウンデータの取得
        //-------------------------------------------------------------
        //- 職員プルダウン
        $stuffs = Stuff::where('organization_id', $organization->id)
                            // 状態の絞り込み
                            ->when($search_status_id > 0, function ($query) use ($search_status_id) {
                                return $query->where('status',$search_status_id);
                            })
                            ->get();
        Log::debug('stuffs = ' . $stuffs);

        // 年プルダウン
        $years = $this->getYears($organization->id,true);

        //-------------------------------------------------------------
        //- 一覧データの取得
        //-------------------------------------------------------------
        $stuffattendances = StuffAttendance::select(
            'stuffattendances.id as id'
            , 'stuffs.id as stuff_id'
            , 'stuffs.first_name as first_name'
            , 'stuffs.last_name as last_name'
            , 'stuffattendances.care_type as care_type'
            , 'stuffattendances.eventdate as eventdate'
            , 'stuffattendances.entrytime as entrytime'
            , 'stuffattendances.exittime as exittime'
            , 'stuffattendances.breaktime as breaktime'
            , 'stuffattendances.worktime as worktime'
            , 'stuffattendances.status as status'
            , 'stuffattendances.comment as comment'
        )
        ->join('stuffs', function ($join) {
            $join->on('stuffs.organization_id', '=', 'stuffattendances.organization_id');
            $join->on('stuffs.id', '=', 'stuffattendances.stuff_id');
        })
        // 削除されていない
        ->whereNull('stuffattendances.deleted_at')
        ->whereNull('stuffs.deleted_at')
        // 所属会社の絞り込み
        ->where('stuffattendances.organization_id', $organization->id)
        // 職員の状態の絞り込み
        ->when($search_status_id > 0, function ($query) use ($search_status_id) {
            return $query->where('stuffs.status',$search_status_id);
        })
        // 職員の絞り込み
        ->where('stuffattendances.stuff_id', $search_stuff_id )
        // 年月の絞り込み
        ->whereYear('stuffattendances.eventdate', $search_year )
        ->whereMonth('stuffattendances.eventdate', $search_month )
        ->OrderBy('stuffattendances.id')
        ->paginate(31);

        $compacts = compact(   'search_stuff_id'
                            ,'search_status_id'
                            ,'search_year'
                            ,'search_month'
                            ,'stuffs'
                            ,'years'
                            ,'stuffattendances'
                            ,'organization'
        );

        Log::info('END');
        return view('stuff_attendance.input', $compacts );
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        Log::info('stuff-attend index START');

        //-------------------------------------------------------------
        //- Request パラメータ
        //-------------------------------------------------------------
        $frdate = $request->Input('frdate');
        $todate = $request->Input('todate');

        $organization  = $this->auth_user_organization();
        $organization_id = $organization->id;

        if($organization_id == 0) {
            $stuffattendances = StuffAttendance::select(
                'stuffattendances.id               as id'
                ,'stuffattendances.organization_id as organization_id'
                ,'stuffattendances.stuff_id        as stuffattend_id'
                ,'stuffattendances.eventdate       as eventdate'
                ,'stuffattendances.entrytime       as entrytime'
                ,'stuffattendances.exittime        as exittime'
                ,'stuffattendances.status          as status'
                ,'stuffs.id                        as stuff_id'
                ,'stuffs.last_name                 as last_name'
                ,'stuffs.first_name                as first_name'
                ,'stuffs.status                    as stuff_status'
            )
            ->leftJoin('stuffs', function ($join) {
                $join->on('stuffattendances.stuff_id', '=', 'stuffs.id');
            })
            ->where('stuffattendances.organization_id','>=',$organization_id)
            ->where('stuffs.status','=',1)    //1:在職中
            ->whereNull('stuffattendances.deleted_at')
            ->whereNull('stuffs.deleted_at')
            ->OrderBy('stuffattendances.id','asc')
            ->sortable()
            ->paginate(300);
        } else {
            $stuffattendances = StuffAttendance::select(
                'stuffattendances.id               as id'
                ,'stuffattendances.organization_id as organization_id'
                ,'stuffattendances.stuff_id        as stuffattend_id'
                ,'stuffattendances.eventdate       as eventdate'
                ,'stuffattendances.entrytime       as entrytime'
                ,'stuffattendances.exittime        as exittime'
                ,'stuffattendances.status          as status'
                ,'stuffs.id                        as stuff_id'
                ,'stuffs.last_name                 as last_name'
                ,'stuffs.first_name                as first_name'
                ,'stuffs.status                    as stuff_status'
            )
            ->leftJoin('stuffs', function ($join) {
                $join->on('stuffattendances.stuff_id', '=', 'stuffs.id');
            })
            ->where('stuffattendances.organization_id','=',$organization_id)
            ->where('stuffs.status','=',1)    //1:在職中
            ->whereNull('stuffattendances.deleted_at')
            ->whereNull('stuffs.deleted_at')
            ->OrderBy('stuffattendances.id','asc')
            ->sortable()
            ->paginate(300);
        }
        $common_no = '00_4';
        $compacts = compact( 'stuffattendances','common_no','frdate','todate' );
        Log::info('stuff-attend index  END');
        return view( 'stuffattendance.index', $compacts );

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        Log::info('stuff-attend create START');
        $organization = $this->auth_user_organization();
        $stuffs = Stuff::get();

        $common_no = '00_3';
        $compacts = compact( 'organization','stuffs','common_no' );
        Log::info('stuff-attend create END');

        return view('stuffattendance.create',$compacts);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Log::info('stuff-attend store START');

        $validator = $this->get_validator($request);
        if ($validator->fails()) {
            return redirect('stuff-attend/create')->withErrors($validator)->withInput();
        }

        $organization = $this->auth_user_organization();
        $request->merge( ['organization_id'=> $organization->id] );
        Log::debug('stuff-attend store $request = ' . print_r($request->all(), true));

        DB::beginTransaction();
        Log::info('stuff-attend store beginTransaction - start');
        try {
            // StuffAttendance::create($request->all());
            $stuffatd = new Stuffattendance();

            $stuffatd->organization_id  = 1;
            $stuffatd->stuff_id         = $request->stuff_id;
            $stuffatd->eventdate        = $request->eventdate;
            $stuffatd->entrytime        = $request->entrytime;
            $stuffatd->exittime         = $request->exittime;
            $stuffatd->care_type        = 1;  //1:一般 2:管理職
            $stuffatd->status           = $request->status;

            $stuffatd->save();         //  Inserts
            DB::commit();
            Log::info('stuff-attend store beginTransaction - end');
        }
        catch(\QueryException $e) {
            Log::error('stuff-attend store exception : ' . $e->getMessage());
            DB::rollback();
            Log::info('stuff-attend store beginTransaction - end(rollback)');
        }
        $common_no = '00_3';
        Log::info('stuff-attend store END');
        // toastrというキーでメッセージを格納
        session()->flash('toastr', config('toastr.create'));
        return redirect()->route('stuff-attend.index');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        Log::info('stuff-attend show START');
        Log::info('stuff-attend show END');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        Log::info('stuff-attend edit START');

        $organization = $this->auth_user_organization();
        $stuffattend  = StuffAttendance::find($id);
        // $stuff      = Stuff::find($stuattend->stuff_id);
        $stuffs = Stuff::get();

        // Log::debug('edit $stuattend->stuff_id = ' . $stuattend->stuff_id);

        $common_no = '00_4';
        $compacts = compact('stuffattend','stuffs','organization','common_no');
        Log::info('stuff-attend edit END');

        return view('stuffattendance.edit',$compacts);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        Log::info('stuff-attend update START');

        $validator = $this->get_validator($request);
        if ($validator->fails()) {
            return redirect('stuff-attend/'.$id.'/edit')->withErrors($validator)->withInput();
        }

        Log::debug('$entrytime = ' . $request->entrytime);
        Log::debug('$exittime  = ' . $request->exittime);

        $update = [
            'entrytime'  => $request->entrytime,
            'exittime'   => $request->exittime,
            'breaktime'  => null,
            'worktime'   => null,
            'status'     => $request->status,
            'comment'    => $request->comment,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        DB::beginTransaction();
        Log::info('stuff-attend update beginTransaction - start');
        try {
            StuffAttendance::where('id', $id)->update($update);
            DB::commit();
            Log::info('stuff-attend update beginTransaction - end');
        }
        catch(\QueryException $e) {
            Log::error('exception : ' . $e->getMessage());
            DB::rollback();
            Log::info('stuff-attend update beginTransaction - end(rollback)');
        }

        Log::info('stuff-attend update END');

        // toastrというキーでメッセージを格納
        session()->flash('toastr', config('toastr.update'));
        return redirect()->route('stuff-attend.index');

        // return redirect()->route('stuff-attend.index')->with('success', '編集完了');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Log::info('stuff-attend destroy START');

        DB::beginTransaction();
        Log::info('stuff-attend destroy beginTransaction - start');
        try {
            $stuffattendances = StuffAttendance::find($id);
            $stuffattendances->deleted_at     = now();
            $result = $stuffattendances->save();

            DB::commit();
            Log::info('stuff-attend destroy beginTransaction - end');
        }
        catch(\QueryException $e) {
            Log::error('exception : ' . $e->getMessage());
            DB::rollback();
            Log::info('stuff-attend destroy beginTransaction - end(rollback)');
        }

        Log::info('stuff-attend destroy END');
        // return redirect()->route('stuff-attend.index')->with('success', '削除完了');

        // toastrというキーでメッセージを格納
        session()->flash('toastr', config('toastr.delete'));
        return redirect()->route('stuff-attend.index');

    }


    /**
     * []studentattend csv出力
     */
    public function export(Request $request)
    {
        Log::info('export studentattend START');

        $now = Carbon::now();
        $str = $now->format('Ymd_Hi');
        $filename = '職員出勤履歴_'.$str.'.csv';

        $organization    = $this->auth_user_organization();
        $organization_id = $organization->id;


        $frdate      = $request->input('frdate');
        $todate      = $request->input('todate');

        // 開始/終了が入力された
        if(isset($frdate) && isset($todate)) {
            // $stadate   = ( new DateTime($frdate))->format($format);
            // $enddate   = ( new DateTime($todate))->format($format);
            $stadate    = Carbon::parse($frdate)->startOfDay();
            $enddate    = Carbon::parse($todate)->endOfDay();
        } else {
            if(isset($frdate)) {
                $stadate   = Carbon::parse($frdate)->startOfDay();
                $enddate   = Carbon::parse('2050-12-31')->endOfDay();
            } else {
                $stadate   = Carbon::parse('2000-01-01')->startOfDay();
                $enddate   = Carbon::parse($todate)->endOfDay();
            }
        }

        Log::debug('stuffattendances $frdate  =  ' . $frdate);
        Log::debug('stuffattendances $todate  =  ' . $todate);
        Log::debug('stuffattendances $stadate =  ' . $stadate);
        Log::debug('stuffattendances $enddate =  ' . $enddate);

        if($organization_id == 0) {
            // stuffattendancesを取得
            $stuffattendances = StuffAttendance::select(
                'stuffattendances.id                as id'
                ,'stuffattendances.organization_id  as organization_id'
                ,'stuffs.custom_no                  as custom_no'   //顧客No
                ,'stuffattendances.stuff_id         as stuffattd_id'
                ,'stuffattendances.eventdate        as eventdate'
                ,'stuffattendances.entrytime        as entrytime'
                ,'stuffattendances.exittime         as exittime'
                ,'stuffattendances.status           as status'		//出欠
                ,'stuffs.id                         as stuff_id'
                ,'stuffs.last_name                  as last_name'
                ,'stuffs.first_name                 as first_name'
                ,'stuffs.status                     as stuff_status'			//1:在職中,2:休会,3:退会'
                ,'stuffs.care_type                  as care_type'	//1:一般,2:管理職,3:臨時,4:バイト
                )
                ->leftJoin('stuffs', function ($join) {
                    $join->on('stuffattendances.stuff_id', '=', 'stuffs.id');
                })
                ->where('stuffattendances.organization_id','>=',$organization_id)
                ->where('stuffs.status','=',1)    //1:在職中
                ->whereNull('stuffattendances.deleted_at')
                ->whereNull('stuffs.deleted_at')

                //eventdateが20xx/xx/xx ~ 20xx/xx/xxのデータを取得
                ->whereBetween("eventdate", [$stadate, $enddate])
                ->orderByRaw('id asc')
                ->get();
        } else {
            // stuffattendancesを取得
            $stuffattendances = StuffAttendance::select(
                 'stuffattendances.id              as id'
                ,'stuffattendances.organization_id as organization_id'
                ,'stuffs.custom_no                 as custom_no'   //顧客No
                ,'stuffattendances.stuff_id        as stuffattd_id'
                ,'stuffattendances.eventdate       as eventdate'
                ,'stuffattendances.entrytime       as entrytime'
                ,'stuffattendances.exittime        as exittime'
                ,'stuffattendances.status          as status'		//出欠
                ,'stuffs.id                        as stuff_id'
                ,'stuffs.last_name                 as last_name'
                ,'stuffs.first_name                as first_name'
                ,'stuffs.status                    as stuff_status'	//1:在職中,2:休会,3:退会'
                ,'stuffs.care_type                 as care_type'	//1:一般,2:管理職,3:臨時,4:バイト
                )
                ->leftJoin('stuffs', function ($join) {
                    $join->on('stuffattendances.stuff_id', '=', 'stuffs.id');
                })
                ->where('stuffattendances.organization_id','=',$organization_id)
                ->where('stuffs.status','=',1)    //1:在職中
                ->whereNull('stuffattendances.deleted_at')
                ->whereNull('stuffs.deleted_at')

                //eventdateが20xx/xx/xx ~ 20xx/xx/xxのデータを取得
                ->whereBetween("eventdate", [$stadate, $enddate])
                ->orderByRaw('id asc')
                ->get();
        }
        // Log::debug('stuffattendances $->count() = ' . print_r($stuffattendances->count(), true));

        //-------------------------------------------------
        //- DataCheck 0=対象データがありません
        //-------------------------------------------------
        if( $stuffattendances->count() <= 0 ) {
            session()->flash('toastr', config('toastr.csv_warning'));
            return redirect()->route('stuff-attend.index');
        }

        //-------------------------------------------------
        //- CSV生成
        //-------------------------------------------------
        $response = new StreamedResponse (function() use ( $stuffattendances, $frdate, $todate ){

            //-------------------------------------------------
            //- CSVにするデータ収集
            //-------------------------------------------------
            $ret_val = $this->getListData( $stuffattendances, $frdate, $todate );

            // Log::debug('stuffattendances getListData $ret_val = ' . print_r($ret_val, true));

            $custm_list    = $ret_val['custm_list'];

            $stream = fopen('php://output', 'w');

            // 文字化け回避
            stream_filter_prepend($stream,'convert.iconv.utf-8/cp932//TRANSLIT');

            // タイトルを追加
            fputcsv($stream,
                            [
                                 'No'                 //text
                                ,'職員氏名'           //text
                                ,'職種'               //text
                                ,'出勤日'             //date
                                ,'出／欠'             //for
                                ,'開始'               //time
                                ,'終了'               //time
                            ]
                        );

            foreach($custm_list as $custm_){

                $rec = array();
                array_push($rec, $custm_['custom_no'] );     // No
                array_push($rec, $custm_['stu_name'] );      // 職員氏名
                array_push($rec, $custm_['care_type'] );     // 職種
                array_push($rec, $custm_['eventdate'] );     // 出勤日
                array_push($rec, $custm_['stuff_status'] );  // 出／欠
                array_push($rec, $custm_['entrytime'] );     // 開始
                array_push($rec, $custm_['exittime'] );      // 終了

                fputcsv($stream, $rec);
            }

            fclose($stream);
        });

        $response->headers->set('Content-Type', 'application/octet-stream');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

        Log::info('export studentattend END');
        return $response;
    }

    /**
     * List Data の取得
     */
    public function getListData($stuffattendances, $frdate, $todate )
    {
        Log::info('getListData stuffattendances START');

        $custm_list = array();
        $custm_rec  = array();
        $ret_val    = array();

        //---------------------------------------------------------------
        //- 返却データの整形
        //---------------------------------------------------------------
        foreach($stuffattendances as $stuffattend) {

            // 現在の配列を大本に追加
            if (0 < count($custm_rec)) {
                array_push($custm_list, $custm_rec);
            }
            // {{-- No --}}
            $custm_rec['custom_no']     = sprintf('%s', $stuffattend->custom_no);

            // {{-- 職員氏名 --}}
            $custm_rec['stu_name']      = $stuffattend->last_name .' '.$stuffattend->first_name;

            // {{-- 出勤日 --}}
            $str = "";
            if (isset($stuffattend->eventdate)) {
                $str = ( new DateTime($stuffattend->eventdate))->format('Y-m-d');
            }
            $custm_rec['eventdate']        = $str;

            // {{-- 職種～ --}}
            switch($stuffattend->care_type) {
                case (1): $custm_rec['care_type']    = "一般";
                    break;
                case (2): $custm_rec['care_type']    = "管理職";
                    break;
                case (3): $custm_rec['care_type']    = "臨時";
                    break;
                case (4): $custm_rec['care_type']    = "バイト";
                    break;
                default:  $custm_rec['empl_type']    = "該当無";
                    break;
            }

            // {{-- 出／欠 1～ --}}
            switch($stuffattend->status) {
                case (1): $custm_rec['stuff_status']   = "出勤";
                    break;
                case (2): $custm_rec['stuff_status']   = "欠勤";
                    break;
                default:  $custm_rec['stuff_status']   = "";
                    break;
            }

            // {{-- 開始 --}}
            $str = "";
            if (isset($stuffattend->entrytime)) {
                $str = ( new DateTime($stuffattend->entrytime))->format('H:i');
            }
            $custm_rec['entrytime']      = $str;

            // {{-- 終了 --}}
            $str = "";
            if (isset($stuffattend->exittime)) {
                $str = ( new DateTime($stuffattend->exittime))->format('H:i');
            }
            $custm_rec['exittime']      = $str;

        }
        array_push($custm_list, $custm_rec);

        // Footer
        if (0 < count($custm_rec)) {
            // {{-- //開始年月日 --}}
            $str = "指定無し";
            if (isset($frdate)) {
                $str = ( new DateTime($frdate))->format('Y-m-d');
            }
            $custm_rec['custom_no']     = "処理年月日(開始)";
            $custm_rec['stu_name']      = $str;

            // {{-- //終了年月日 --}}
            $str = "指定無し";
            if (isset($todate)) {
                $str = ( new DateTime($todate))->format('Y-m-d');
            }
            $custm_rec['empl_type']     = "処理年月日(終了)";
            $custm_rec['eventdate']     = $str;

            $custm_rec['stuff_status']    = "";
            $custm_rec['entrytime']     = "";
            $custm_rec['exittime']      = "";

            array_push($custm_list, $custm_rec);
        }

        $ret_val['custm_list']    = $custm_list;
        Log::info('getListData studentattend END');
        return $ret_val;
    }


    /**
     * [webapi]クラス一覧テーブルに格納されている最新年のクラス一覧を取得する
     */
    public function getstuffs(Request $request)
    {
        Log::info('getstuffs START');

        $organization = $this->auth_user_organization();

        $status = $request->search_status_id;

        $stuffs = Stuff::whereNull('deleted_at')
                            ->where('organization_id', $organization->id)
                            // 状態の絞り込み
                            ->when($status > 0, function ($query) use ($status) {
                                return $query->where('status',$status);
                            })
                            ->get();

        Log::debug('retval = ' . print_r($stuffs,true));
        Log::info('getstuffs END');
        return response()->json([ compact('stuffs') ]);
    }

    /**
     * 内部メソッド
     */
    function getYears( $organization_id, $is_to_now_year = false)
    {
        Log::info('START');

        $query = '';
        $query .= 'SELECT DATE_FORMAT(eventdate,"%Y") as year ';
        $query .= 'FROM stuffattendances ';
        $query .= 'WHERE deleted_at is NULL AND organization_id = %organization_id% ';
        $query .= 'GROUP BY DATE_FORMAT(eventdate,"%Y") ';
        $query .= 'ORDER BY DATE_FORMAT(eventdate,"%Y") ';
        $query = str_replace('%organization_id%',$organization_id,$query);
        Log::debug('$query = ' . print_r($query,true));

        $years = DB::select($query);

        if( $is_to_now_year ){
            // 最小年～現在までの年を追加する
            $min_year = date('Y');
            $now_year = date('Y');
            if( !is_null($years) && !empty($years)){
                $min_year = $years[0]->year;
            }

            $this_year = $min_year ;
            $years = array();
            while( $this_year <= $now_year ){
                $obj = new \stdClass;
                $obj->year = $this_year;
                array_push($years, $obj );
                $this_year++;
            }
        }

        Log::debug('retval = ' . print_r($years,true));
        Log::info('END');
        return $years;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function serch_stdattend(Request $request)
    {
        Log::info('stuff-attend serch START');

        //-------------------------------------------------------------
        //- Request パラメータ
        //-------------------------------------------------------------
        $frdate = $request->Input('frdate');
        $todate = $request->Input('frdate');

        $organization  = $this->auth_user_organization();
        $organization_id = $organization->id;

        Log::debug('serch_stdattend $frdate  =  ' . $frdate);

        // 日付が入力された
        if(isset($frdate)) {
            $stadate   = Carbon::parse($frdate)->startOfDay();

            if($organization_id == 0) {
                $stuffattendances = StuffAttendance::select(
                    'stuffattendances.id               as id'
                    ,'stuffattendances.organization_id as organization_id'
                    ,'stuffattendances.stuff_id        as stuffattend_id'
                    ,'stuffattendances.eventdate       as eventdate'
                    ,'stuffattendances.entrytime       as entrytime'
                    ,'stuffattendances.exittime        as exittime'
                    ,'stuffattendances.status          as status'
                    ,'stuffs.id                        as stuff_id'
                    ,'stuffs.last_name                 as last_name'
                    ,'stuffs.first_name                as first_name'
                    ,'stuffs.status                    as stuff_status'
                    )
                    ->leftJoin('stuffs', function ($join) {
                        $join->on('stuffattendances.stuff_id', '=', 'stuffs.id');
                    })
                    ->where('stuffattendances.organization_id','>=',$organization_id)
                    ->where('stuffs.status','=',1)    //1:在職中
                    ->whereBetween("eventdate", [$stadate, $stadate])
                    ->whereNull('stuffattendances.deleted_at')
                    ->whereNull('stuffs.deleted_at')
                    ->sortable()
                    ->paginate(10);;
            } else {
                $stuffattendances = StuffAttendance::select(
                    'stuffattendances.id               as id'
                    ,'stuffattendances.organization_id as organization_id'
                    ,'stuffattendances.stuff_id        as stuffattend_id'
                    ,'stuffattendances.eventdate       as eventdate'
                    ,'stuffattendances.entrytime       as entrytime'
                    ,'stuffattendances.exittime        as exittime'
                    ,'stuffattendances.status          as status'
                    ,'stuffs.id                        as stuff_id'
                    ,'stuffs.last_name                 as last_name'
                    ,'stuffs.first_name                as first_name'
                    ,'stuffs.status                    as stuff_status'
                )
                ->leftJoin('stuffs', function ($join) {
                    $join->on('stuffattendances.stuff_id', '=', 'stuffs.id');
                })
                ->where('stuffattendances.organization_id','=',$organization_id)
                ->where('stuffs.status','=',1)    //1:在職中
                ->whereBetween("eventdate", [$stadate, $stadate])
                ->whereNull('stuffattendances.deleted_at')
                ->whereNull('stuffs.deleted_at')
                ->sortable()
                ->paginate(10);;
            }
        //日付が入力されない
        } else {
            if($organization_id == 0) {
                $stuffattendances = StuffAttendance::select(
                    'stuffattendances.id               as id'
                    ,'stuffattendances.organization_id as organization_id'
                    ,'stuffattendances.stuff_id        as stuffattend_id'
                    ,'stuffattendances.eventdate       as eventdate'
                    ,'stuffattendances.entrytime       as entrytime'
                    ,'stuffattendances.exittime        as exittime'
                    ,'stuffattendances.status          as status'
                    ,'stuffs.id                        as stuff_id'
                    ,'stuffs.last_name                 as last_name'
                    ,'stuffs.first_name                as first_name'
                    ,'stuffs.status                    as stuff_status'
                    )
                    ->leftJoin('stuffs', function ($join) {
                        $join->on('stuffattendances.stuff_id', '=', 'stuffs.id');
                    })
                    ->where('stuffattendances.organization_id','>=',$organization_id)
                    ->where('stuffs.status','=',1)    //1:在職中
                    ->whereNull('stuffattendances.deleted_at')
                    ->whereNull('stuffs.deleted_at')
                    ->sortable()
                    ->paginate(10);;
            } else {
                $stuffattendances = StuffAttendance::select(
                    'stuffattendances.id               as id'
                    ,'stuffattendances.organization_id as organization_id'
                    ,'stuffattendances.stuff_id        as stuffattend_id'
                    ,'stuffattendances.eventdate       as eventdate'
                    ,'stuffattendances.entrytime       as entrytime'
                    ,'stuffattendances.exittime        as exittime'
                    ,'stuffattendances.status          as status'
                    ,'stuffs.id                        as stuff_id'
                    ,'stuffs.last_name                 as last_name'
                    ,'stuffs.first_name                as first_name'
                    ,'stuffs.status                    as stuff_status'
                )
                ->leftJoin('stuffs', function ($join) {
                    $join->on('stuffattendances.stuff_id', '=', 'stuffs.id');
                })
                ->where('stuffattendances.organization_id','=',$organization_id)
                ->where('stuffs.status','=',1)    //1:在職中
                ->whereNull('stuffattendances.deleted_at')
                ->whereNull('stuffs.deleted_at')
                ->sortable()
                ->paginate(10);;
            }
        }

        $common_no = '00_4';
        $compacts = compact( 'stuffattendances','common_no','frdate','todate' );

        // $ret = $this->export($request);

        Log::info('stuff-attend serch  END');
        return view( 'stuffattendance.index', $compacts );

    }

    /**
     *
     */
    public function get_validator(Request $request)
    {
        Log::debug('stuff-attend get_validator $request->all() = ' . print_r($request->all(),true) );

        $rules   = [
                    'eventdate'       => 'required',
                    'entrytime'       => 'required',
                    'exittime'        => 'required',
                ];

        $messages = [
                    'eventdate.required'       => '登校日は入力必須項目です。',
                    'entrytime.required'       => '開始時間は入力必須項目です。',
                    'exittime.required'        => '終了時間は入力必須項目です。',
                    ];

        $validator = Validator::make($request->all(), $rules, $messages);

        return $validator;
    }
}
