<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class AccountsController extends Controller
{
    private $response;
    public function ledgerSalesAndPurchase(Request $request) {
        $this->validate($request, [
            'date_start' => 'required|date_format:Y-m-d', 
            'date_end' => 'required|date_format:Y-m-d|after_or_equal:date_start', 
        ]);
        $leadDetails = DB::table('lead_detail')->select('lead_id', 'enq_name', 'enq_hotel', 'enq_adv_pay_val', 'reference_number', 'mail_date')
        ->where('lead_status', 'booked')
        ->where('lead_detail.mail_date', '>=', $request->date_start)
        ->where('lead_detail.mail_date', '<=', $request->date_end)->orderBy('mail_date', 'desc')->paginate(50);
        $this->response = [
            'data' => [],
            'links' => [
                'prev' => $leadDetails->previousPageUrl(),
                'next' => $leadDetails->nextPageUrl()
            ],
            'meta' => [
                'current_page' => $leadDetails->currentPage(),
                'from' => $leadDetails->firstItem(),
                'last_page' => $leadDetails->lastPage(),
                'path' => null,
                'per_page' => $leadDetails->count(),
                'to' => $leadDetails->lastItem(),
                'total' => $leadDetails->total()
            ]
        ];
        foreach($leadDetails as $index => $leadDetail) {
            $lsm = current(DB::select('select amount, commission from lead_send_mail where lead_id = ? order by lsm_id desc limit 1', [$leadDetail->lead_id]));
            if(!$lsm) continue;
            $this->response['data'][$index]['lead_id'] = $leadDetail->lead_id;
            $this->response['data'][$index]['date'] = $leadDetail->mail_date;
            $this->response['data'][$index]['customer_name'] = $leadDetail->enq_name;
            $this->response['data'][$index]['hotel_name'] = $leadDetail->enq_hotel;
            $this->response['data'][$index]['booking_type'] = 'Hotel';
            
            if($leadDetail->enq_adv_pay_val) {
                $this->response['data'][$index]['booking_amount'] = round($leadDetail->enq_adv_pay_val) + round($lsm->amount);
                $this->response['data'][$index]['purchase_amount'] = $this->response['data'][$index]['booking_amount'] - round($lsm->commission);
            } else {
                $this->response['data'][$index]['booking_amount'] = round($lsm->amount);
                $this->response['data'][$index]['purchase_amount'] = $this->response['data'][$index]['booking_amount'] - round($lsm->commission);
            }
        }
        return response()->json($this->response);
    }
}
