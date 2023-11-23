<?php

namespace App\Http\Controllers;

use App\Models\Visitor;
use Illuminate\Http\Request;

class VisitorCountController extends Controller
{
    //
    public function index() {

        return view('visitor.index');

    }

    public function ajax_index(Request $request) {

        $year = date('Y');
        $month = date('m');

        if($request->filled('year', 'month')) {

            $year = $request->year;
            $month = $request->month;

        }

        $visitors = Visitor::select('hour', \DB::raw('COUNT(id) AS access_count'))
            ->where('year', $year)
            ->where('month', $month)
            ->groupBy('hour')
            ->pluck('access_count', 'hour');

        $counts = [];

        for($i = 0 ; $i < 24 ; $i++) {

            $counts[$i] = $visitors->get($i, 0);  // 存在しない時間は "0" 回

        }

        return $counts;

    }
}
