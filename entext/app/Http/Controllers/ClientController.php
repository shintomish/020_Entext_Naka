<?php

namespace App\Http\Controllers;

use Validator;
use App\Models\User;
use App\Models\Customer;
use App\Models\Applestabl;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class ClientController extends Controller
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
        Log::info('client index START');

        $organization  = $this->auth_user_organization();
        $organization_id = $organization->id;

        if($organization_id == 0) {
        } else {
        }

        $common_no = '00_1';

        $compacts = compact( 'common_no' );

        Log::info('client index END');
        return view( 'client.index', $compacts);
    }

    public function post(Request $data)
    {
        // Log::info('client post START');
        // Log::info('client post END');
        // // ホーム画面へリダイレクト
        // return redirect('/user');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        Log::info('client show START');
        Log::info('client show END');
    }

}
