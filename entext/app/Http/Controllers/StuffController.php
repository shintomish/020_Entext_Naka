<?php

namespace App\Http\Controllers;

use Validator;
use App\Models\Stuff;
use App\Models\Stuffattendance;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StuffController extends Controller
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
        Log::info('stuffs index START');

        $organization  = $this->auth_user_organization();
        $organization_id = $organization->id;

        if($organization_id == 0) {
            $stuffs = Stuff::select(
                'stuffs.id                as id'
                ,'stuffs.organization_id  as organization_id'
                ,'stuffs.custom_no        as custom_no'   //顧客No
                ,'stuffs.last_name        as last_name'
                ,'stuffs.first_name       as first_name'
                ,'stuffs.sex              as sex'
                ,'stuffs.care_type        as care_type'
                ,'stuffs.status           as status'      //1:在職中,2:休職,3:退職'
                ,'stuffs.joindate         as joindate'    //入社日
                ,'stuffs.school_name      as school_name'
                //100:幼稚園,210-260:小学,310-330:中学,410-430:高校,510:大学,600:社会人,700:無職
                ,'stuffs.employment_type  as employment_type'
                //0:日曜,1:月曜,2:火曜,3:水曜,4:木曜,5:金曜,6:土曜
                ,'stuffs.week_type        as week_type'
                ,'stuffs.entrytime        as entrytime'   //開始時間
                ,'stuffs.exittime         as exittime'    //終了時間
                )

                ->whereNull('stuffs.deleted_at')
                // ->orderBy('stuffs.custom_no', 'asc')
                ->sortable()
                ->paginate(300);
        } else {
            $stuffs = Stuff::select(
                'stuffs.id                as id'
                ,'stuffs.organization_id  as organization_id'
                ,'stuffs.custom_no        as custom_no'   //顧客No
                ,'stuffs.last_name        as last_name'
                ,'stuffs.first_name       as first_name'
                ,'stuffs.sex              as sex'
                ,'stuffs.care_type        as care_type'
                ,'stuffs.status           as status'      //1:在職中,2:休職,3:退職'
                ,'stuffs.joindate         as joindate'    //入社日
                ,'stuffs.school_name      as school_name'
                //100:幼稚園,210-260:小学,310-330:中学,410-430:高校,510:大学,600:社会人,700:無職
                ,'stuffs.employment_type  as employment_type'
                //0:日曜,1:月曜,2:火曜,3:水曜,4:木曜,5:金曜,6:土曜
                ,'stuffs.week_type        as week_type'
                ,'stuffs.entrytime        as entrytime'   //開始時間
                ,'stuffs.exittime         as exittime'    //終了時間
                )

                ->where('stuffs.organization_id','=',$organization_id)
                ->whereNull('stuffs.deleted_at')
                // ->orderBy('stuffs.custom_no', 'asc')
                ->sortable()
                ->paginate(300);
        }
        $common_no = '00_4';
        $compacts = compact( 'stuffs','common_no' );
        Log::info('stuffs index END');
        return view( 'stuff.index', $compacts );

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        Log::info('Stuff create START');

        $organization = $this->auth_user_organization();
        $organization_id = $organization->organization_id;
        $stuff = $request->all();
        $compacts = compact( 'organization','stuff','organization_id' );

        Log::info('Stuff create End');
        return view('stuff.create', $compacts );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Log::info('Stuff store START');

        $organization = $this->auth_user_organization();
        $request->merge( ['organization_id'=> $organization->id] );

        $validator = $this->get_validator($request);
        if ($validator->fails()) {
            return redirect('stuff/create')->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        Log::info('Stuff store beginTransaction - start');
        try {
            // Stuff::create($request->all());
            $stuff = new Stuff();

            $stuff->organization_id  = 1;
            $stuff->last_name        = $request->last_name;
            $stuff->first_name       = $request->first_name;
            $stuff->last_kana        = $request->last_kana;
            $stuff->first_kana       = $request->first_kana;
            $stuff->sex              = $request->sex;
            $stuff->custom_no        = $request->custom_no ;
            $stuff->birthdate        = $request->birthdate;
            $stuff->age              = $request->age;
            $stuff->status           = $request->status;
            $stuff->joindate         = $request->joindate ;
            $stuff->recessdate       = $request->recessdate;
            $stuff->withdrawaldate   = $request->withdrawaldate;
            $stuff->parent_name      = $request->parent_name;
            $stuff->school_name      = $request->school_name;
            $stuff->zip_code         = $request->zip_code;
            $stuff->address          = $request->address;
            $stuff->phone_1          = $request->phone_1;
            $stuff->phone_2          = $request->phone_2;
            $stuff->email            = $request->email;
            $stuff->reserve          = $request->reserve;
            $stuff->week_type        = $request->week_type;
            $stuff->care_type        = $request->care_type;
            $stuff->employment_type  = $request->employment_type;
            $stuff->ic_number        = $request->ic_number;
            $stuff->employment_type  = $request->employment_type;
            $stuff->entrytime        = $request->entrytime;
            $stuff->exittime         = $request->exittime;

            $stuff->save();         //  Inserts
            DB::commit();
            Log::info('Stuff store beginTransaction - end');
        }
        catch(\QueryException $e) {
            Log::error('exception : ' . $e->getMessage());
            DB::rollback();
            Log::info('Stuff store beginTransaction - end(rollback)');
        }

        Log::info('Stuff store End');

        // toastrというキーでメッセージを格納
        session()->flash('toastr', config('toastr.create'));
        // return redirect()->route('stuff.index')->with('success', '新規登録完了');
        return redirect()->route('stuff.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        Log::info('Stuff show CALLED');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        Log::info('Stuff edit START');

        $organization  = $this->auth_user_organization();
        $stuff         = Stuff::find($id);

        Log::info('Stuff edit END');
        return view('stuff.edit', compact('stuff', 'organization'));
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
        Log::info('Stuff update START');

        $validator = $this->get_validator($request);
        if ($validator->fails()) {
            return redirect('stuff/'.$id.'/edit')->withErrors($validator)->withInput();
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
        Log::info('Stuff update beginTransaction - start');
        try {
            Stuff::where('id', $id)->update($update);
            DB::commit();
            Log::info('Stuff update beginTransaction - end');
        }
        catch(\QueryException $e) {
            Log::error('Stuff update exception : ' . $e->getMessage());
            DB::rollback();
            Log::info('Stuff update beginTransaction - end(rollback)');
        }
        Log::info('Stuff update START');
        // toastrというキーでメッセージを格納
        session()->flash('toastr', config('toastr.update'));
        // return redirect()->route('stuff.index')->with('success', '編集完了');
        return redirect()->route('stuff.index');
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
        Log::info('Stuff update_api START');
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
        // Log::debug('Stuff update_api $update   = ' . print_r($update, true));

        // $status = array();
        // $validator = $this->get_validator($request);
        // if ($validator->fails()) {
        //     Log::debug('Stuff update_api validate Error : ' . print_r($validator->errors(),true));
        //     $status = array( 'error_code' => 501,
        //                      'message'    => 'There is an error in the input item.',
        //                      'errors'     => $validator->errors() );
        //     return response()->json([ compact('status') ]);
        // }

        // DB::beginTransaction();
        // Log::info('Stuff update_api beginTransaction - start');
        // try{
        //     $query = DB::table('stuffs')->where('id', $request->Stuff_id);
        //     $sql = $query->getGrammar()->compileUpdate($query, $update);
        //     $updated = $query->update($update);
        //     Log::info('Stuff update_api $sql     = ' . $sql);
        //     Log::info('Stuff update_api $updated = ' . $updated);
        //     $status = array(   'error_code' => 0, 'message'  => 'Your data has been changed!' );

        //     DB::commit();
        //     Log::info('Stuff update_api beginTransaction - end');
        // }
        // catch (Throwable $e) {
        //     Log::error('Stuff update_api exception : ' . $e->getMessage());
        //     DB::rollback();
        //     Log::info('Stuff update_api beginTransaction - end(rollback)');
        //     $status = array(   'error_code' => 501, 'message'  => 'system error' );
        // }

        // Log::info('Stuff update_api END');
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
        Log::info('Stuff destroy START');

        DB::beginTransaction();
        Log::info('Stuff destroy beginTransaction - start');
        try {
            $stuffs = Stuff::find($id);
            $stuffs->deleted_at     = now();
            $result = $stuffs->save();

            DB::commit();
            Log::info('Stuff destroy beginTransaction - end');
        }
        catch(\QueryException $e) {
            Log::error('exception : ' . $e->getMessage());
            DB::rollback();
            Log::info('Stuff destroy beginTransaction - end(rollback)');
        }

        Log::info('Stuff destroy END');
        // return redirect()->route('Stuffattendance.index')->with('success', '削除完了');

        // toastrというキーでメッセージを格納
        session()->flash('toastr', config('toastr.delete'));
        return redirect()->route('stuff.index');
    }

    /**
     *
     */
    public function get_validator(Request $request)
    {
        Log::debug('Stuff get_validator $request->all() = ' . print_r($request->all(),true) );

        $rules   = ['last_name'       => 'required|max:255',
                    'first_name'      => 'required|max:255',
                    'custom_no'       => 'required',
                    'sex'             => 'required',
                    // 'employment_type' => 'required',
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
                     'custom_no.required'       => '管理Noは入力必須項目です。',
                     'sex.required'             => '性別は入力必須項目です。',
                    //  'employment_type.required' => '学年タイプは入力必須項目です。',
                     'status.required'          => '現在の状態は入力必須項目です。',
                     'entrytime.required'       => '開始時間は入力必須項目です。',
                     'exittime.required'        => '終了時間は入力必須項目です。',
                    ];

        $validator = Validator::make($request->all(), $rules, $messages);

        return $validator;
    }

}
