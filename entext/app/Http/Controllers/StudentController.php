<?php

namespace App\Http\Controllers;

use Validator;
use App\Models\Student;
use App\Models\Studentattendance;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StudentController extends Controller
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        Log::info('students index START');

        $organization  = $this->auth_user_organization();
        $organization_id = $organization->id;

        if($organization_id == 0) {
            $students = Student::select(
                'students.id                as id'
                ,'students.organization_id  as organization_id'
                ,'students.custom_no        as custom_no'   //顧客No
                ,'students.last_name        as last_name'
                ,'students.first_name       as first_name'
                ,'students.sex              as sex'
                ,'students.care_type        as care_type'
                ,'students.status           as status'      //1:入会中,2:休会,3:退会'
                ,'students.joindate         as joindate'    //入会日
                ,'students.school_name      as school_name'
                //100:幼稚園,210-260:小学,310-330:中学,410-430:高校,510:大学,600:社会人,700:無職
                ,'students.employment_type  as employment_type'
                //0:日曜,1:月曜,2:火曜,3:水曜,4:木曜,5:金曜,6:土曜
                ,'students.week_type        as week_type'
                ,'students.entrytime        as entrytime'   //開始時間
                ,'students.exittime         as exittime'    //終了時間
                )

                ->whereNull('students.deleted_at')
                // ->orderBy('students.custom_no', 'asc')
                ->sortable()
                ->paginate(300);
        } else {
            $students = Student::select(
                'students.id                as id'
                ,'students.organization_id  as organization_id'
                ,'students.custom_no        as custom_no'   //顧客No
                ,'students.last_name        as last_name'
                ,'students.first_name       as first_name'
                ,'students.sex              as sex'
                ,'students.care_type        as care_type'
                ,'students.status           as status'      //1:入会中,2:休会,3:退会'
                ,'students.joindate         as joindate'    //入会日
                ,'students.school_name      as school_name'
                //100:幼稚園,210-260:小学,310-330:中学,410-430:高校,510:大学,600:社会人,700:無職
                ,'students.employment_type  as employment_type'
                //0:日曜,1:月曜,2:火曜,3:水曜,4:木曜,5:金曜,6:土曜
                ,'students.week_type        as week_type'
                ,'students.entrytime        as entrytime'   //開始時間
                ,'students.exittime         as exittime'    //終了時間
                )

                ->where('students.organization_id','=',$organization_id)
                ->whereNull('students.deleted_at')
                // ->orderBy('students.custom_no', 'asc')
                ->sortable()
                ->paginate(300);
        }
        $common_no = '00_2';
        $compacts = compact( 'students','common_no' );
        Log::info('students index END');
        return view( 'student.index', $compacts );

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        Log::info('student create START');

        $organization = $this->auth_user_organization();
        $organization_id = $organization->organization_id;
        $student = $request->all();
        $compacts = compact( 'organization','student','organization_id' );

        Log::info('student create End');
        return view('student.create', $compacts );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Log::info('student store START');

        $organization = $this->auth_user_organization();
        $request->merge( ['organization_id'=> $organization->id] );

        $validator = $this->get_validator($request);
        if ($validator->fails()) {
            return redirect('stu/create')->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        Log::info('student store beginTransaction - start');
        try {
            // Student::create($request->all());
            $student = new Student();

            $student->organization_id  = 1;
            $student->last_name        = $request->last_name;
            $student->first_name       = $request->first_name;
            $student->last_kana        = $request->last_kana;
            $student->first_kana       = $request->first_kana;
            $student->sex              = $request->sex;
            $student->custom_no        = $request->custom_no ;
            $student->birthdate        = $request->birthdate;
            $student->age              = $request->age;
            $student->status           = $request->status;
            $student->joindate         = $request->joindate ;
            $student->recessdate       = $request->recessdate;
            $student->withdrawaldate   = $request->withdrawaldate;
            $student->parent_name      = $request->parent_name;
            $student->school_name      = $request->school_name;
            $student->zip_code         = $request->zip_code;
            $student->address          = $request->address;
            $student->phone_1          = $request->phone_1;
            $student->phone_2          = $request->phone_2;
            $student->email            = $request->email;
            $student->reserve          = $request->reserve;
            $student->week_type        = $request->week_type;
            $student->care_type        = $request->care_type;
            $student->employment_type  = $request->employment_type;
            $student->ic_number        = $request->ic_number;
            $student->employment_type  = $request->employment_type;
            $student->entrytime        = $request->entrytime;
            $student->exittime         = $request->exittime;

            $student->save();         //  Inserts
            DB::commit();
            Log::info('student store beginTransaction - end');
        }
        catch(\QueryException $e) {
            Log::error('exception : ' . $e->getMessage());
            DB::rollback();
            Log::info('student store beginTransaction - end(rollback)');
        }

        Log::info('student store End');

        // toastrというキーでメッセージを格納
        session()->flash('toastr', config('toastr.create'));
        // return redirect()->route('stu.index')->with('success', '新規登録完了');
        return redirect()->route('stu.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        Log::info('student show CALLED');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        Log::info('student edit START');

        $organization  = $this->auth_user_organization();
        $student                = Student::find($id);

        Log::info('student edit END');
        return view('student.edit', compact('student', 'organization'));
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
        Log::info('student update START');

        $validator = $this->get_validator($request);
        if ($validator->fails()) {
            return redirect('stu/'.$id.'/edit')->withErrors($validator)->withInput();
        }

        $update = [
            'last_name'        => $request->last_name,
            'first_name'       => $request->first_name,
            'last_kana'        => $request->last_kana,
            'first_kana'       => $request->first_kana,
            'sex'              => $request->sex,
            'custom_no'        => $request->custom_no ,
            'birthdate'        => $request->birthdate,
            'age'              => $request->age,
            'status'           => $request->status,
            'joindate'         => $request->joindate ,
            'recessdate'       => $request->recessdate,
            'withdrawaldate'   => $request->withdrawaldate,
            'parent_name'      => $request->parent_name,
            'school_name'      => $request->school_name,
            'zip_code'         => $request->zip_code,
            'address'          => $request->address,
            'phone_1'          => $request->phone_1,
            'phone_2'          => $request->phone_2,
            'email'            => $request->email,
            'reserve'          => $request->reserve,
            'week_type'        => $request->week_type,
            'employment_type'  => $request->employment_type,
            'ic_number'        => $request->ic_number,
            'employment_type'  => $request->employment_type,
            'entrytime'        => $request->entrytime,
            'exittime'         => $request->exittime,

            'updated_at'      => date('Y-m-d H:i:s')
        ];

        DB::beginTransaction();
        Log::info('student update beginTransaction - start');
        try {
            Student::where('id', $id)->update($update);
            DB::commit();
            Log::info('student update beginTransaction - end');
        }
        catch(\QueryException $e) {
            Log::error('student update exception : ' . $e->getMessage());
            DB::rollback();
            Log::info('student update beginTransaction - end(rollback)');
        }
        Log::info('student update START');
        // toastrというキーでメッセージを格納
        session()->flash('toastr', config('toastr.update'));
        // return redirect()->route('stu.index')->with('success', '編集完了');
        return redirect()->route('stu.index');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update_api(Request $request)
    {
        Log::info('student update_api START');
        Log::debug('$request = ' . print_r($request->all(), true));

        // $update = array(
        //     'last_name'       => $request->last_name,
        //     'first_name'      => $request->first_name,
        //     'last_kana'       => $request->last_kana,
        //     'first_kana'      => $request->first_kana,
        //     'sex'             => $request->sex,
        //     'birthdate'       => $request->birthdate,
        //     'picture_path'    => $request->picture_path,
        //     'first_code'      => $request->first_code ,
        //     'last_code'       => $request->last_code ,
        //     'prefecture'      => $request->prefecture,
        //     'city'            => $request->city,
        //     'address'         => $request->address,
        //     'other'           => $request->other,
        //     'phone'           => $request->phone,
        //     'email'           => $request->email,
        //     'employment_type' => $request->employment_type,
        //     'status'          => $request->status,
        //     'entrytime'       => $request->entrytime,
        //     'exittime'        => $request->exittime,
        //     'updated_at'      => date('Y-m-d H:i:s')
        // );
        // Log::debug('student update_api $update   = ' . print_r($update, true));

        // $status = array();
        // $validator = $this->get_validator($request);
        // if ($validator->fails()) {
        //     Log::debug('student update_api validate Error : ' . print_r($validator->errors(),true));
        //     $status = array( 'error_code' => 501,
        //                      'message'    => 'There is an error in the input item.',
        //                      'errors'     => $validator->errors() );
        //     return response()->json([ compact('status') ]);
        // }

        // DB::beginTransaction();
        // Log::info('student update_api beginTransaction - start');
        // try{
        //     $query = DB::table('students')->where('id', $request->student_id);
        //     $sql = $query->getGrammar()->compileUpdate($query, $update);
        //     $updated = $query->update($update);
        //     Log::info('student update_api $sql     = ' . $sql);
        //     Log::info('student update_api $updated = ' . $updated);
        //     $status = array(   'error_code' => 0, 'message'  => 'Your data has been changed!' );

        //     DB::commit();
        //     Log::info('student update_api beginTransaction - end');
        // }
        // catch (Throwable $e) {
        //     Log::error('student update_api exception : ' . $e->getMessage());
        //     DB::rollback();
        //     Log::info('student update_api beginTransaction - end(rollback)');
        //     $status = array(   'error_code' => 501, 'message'  => 'system error' );
        // }

        // Log::info('student update_api END');
        // return response()->json([ compact('status') ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Log::info('student destroy START');

        DB::beginTransaction();
        Log::info('student destroy beginTransaction - start');
        try {
            $students = Student::find($id);
            $students->deleted_at     = now();
            $result = $students->save();

            DB::commit();
            Log::info('student destroy beginTransaction - end');
        }
        catch(\QueryException $e) {
            Log::error('exception : ' . $e->getMessage());
            DB::rollback();
            Log::info('student destroy beginTransaction - end(rollback)');
        }

        Log::info('student destroy END');
        // return redirect()->route('studentattendance.index')->with('success', '削除完了');

        // toastrというキーでメッセージを格納
        session()->flash('toastr', config('toastr.delete'));
        return redirect()->route('stu.index');
    }

    /**
     *
     */
    public function get_validator(Request $request)
    {
        Log::debug('student get_validator $request->all() = ' . print_r($request->all(),true) );

        $rules   = ['last_name'       => 'required|max:255',
                    'first_name'      => 'required|max:255',
                    'custom_no'       => 'required',
                    'sex'             => 'required',
                    'employment_type' => 'required',
                    'status'          => 'required',
                    'entrytime'       => 'required',
                    'exittime'        => 'required',
                ];

        $messages = ['last_name.required'       => 'お名前(名字)は入力必須項目です。',
                    'last_name.max'            => 'お名前(名字)は:max文字以内で入力してください。',
                    'first_name.required'      => 'お名前(名前)は入力必須項目です。',
                    'first_name.max'           => 'お名前(名前)は:max文字以内で入力してください。',
                    'last_kana.required'       => 'お名前(名字カナ)は入力必須項目です。',
                    'last_kana.max'            => 'お名前(名字カナ)は:max文字以内で入力してください。',
                    'first_kana.required'      => 'お名前(名前カナ)は入力必須項目です。',
                    'first_kana.max'           => 'お名前(名前カナ)は:max文字以内で入力してください。',
                    'custom_no.required'       => '顧客Noは入力必須項目です。',
                    'sex.required'             => '性別は入力必須項目です。',
                    'employment_type.required' => '学年タイプは入力必須項目です。',
                    'status.required'          => '現在の状態は入力必須項目です。',
                    'entrytime.required'       => '開始時間は入力必須項目です。',
                    'exittime.required'        => '終了時間は入力必須項目です。',
                    ];

        $validator = Validator::make($request->all(), $rules, $messages);

        return $validator;
    }

}
