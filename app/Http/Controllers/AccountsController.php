<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class AccountsController extends Controller
{
    private $response;
    public function ledgerSales(Request $request) {
        $this->validate($request, [
            'date_start' => 'required|date_format:Y-m-d', 
            'date_end' => 'required|date_format:Y-m-d|after_or_equal:date_start', 
        ]);
        $leadDetails = DB::table('lead_detail')->select('lead_id', 'enq_name', 'enq_adv_pay_val', 'reference_number', 'mail_date')
        ->where('lead_status', 'booked')
        ->where('lead_detail.mail_date', '>=', $request->date_start)
        ->where('lead_detail.mail_date', '<=', $request->date_end)->paginate(50);
        $this->response = [
            'data' => [],
            'links' => [
                'prev' => $leadDetails->prev_page_url,
                'next' => $leadDetails->next_page_url
            ],
            'meta' => [
                'current_page' => $leadDetails->current_page,
                'from' => $leadDetails->from,
                'last_page' => $leadDetails->last_page,
                'path' => $leadDetails->path,
                'per_page' => $leadDetails->per_page,
                'to' => $leadDetails->to,
                'total' => $leadDetails->total
            ]
        ];
        foreach($leadDetails as $index => $leadDetail) {
            $lsm = current(DB::select('select amount from lead_send_mail where lead_id = ? order by lsm_id desc limit 1', $leadDetail->lead_id));
            if(!$lsm) continue;
            $this->response['data'][$index]['lead_id'] = $leadDetail->lead_id;
            $this->response['data'][$index]['date'] = $leadDetail->mail_date;
            $this->response['data'][$index]['name'] = $leadDetail->enq_name;
            $this->response['data'][$index]['booking_type'] = 'Hotel';
            
            if($leadDetail->enq_adv_pay_val) {
                $this->response['data'][$index]['amount'] = $leadDetail->enq_adv_pay_val + $lsm->amount;
            } else {
                $this->response['data'][$index]['amount'] = $lsm->amount;
            }
        }
        return response()->json($this->response);
    }
    public function ledgerPurchases(Request $request) {
        $this->validate($request, [
            'date_start' => 'required|date_format:Y-m-d', 
            'date_end' => 'required|date_format:Y-m-d|after_or_equal:date_start', 
        ]);
    }
}
