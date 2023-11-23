<?php

namespace App\Http\Controllers;

use DateTime;
use Validator;
use App\Models\Holiday;
use App\Http\Requests\StoreHoliday;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HolidayController extends Controller
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
        Log::info('START');

        $organization = $this->auth_user_organization();

        Log::debug('organization_id = ' . $organization->id);
        $current_year = $request->input('select_year');

        if( is_null($current_year) ) $current_year = 0;
        if($current_year == 0 ){
            $holidays = DB::table('holidays')
                            ->where('organization_id','=', $organization->id)
                            ->whereNull('deleted_at')
                            ->orderBy('date')
                            ->paginate(10);
        }
        else{
            $s_date = strval($current_year) .'-1-1';
            $e_date = strval($current_year+1) .'-1-1';

            $holidays = DB::table('holidays')
                            ->where('organization_id','=', $organization->id)
                            ->whereNull('deleted_at')
                            ->whereYear('date','>=', $s_date)
                            ->where('date','<', $e_date)
                            ->orderBy('date')
                            ->paginate(10)
            ;
        }

        $years = DB::select('select DATE_FORMAT(date,"%Y") as year from holidays group by DATE_FORMAT(date,"%Y")');

        Log::info('END');
        return view('holiday.index', compact('holidays', 'current_year', 'years','organization'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        Log::info('CALLED');

        $organization = $this->auth_user_organization();
        $year = $request->input('select_year');

        return view('holiday.create',compact('organization','year'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Log::info('START');

        $param = array();
        DB::beginTransaction();
        Log::info('beginTransaction - start');
        try {
            $validator = $this->get_validator($request);
            if ($validator->fails()) {
                return redirect('holiday/create')->withErrors($validator)->withInput();
            }

            DB::table('holidays')->updateOrInsert(
                ['organization_id' => $request->organization_id, 'date' => $request->date],
                ['comment' => $request->comment,
                 'updated_at' => date('Y-m-d H:i:s')
                ]
            );

            $date  = new DateTime($request->date);
            $param = [ 'select_year' => intval($date->format('Y')) ];
            DB::commit();
            Log::info('beginTransaction - end');
        }
        catch(\QueryException $e) {
            Log::error('exception : ' . $e->getMessage());
            DB::rollback();
            Log::info('beginTransaction - end(rollback)');
        }
        Log::info('END');
        return redirect()->route('holiday.index',$param)->with('success', '新規登録完了');
    }

    /**
     *
     */
    public function get_validator(Request $request)
    {
        $rules   = ['date'    => 'required',
                    'comment' => 'required | max:15',];
        $messages = ['date.required'    => '日付は必ず入力してください。',
                     'comment.required' => 'メモは必ず入力してください。',
                     'comment.max'      => 'メモは:max文字以内で入力してください。',
                    ];

        $validator = Validator::make($request->all(), $rules, $messages);

        return $validator;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        Log::info('START');
        $organization  = $this->auth_user_organization();
        $holiday = Holiday::find($id);

        $date = new DateTime($holiday->date);
        $year = $date->format('Y');

        $compacts = compact( 'holiday', 'year','organization' );

        Log::info('END');
        return view( 'holiday.edit', $compacts );
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
        Log::info('START');

        $param = array();
        DB::beginTransaction();
        Log::info('beginTransaction - start');
        try {
            $validator = $this->get_validator($request);
            if ($validator->fails()) {
                return redirect('holiday/'.$id.'/edit')->withErrors($validator)->withInput();
            }

            $update = [
                'date'       => $request->date,
                'comment'    => $request->comment,
                'updated_at' => date('Y-m-d H:i:s')
            ];
            Holiday::where('id', $id)->update($update);

            $date  = new DateTime($request->date);
            $param = [ 'select_year' => $date->format('Y')];
            DB::commit();
            Log::info('beginTransaction - end');
        }
        catch(\QueryException $e) {
            Log::error('exception : ' . $e->getMessage());
            DB::rollback();
            Log::info('beginTransaction - end(rollback)');
        }

        Log::info('END');
        return redirect()->route('holiday.index',$param)->with('success', '編集完了');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Log::info('START');

        $param = array();
        DB::beginTransaction();
        Log::info('beginTransaction - start');
        try {
            $holiday = Holiday::find($id);
            Holiday::where('id', $id)->delete();

            $date  = new DateTime($holiday->date);
            $param = [ 'select_year' => $date->format('Y')];
            DB::commit();
            Log::info('beginTransaction - end');
        }
        catch(\QueryException $e) {
            Log::error('exception : ' . $e->getMessage());
            DB::rollback();
            Log::info('beginTransaction - end(rollback)');
        }

        Log::info('END');
        return redirect()->route('holiday.index',$param)->with('success', '削除完了');
    }

}
