<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class AccountsController extends Controller
{
    public function ledgerSales(Request $request) {
        $this->validate($request, [
            'date_start' => 'required|date_format:Y-m-d', 
            'date_end' => 'required|date_format:Y-m-d|after_or_equal:date_start', 
        ]);
        $leadDetails = DB::table('lead_detail')
        ->where('lead_detail.mail_date', '>=', $request->date_start)
        ->where('lead_detail.mail_date', '<=', $request->date_end)->paginate(50);
        return response()->json($result);
    }
    public function ledgerPurchases(Request $request) {
        $this->validate($request, [
            'date_start' => 'required|date_format:Y-m-d', 
            'date_end' => 'required|date_format:Y-m-d|after_or_equal:date_start', 
        ]);
    }
}
