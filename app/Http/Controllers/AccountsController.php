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
        $result = DB::table('lead_detail')
        ->where('lead_detail.mail_date', '>=', $request->date_start)
        ->where('lead_detail.mail_date', '<=', $request->date_end)
        ->join('lead_send_mail', function ($join) {
            $join->on('lead_send_mail.lead_id', '=', 'lead_detail.lead_id')
            ->where('lead_send_mail.lsm_id', function($query) {
                $query->table('lead_send_mail')->whereRaw('lead_send_mail.lead_id = lead_detail.lead_id')->max('lead_send_mail.lsm_id');
            });
        })->selectRaw('lead_detail.reference_number, sum(case when lead_detail.enq_adv_pay_val then (lead_detail.enq_adv_pay_val+lead_send_mail.amount) else lead_send_mail.amount end) as BookingAmount')->paginate(50);
        return response()->json($result);
    }
    public function ledgerPurchases(Request $request) {
        $this->validate($request, [
            'date_start' => 'required|date_format:Y-m-d', 
            'date_end' => 'required|date_format:Y-m-d|after_or_equal:date_start', 
        ]);
    }
}
