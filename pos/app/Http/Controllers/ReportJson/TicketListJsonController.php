<?php

namespace App\Http\Controllers\ReportJson;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Reports\TicketListRequest as QueryRequest;

class TicketListJsonController extends Controller
{
    public function index(QueryRequest $request)
    {
        $data = [
            "start_date" => $request->filter['start_date'],
            "end_date" => $request->filter['end_date'],
        ];

        $tickets = DB::select(DB::raw(
            "
			SELECT
                barcodes.*,
                DATE_FORMAT(barcodes.active_on,'%d-%m-%Y') as activation_date,
                barcode_types.prefix,
                barcode_types.type
            FROM barcodes
            LEFT JOIN barcode_types ON barcode_types.prefix = LEFT(barcodes.barcode_id, 1)
            WHERE 
                barcodes.created_at >= :start_date
                AND barcodes.created_at <= :end_date
            "
        ), [
            "start_date" => $request->filter['start_date'],
            "end_date" => $request->filter['end_date'] . ' 23:59:59',
        ]);

        return response()->json([
            'data' => $data,
            'tickets' => $tickets,
        ], 200);
    }
}
