<?php

namespace App\Http\Controllers\ReportJson;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class MemberJsonController extends Controller
{
    public function index()
    {
        $members = DB::select(DB::raw(
            "
			SELECT
                *
            FROM members
            "
        ));

        return response()->json([
            'members' => $members,
        ], 200);
    }
}
