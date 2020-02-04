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
        $leadIds = DB::table('lead_send_mail')->selectRaw('lead_send_mail.lead_id, min(lead_send_mail.mail_date) as booking_date')
        ->where('lead_send_mail.mail_date', '>=', $request->date_start)
        ->where('lead_send_mail.mail_date', '<=', $request->date_end)
        ->where('lead_send_mail.status', 'booked')->groupBy('lead_send_mail.lead_id')->paginate(100);

        $leadDetails = DB::table('lead_detail')->select('lead_id', 'enq_name', 'enq_hotel', 'enq_adv_pay_val', 'reference_number', 'mail_date', 'lead_status')
        ->whereIn('lead_id', $leadIds->pluck('lead_id'))->orderBy('lead_id')->get();
        $this->response = [
            'data' => [],
            'links' => [
                'first' => $leadIds->url(1),
                'prev' => $leadIds->previousPageUrl(),
                'next' => $leadIds->nextPageUrl(),
                'last' => $leadIds->url($leadIds->lastPage())
            ],
            'meta' => [
                'current_page' => $leadIds->currentPage(),
                'from' => $leadIds->firstItem(),
                'last_page' => $leadIds->lastPage(),
                'path' => null,
                'per_page' => $leadIds->count(),
                'to' => $leadIds->lastItem(),
                'total' => $leadIds->total()
            ]
        ];
        foreach($leadDetails as $index => $leadDetail) {
            $lsm = current(DB::select('select amount, commission from lead_send_mail where lead_id = ? and status = "booked" order by lsm_id desc limit 1', [$leadDetail->lead_id]));
            if(!$lsm) continue;
            $this->response['data'][$index]['lead_id'] = $leadDetail->lead_id;
            $this->response['data'][$index]['date'] = $leadDetail->mail_date;
            $this->response['data'][$index]['customer_name'] = $leadDetail->enq_name;
            $this->response['data'][$index]['hotel_name'] = $leadDetail->enq_hotel;
            $this->response['data'][$index]['booking_type'] = 'Hotel';
            $this->response['data'][$index]['status'] = $leadDetail->lead_status;
            
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
    public function customerLedger($table, $id) {
        if($table == 'hotel') {
            $lead = DB::table('lead_detail')->select('lead_id as id', 'enq_name as customer_name', 'enq_hotel as vendor_name', 'enq_email as email', 'reference_number as booking_number', 'enq_mobile as phone', 'enq_adv_pay_val')->where('lead_id', $id)->first();
            $lsm = current(DB::select('select amount, commission from lead_send_mail where lead_id = ? order by lsm_id desc limit 1', [$lead->lead_id]));
            $collection = collect([]);

            $bookingAmount = 0;
            if($lead->enq_adv_pay_val) {
                $booking_amount = round($lead->enq_adv_pay_val) + round($lsm->amount);
            } else {
                $booking_amount = round($lsm->amount);
            }
            $collection->push([
                'date' => $row->date,
                'particular' => $row->particular,
                'amount' => $bookingAmount,
                'type' => 'credit',
                'voucher' => 'Receipt',
                'bill_id' => ''
            ]);


            $transfer = DB::table('lead_advance_details')
            ->select('adv_amount as amount', 'adv_mode as particular', 'adv_date as date')
            ->where('lead_id', $id)->get();
            foreach($transfer as $row) {
                $collection->push([
                    'date' => $row->date,
                    'particular' => $row->particular,
                    'amount' => round($row->amount),
                    'type' => 'credit',
                    'voucher' => 'Receipt',
                    'bill_id' => ''
                ]);
            }
            unset($transfer);

        }
    }
}
