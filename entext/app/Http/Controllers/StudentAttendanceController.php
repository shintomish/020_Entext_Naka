<?php

namespace App\Http\Controllers;

use Validator;
use DateTime;
use Carbon\Carbon;

use App\Models\Student;
use App\Models\StudentAttendance;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StudentAttendanceController extends Controller

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
        Log::info('StudentAttendances batch_save START');

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

        Log::debug('StudentAttendances batch_save Sid:' . print_r($id,true));

        if($id == null){
            Log::debug('id:null');

        }

        if (!is_null($id)) {
            DB::beginTransaction();
            Log::info('StudentAttendances batch_save beginTransaction - start');
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
                    StudentAttendance::where('id', $id[$i])->update($update);
                    Log::debug('StudentAttendances batch_save update:' . print_r($update, true));
                }
                DB::commit();
                Log::info('StudentAttendances batch_save beginTransaction - end');
            }
            catch(\QueryException $e) {
                Log::error('StudentAttendances batch_save exception : ' . $e->getMessage());
                DB::rollback();
                Log::info('StudentAttendances batch_save beginTransaction - end(rollback)');
            }
        }

        Log::info('StudentAttendances batch_save END');
    }

    /**
     *
     */
    function create_data( $year, $month, $student_id )
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
                $count = DB::table('studentattendances')
                            ->where('organization_id',$organization->id)
                            ->where('student_id',$student_id)
                            ->where('eventdate',$thisDate->format('Y-m-d'))
                            ->count();

                Log::debug('date = '. $thisDate->format('Y-m-d') . ', day of week = ' . $thisDate->format('w') . ', count = ' . $count);
                if( 0 == $count ){
                    $student = Student::find($student_id);

                    $holiday = $this->is_holiday($organization->id, $thisDate->format('Y-m-d'));
                    if( 0 == $thisDate->format('w') || 6 == $thisDate->format('w') || $holiday){
                        // 新規作成(土日祝は出勤/退勤時刻をセットしない)
                        StudentAttendance::insert(
                            [
                                'organization_id' => $organization->id
                                ,'student_id'     => $student_id
                                ,'eventdate'      => $thisDate->format('Y-m-d')
                                ,'created_at'     => date('Y-m-d H:i:s')
                                ,'updated_at'     => date('Y-m-d H:i:s')
                            ]
                        );
                    }
                    else{
                        // 新規作成
                        StudentAttendance::insert(
                            [
                                'organization_id' => $organization->id
                                ,'student_id'     => $student_id
                                ,'eventdate'      => $thisDate->format('Y-m-d')
                                ,'entrytime'      => $student->entrytime
                                ,'exittime'       => $student->exittime
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
            $search_student_id  = $requestData['search_student_id'];
            $search_status_id   = $requestData['search_status_id'];
            $search_year        = $requestData['search_year'];
            $search_month       = $requestData['search_month'];
        }
        else{
            $search_student_id  = $request->input('search_student_id');
            $search_status_id   = $request->input('search_status_id');
            $search_year        = $request->input('search_year');
            $search_month       = $request->input('search_month');
        }

        if( is_null($search_student_id) ) $search_student_id = '0';
        if( is_null($search_status_id)  ) $search_status_id  = '0';
        if( is_null($search_year)       ) $search_year       = $nowDate->format('Y');
        if( is_null($search_month)      ) $search_month      = $nowDate->format('n');

        $submit_type = $request->input('submit_type');

        Log::debug('search_student_id = ' . $search_student_id);
        Log::debug('search_status_id  = ' . $search_status_id);
        Log::debug('search_year       = ' . $search_year);
        Log::debug('search_month      = ' . $search_month);
        Log::debug('submit_type       = ' . $submit_type);

        if( 0 == strcmp($submit_type,'create') && 0 < $search_student_id && 0 < $search_year && 0 < $search_month){
            $this->create_data($search_year,$search_month,$search_student_id);
        }
        if( 0 == strcmp($submit_type, 'save') && 0 < $search_student_id && 0 < $search_year && 0 < $search_month){
            $this->batch_save($request);
        }

        //-------------------------------------------------------------
        //- 検索プルダウンデータの取得
        //-------------------------------------------------------------
        //- 生徒プルダウン
        $students = Student::where('organization_id', $organization->id)
                            // 状態の絞り込み
                            ->when($search_status_id > 0, function ($query) use ($search_status_id) {
                                return $query->where('status',$search_status_id);
                            })
                            ->get();
        Log::debug('students = ' . $students);

        // 年プルダウン
        $years = $this->getYears($organization->id,true);

        //-------------------------------------------------------------
        //- 一覧データの取得
        //-------------------------------------------------------------
        $studentattendances = StudentAttendance::select(
              'studentattendances.id as id'
            , 'students.id as student_id'
            , 'students.first_name as first_name'
            , 'students.last_name as last_name'
            , 'studentattendances.care_type as care_type'
            , 'studentattendances.eventdate as eventdate'
            , 'studentattendances.entrytime as entrytime'
            , 'studentattendances.exittime as exittime'
            , 'studentattendances.breaktime as breaktime'
            , 'studentattendances.worktime as worktime'
            , 'studentattendances.status as status'
            , 'studentattendances.comment as comment'
        )
        ->join('students', function ($join) {
            $join->on('students.organization_id', '=', 'studentattendances.organization_id');
            $join->on('students.id', '=', 'studentattendances.student_id');
        })
        // 削除されていない
        ->whereNull('studentattendances.deleted_at')
        ->whereNull('students.deleted_at')
        // 所属会社の絞り込み
        ->where('studentattendances.organization_id', $organization->id)
        // 生徒の状態の絞り込み
        ->when($search_status_id > 0, function ($query) use ($search_status_id) {
            return $query->where('students.status',$search_status_id);
        })
        // 生徒の絞り込み
        ->where('studentattendances.student_id', $search_student_id )
        // 年月の絞り込み
        ->whereYear('studentattendances.eventdate', $search_year )
        ->whereMonth('studentattendances.eventdate', $search_month )
        ->OrderBy('studentattendances.id')
        ->paginate(31);

      $compacts = compact(   'search_student_id'
                            ,'search_status_id'
                            ,'search_year'
                            ,'search_month'
                            ,'students'
                            ,'years'
                            ,'studentattendances'
                            ,'organization'
        );

        Log::info('END');
        return view('student_attendance.input', $compacts );
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        Log::info('stu-attend index START');

        //-------------------------------------------------------------
        //- Request パラメータ
        //-------------------------------------------------------------
        $frdate = $request->Input('frdate');
        $todate = $request->Input('todate');

        $organization  = $this->auth_user_organization();
        $organization_id = $organization->id;

        if($organization_id == 0) {
            $studentattendances = Studentattendance::select(
                'studentattendances.id               as id'
                ,'studentattendances.organization_id as organization_id'
                ,'studentattendances.student_id      as student_id'
                ,'studentattendances.eventdate       as eventdate'
                ,'studentattendances.entrytime       as entrytime'
                ,'studentattendances.exittime        as exittime'
                ,'studentattendances.status          as status'
                ,'students.id                        as stu_id'
                ,'students.last_name                 as last_name'
                ,'students.first_name                as first_name'
                ,'students.status                    as stu_status'
            )
            ->leftJoin('students', function ($join) {
                $join->on('studentattendances.student_id', '=', 'students.id');
            })
            ->where('studentattendances.organization_id','>=',$organization_id)
            ->where('students.status','=',1)    //1:入会中
            ->whereNull('studentattendances.deleted_at')
            ->whereNull('students.deleted_at')
            ->OrderBy('studentattendances.id','asc')
            ->sortable()
            ->paginate(300);
        } else {
            $studentattendances = Studentattendance::select(
                'studentattendances.id               as id'
                ,'studentattendances.organization_id as organization_id'
                ,'studentattendances.student_id      as student_id'
                ,'studentattendances.eventdate       as eventdate'
                ,'studentattendances.entrytime       as entrytime'
                ,'studentattendances.exittime        as exittime'
                ,'studentattendances.status          as status'
                ,'students.id                        as stu_id'
                ,'students.last_name                 as last_name'
                ,'students.first_name                as first_name'
                ,'students.status                    as stu_status'
            )
            ->leftJoin('students', function ($join) {
                $join->on('studentattendances.student_id', '=', 'students.id');
            })
            ->where('studentattendances.organization_id','=',$organization_id)
            ->where('students.status','=',1)    //1:入会中
            ->whereNull('studentattendances.deleted_at')
            ->whereNull('students.deleted_at')
            ->OrderBy('studentattendances.id','asc')
            ->sortable()
            ->paginate(300);
        }
        $common_no = '00_3';
        $compacts = compact( 'studentattendances','common_no','frdate','todate' );
        Log::info('stu-attend index  END');
        return view( 'studentattendance.index', $compacts );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        Log::info('stu-attend create START');
        $organization = $this->auth_user_organization();
        $students = Student::get();

        $common_no = '00_3';
        $compacts = compact( 'organization','students','common_no' );
        Log::info('stu-attend create END');

        return view('studentattendance.create',$compacts);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Log::info('stu-attend store START');

        $validator = $this->get_validator($request);
        if ($validator->fails()) {
            return redirect('stu-attend/create')->withErrors($validator)->withInput();
        }

        $organization = $this->auth_user_organization();
        $request->merge( ['organization_id'=> $organization->id] );
        Log::debug('stu-attend store $request = ' . print_r($request->all(), true));

        DB::beginTransaction();
        Log::info('stu-attend store beginTransaction - start');
        try {
            // StudentAttendance::create($request->all());
            $studentatd = new StudentAttendance();

            $studentatd->organization_id  = 1;
            $studentatd->student_id       = $request->student_id;
            $studentatd->eventdate        = $request->eventdate;
            $studentatd->entrytime        = $request->entrytime;
            $studentatd->exittime         = $request->exittime;
            $studentatd->care_type        = 1;  //1:KIS 2:BUIS
            $studentatd->status           = $request->status;

            $studentatd->save();         //  Inserts
            DB::commit();
            Log::info('stu-attend store beginTransaction - end');
        }
        catch(\QueryException $e) {
            Log::error('stu-attend store exception : ' . $e->getMessage());
            DB::rollback();
            Log::info('stu-attend store beginTransaction - end(rollback)');
        }
        $common_no = '00_3';
        Log::info('stu-attend store END');
        // toastrというキーでメッセージを格納
        session()->flash('toastr', config('toastr.create'));
        return redirect()->route('stu-attend.index');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        Log::info('stu-attend show START');
        Log::info('stu-attend show END');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        Log::info('stu-attend edit START');

        $organization = $this->auth_user_organization();
        $stuattend    = StudentAttendance::find($id);
        // $student      = Student::find($stuattend->student_id);
        $students = Student::get();

        // Log::debug('edit $stuattend->student_id = ' . $stuattend->student_id);

        $common_no = '00_4';
        $compacts = compact('stuattend','students','organization','common_no');
        Log::info('stu-attend edit END');

        return view('studentattendance.edit',$compacts);

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
        Log::info('stu-attend update START');

        $validator = $this->get_validator($request);
        if ($validator->fails()) {
            return redirect('stu-attend/'.$id.'/edit')->withErrors($validator)->withInput();
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
        Log::info('stu-attend update beginTransaction - start');
        try {
            StudentAttendance::where('id', $id)->update($update);
            DB::commit();
            Log::info('stu-attend update beginTransaction - end');
        }
        catch(\QueryException $e) {
            Log::error('exception : ' . $e->getMessage());
            DB::rollback();
            Log::info('stu-attend update beginTransaction - end(rollback)');
        }

        Log::info('stu-attend update END');

        // toastrというキーでメッセージを格納
        session()->flash('toastr', config('toastr.update'));
        return redirect()->route('stu-attend.index');

        // return redirect()->route('stu-attend.index')->with('success', '編集完了');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Log::info('stu-attend destroy START');

        DB::beginTransaction();
        Log::info('stu-attend destroy beginTransaction - start');
        try {
            $studentAttendances = StudentAttendance::find($id);
            $studentAttendances->deleted_at     = now();
            $result = $studentAttendances->save();

            DB::commit();
            Log::info('stu-attend destroy beginTransaction - end');
        }
        catch(\QueryException $e) {
            Log::error('exception : ' . $e->getMessage());
            DB::rollback();
            Log::info('stu-attend destroy beginTransaction - end(rollback)');
        }

        Log::info('stu-attend destroy END');
        // return redirect()->route('stu-attend.index')->with('success', '削除完了');

        // toastrというキーでメッセージを格納
        session()->flash('toastr', config('toastr.delete'));
        return redirect()->route('stu-attend.index');

    }


    /**
     * []studentattend csv出力
     */
    public function export(Request $request)
    {
        Log::info('export studentattend START');

        $now = Carbon::now();
        $str = $now->format('Ymd_Hi');
        $filename = '生徒出席履歴_'.$str.'.csv';

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

        Log::debug('studentattendances $frdate  =  ' . $frdate);
        Log::debug('studentattendances $todate  =  ' . $todate);
        Log::debug('studentattendances $stadate =  ' . $stadate);
        Log::debug('studentattendances $enddate =  ' . $enddate);

        if($organization_id == 0) {
            // studentattendancesを取得
            $studentattendances = Studentattendance::select(
                'studentattendances.id              as id'
                ,'studentattendances.organization_id as organization_id'
                ,'students.custom_no                 as custom_no'   //顧客No
                ,'studentattendances.student_id      as student_id'
                ,'studentattendances.eventdate       as eventdate'
                ,'studentattendances.entrytime       as entrytime'
                ,'studentattendances.exittime        as exittime'
                ,'studentattendances.status          as status'		//出欠
                ,'students.id                        as stu_id'
                ,'students.last_name                 as last_name'
                ,'students.first_name                as first_name'
                ,'students.status                    as stu_status'			//1:入会中,2:休会,3:退会'
                ,'students.employment_type           as employment_type'	//100:幼稚園,210-260:小学,310-330:中学,410-430:高校,510:大学,600:社会人,700:無職

                )
                ->leftJoin('students', function ($join) {
                    $join->on('studentattendances.student_id', '=', 'students.id');
                })
                ->where('studentattendances.organization_id','>=',$organization_id)
                ->where('students.status','=',1)    //1:入会中
                ->whereNull('studentattendances.deleted_at')
                ->whereNull('students.deleted_at')

                //eventdateが20xx/xx/xx ~ 20xx/xx/xxのデータを取得
                ->whereBetween("eventdate", [$stadate, $enddate])
                ->orderByRaw('id asc')
                ->get();
        } else {
            // studentattendancesを取得
            $studentattendances = Studentattendance::select(
                 'studentattendances.id              as id'
                ,'studentattendances.organization_id as organization_id'
                ,'students.custom_no                 as custom_no'   //顧客No
                ,'studentattendances.student_id      as student_id'
                ,'studentattendances.eventdate       as eventdate'
                ,'studentattendances.entrytime       as entrytime'
                ,'studentattendances.exittime        as exittime'
                ,'studentattendances.status          as status'		//出欠
                ,'students.id                        as stu_id'
                ,'students.last_name                 as last_name'
                ,'students.first_name                as first_name'
                ,'students.status                    as stu_status'			//1:入会中,2:休会,3:退会'
                ,'students.employment_type           as employment_type'	//100:幼稚園,210-260:小学,310-330:中学,410-430:高校,510:大学,600:社会人,700:無職
                )
                ->leftJoin('students', function ($join) {
                    $join->on('studentattendances.student_id', '=', 'students.id');
                })
                ->where('studentattendances.organization_id','=',$organization_id)
                ->where('students.status','=',1)    //1:入会中
                ->whereNull('studentattendances.deleted_at')
                ->whereNull('students.deleted_at')

                //eventdateが20xx/xx/xx ~ 20xx/xx/xxのデータを取得
                ->whereBetween("eventdate", [$stadate, $enddate])
                ->orderByRaw('id asc')
                ->get();
        }
        // Log::debug('studentattendances $->count() = ' . print_r($studentattendances->count(), true));

        //-------------------------------------------------
        //- DataCheck 0=対象データがありません
        //-------------------------------------------------
        if( $studentattendances->count() <= 0 ) {
            session()->flash('toastr', config('toastr.csv_warning'));
            return redirect()->route('stu-attend.index');
        }

        //-------------------------------------------------
        //- CSV生成
        //-------------------------------------------------
        $response = new StreamedResponse (function() use ( $studentattendances, $frdate, $todate ){

            //-------------------------------------------------
            //- CSVにするデータ収集
            //-------------------------------------------------
            $ret_val = $this->getListData( $studentattendances, $frdate, $todate );

            // Log::debug('studentattendances getListData $ret_val = ' . print_r($ret_val, true));

            $custm_list    = $ret_val['custm_list'];

            $stream = fopen('php://output', 'w');

            // 文字化け回避
            stream_filter_prepend($stream,'convert.iconv.utf-8/cp932//TRANSLIT');

            // タイトルを追加
            fputcsv($stream,
                            [
                                 'No'                 //text
                                ,'生徒氏名'           //text
                                ,'所属'               //text
                                ,'登校日'             //date
                                ,'出／欠'             //for
                                ,'開始'               //time
                                ,'終了'               //time
                            ]
                        );

            foreach($custm_list as $custm_){

                $rec = array();
                array_push($rec, $custm_['custom_no'] );     // No
                array_push($rec, $custm_['stu_name'] );      // 生徒氏名
                array_push($rec, $custm_['empl_type'] );     // 所属
                array_push($rec, $custm_['eventdate'] );     // 登校日
                array_push($rec, $custm_['stu_status'] );    // 出／欠
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
    public function getListData($studentattendances, $frdate, $todate )
    {
        Log::info('getListData studentattendances START');

        $custm_list = array();
        $custm_rec  = array();
        $ret_val    = array();

        //---------------------------------------------------------------
        //- 返却データの整形
        //---------------------------------------------------------------
        foreach($studentattendances as $studentattend) {

            // 現在の配列を大本に追加
            if (0 < count($custm_rec)) {
                array_push($custm_list, $custm_rec);
            }
            // {{-- No --}}
            $custm_rec['custom_no']     = sprintf('%s', $studentattend->custom_no);

            // {{-- 生徒氏名 --}}
            $custm_rec['stu_name']      = $studentattend->last_name .' '.$studentattend->first_name;

            // {{-- 登校日 --}}
            $str = "";
            if (isset($studentattend->eventdate)) {
                $str = ( new DateTime($studentattend->eventdate))->format('Y-m-d');
            }
            $custm_rec['eventdate']        = $str;

            // {{-- 所属～ --}}
            switch($studentattend->employment_type) {
                case (100): $custm_rec['empl_type']    = "幼稚園";
                    break;
                case (210): $custm_rec['empl_type']    = "小学１";
                    break;
                case (220): $custm_rec['empl_type']    = "小学２";
                    break;
                case (230): $custm_rec['empl_type']    = "小学３";
                    break;
                case (240): $custm_rec['empl_type']    = "小学４";
                    break;
                case (250): $custm_rec['empl_type']    = "小学５";
                    break;
                case (260): $custm_rec['empl_type']    = "小学６";
                    break;
                case (310): $custm_rec['empl_type']    = "中学１";
                    break;
                case (320): $custm_rec['empl_type']    = "中学２";
                    break;
                case (330): $custm_rec['empl_type']    = "中学３";
                    break;
                case (410): $custm_rec['empl_type']    = "高校１";
                    break;
                case (420): $custm_rec['empl_type']    = "高校２";
                    break;
                case (430): $custm_rec['empl_type']    = "高校３";
                    break;
                case (510): $custm_rec['empl_type']    = "大学生";
                    break;
                case (600): $custm_rec['empl_type']    = "社会人";
                    break;
                case (700): $custm_rec['empl_type']    = "無　職";
                    break;
                default:  $custm_rec['empl_type']      = "該当無";
                    break;
            }

            // {{-- 出／欠 1～ --}}
            switch($studentattend->status) {
                case (1): $custm_rec['stu_status']   = "出席";
                    break;
                case (2): $custm_rec['stu_status']   = "欠席";
                    break;
                default:  $custm_rec['stu_status']   = "";
                    break;
            }

            // {{-- 開始 --}}
            $str = "";
            if (isset($studentattend->entrytime)) {
                $str = ( new DateTime($studentattend->entrytime))->format('H:i');
            }
            $custm_rec['entrytime']      = $str;

            // {{-- 終了 --}}
            $str = "";
            if (isset($studentattend->exittime)) {
                $str = ( new DateTime($studentattend->exittime))->format('H:i');
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

            $custm_rec['stu_status']    = "";
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
    public function getstudents(Request $request)
    {
        Log::info('START');

        $organization = $this->auth_user_organization();

        $status = $request->search_status_id;

        $students = Student::whereNull('deleted_at')
                            ->where('organization_id', $organization->id)
                            // 状態の絞り込み
                            ->when($status > 0, function ($query) use ($status) {
                                return $query->where('status',$status);
                            })
                            ->get();

        Log::debug('retval = ' . print_r($students,true));
        Log::info('END');
        return response()->json([ compact('students') ]);
    }

    /**
     * 内部メソッド
     */
    function getYears( $organization_id, $is_to_now_year = false)
    {
        Log::info('START');

        $query = '';
        $query .= 'SELECT DATE_FORMAT(eventdate,"%Y") as year ';
        $query .= 'FROM studentattendances ';
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
        Log::info('stu-attend serch START');

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
                $studentattendances = Studentattendance::select(
                    'studentattendances.id               as id'
                    ,'studentattendances.organization_id as organization_id'
                    ,'studentattendances.student_id      as student_id'
                    ,'studentattendances.eventdate       as eventdate'
                    ,'studentattendances.entrytime       as entrytime'
                    ,'studentattendances.exittime        as exittime'
                    ,'studentattendances.status          as status'
                    ,'students.id                        as stu_id'
                    ,'students.last_name                 as last_name'
                    ,'students.first_name                as first_name'
                    ,'students.status                    as stu_status'
                    )
                    ->leftJoin('students', function ($join) {
                        $join->on('studentattendances.student_id', '=', 'students.id');
                    })
                    ->where('studentattendances.organization_id','>=',$organization_id)
                    ->where('students.status','=',1)    //1:入会中
                    ->whereBetween("eventdate", [$stadate, $stadate])
                    ->whereNull('studentattendances.deleted_at')
                    ->whereNull('students.deleted_at')
                    ->sortable()
                    ->paginate(10);;
            } else {
                $studentattendances = Studentattendance::select(
                    'studentattendances.id               as id'
                    ,'studentattendances.organization_id as organization_id'
                    ,'studentattendances.student_id      as student_id'
                    ,'studentattendances.eventdate       as eventdate'
                    ,'studentattendances.entrytime       as entrytime'
                    ,'studentattendances.exittime        as exittime'
                    ,'studentattendances.status          as status'
                    ,'students.id                        as stu_id'
                    ,'students.last_name                 as last_name'
                    ,'students.first_name                as first_name'
                    ,'students.status                    as stu_status'
                )
                ->leftJoin('students', function ($join) {
                    $join->on('studentattendances.student_id', '=', 'students.id');
                })
                ->where('studentattendances.organization_id','=',$organization_id)
                ->where('students.status','=',1)    //1:入会中
                ->whereBetween("eventdate", [$stadate, $stadate])
                ->whereNull('studentattendances.deleted_at')
                ->whereNull('students.deleted_at')
                ->sortable()
                ->paginate(10);;
            }
        //日付が入力されない
        } else {
            if($organization_id == 0) {
                $studentattendances = Studentattendance::select(
                    'studentattendances.id               as id'
                    ,'studentattendances.organization_id as organization_id'
                    ,'studentattendances.student_id      as student_id'
                    ,'studentattendances.eventdate       as eventdate'
                    ,'studentattendances.entrytime       as entrytime'
                    ,'studentattendances.exittime        as exittime'
                    ,'studentattendances.status          as status'
                    ,'students.id                        as stu_id'
                    ,'students.last_name                 as last_name'
                    ,'students.first_name                as first_name'
                    ,'students.status                    as stu_status'
                    )
                    ->leftJoin('students', function ($join) {
                        $join->on('studentattendances.student_id', '=', 'students.id');
                    })
                    ->where('studentattendances.organization_id','>=',$organization_id)
                    ->where('students.status','=',1)    //1:入会中
                    ->whereNull('studentattendances.deleted_at')
                    ->whereNull('students.deleted_at')
                    ->sortable()
                    ->paginate(10);;
            } else {
                $studentattendances = Studentattendance::select(
                    'studentattendances.id               as id'
                    ,'studentattendances.organization_id as organization_id'
                    ,'studentattendances.student_id      as student_id'
                    ,'studentattendances.eventdate       as eventdate'
                    ,'studentattendances.entrytime       as entrytime'
                    ,'studentattendances.exittime        as exittime'
                    ,'studentattendances.status          as status'
                    ,'students.id                        as stu_id'
                    ,'students.last_name                 as last_name'
                    ,'students.first_name                as first_name'
                    ,'students.status                    as stu_status'
                )
                ->leftJoin('students', function ($join) {
                    $join->on('studentattendances.student_id', '=', 'students.id');
                })
                ->where('studentattendances.organization_id','=',$organization_id)
                ->where('students.status','=',1)    //1:入会中
                ->whereNull('studentattendances.deleted_at')
                ->whereNull('students.deleted_at')
                ->sortable()
                ->paginate(10);;
            }
        }

        $common_no = '00_3';
        $compacts = compact( 'studentattendances','common_no','frdate','todate' );

        // $ret = $this->export($request);

        Log::info('stu-attend serch  END');
        return view( 'studentattendance.index', $compacts );

    }

    /**
     *
     */
    public function get_validator(Request $request)
    {
        Log::debug('stu-attend get_validator $request->all() = ' . print_r($request->all(),true) );

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
