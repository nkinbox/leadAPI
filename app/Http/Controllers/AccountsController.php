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
        ->where('lead_send_mail.status', 'booked')->groupBy('lead_send_mail.lead_id')->orderBy('booking_date')->paginate(100);

        $leadDetails = DB::table('lead_detail')->select('lead_detail.lead_id', 'lead_detail.enq_name', 'lead_detail.enq_hotel', 'lead_detail.enq_adv_pay_val', 'lead_detail.reference_number', 'lead_detail.mail_date', 'lead_detail.lead_status', 'project_detail.project_id as hotel_id')->join('project_detail', function($join) {
            $join->on('lead_detail.enq_hotel', '=', 'project_detail.project_name')
            ->where('project_detail.city', 'lead_detail.enq_city')->where('lead_detail.enq_website', 'project_detail.website_url');
        })
        ->whereIn('lead_id', $leadIds->pluck('lead_id'))->get();
        $leadDetails = $leadDetails->groupBy('lead_id');
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
        foreach($leadIds as $index => $leadId) {
            $leadDetail = $leadDetails[$leadId->lead_id]->first();
            $lsm = current(DB::select('select amount, commission from lead_send_mail where lead_id = ? and status = "booked" order by lsm_id desc limit 1', [$leadDetail->lead_id]));
            if(!$lsm) continue;
            $this->response['data'][$index]['lead_id'] = $leadDetail->lead_id;
            $this->response['data'][$index]['hotel_id'] = $leadDetail->hotel_id;
            $this->response['data'][$index]['date'] = $leadId->booking_date;
            $this->response['data'][$index]['customer_name'] = $leadDetail->enq_name;
            $this->response['data'][$index]['hotel_name'] = $leadDetail->enq_hotel;
            $this->response['data'][$index]['booking_type'] = 'Hotel';
            $this->response['data'][$index]['invoice_id'] = 'HT'.$leadDetail->lead_id;
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
        $this->response = [
            'customer_name' => '',
            'email' => '',
            'phone' => '',
            'date' => ['from' => '', 'to' => ''],
            'list' => [],
        ];
        $collection = collect([]);
        if($table == 'hotel') {
            $lead = DB::table('lead_detail')->select('lead_id', 'enq_name as customer_name', 'enq_hotel as seller_name', 'enq_email as email', 'reference_number as booking_number', 'enq_mobile as phone', 'enq_adv_pay_val', 'enq_check_out')->where('lead_id', $id)->first();

            $this->response['customer_name'] = $lead->customer_name;
            $this->response['email'] = $lead->email;
            $this->response['phone'] = $lead->phone;

            $lsm = current(DB::select('select amount, commission from lead_send_mail where lead_id = ? and status = ? order by lsm_id desc limit 1', [$lead->lead_id, 'booked']));

            $bookingDate = current(DB::select('select mail_date as date from lead_send_mail where lead_id = ? and status = ? order by lsm_id limit 1', [$lead->lead_id, 'booked']));

            $advanceAmount = 0;
            if($lead->enq_adv_pay_val) {
                $bookingAmount = round($lead->enq_adv_pay_val) + round($lsm->amount);
            } else {
                $bookingAmount = round($lsm->amount);
            }

            $collection->push([
                'date' => $bookingDate->date,
                'particular' => $lead->seller_name,
                'amount' => $bookingAmount,
                'type' => 'debit',
                'voucher' => 'Sales Invoice',
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
                $advanceAmount += round($row->amount);
            }
            unset($transfer);
            $remainingAmount = $bookingAmount - $advanceAmount;
            if($remainingAmount > 0 && strtotime($lead->enq_check_out) < time()) {
                $collection->push([
                    'date' => $lead->enq_check_out,
                    'particular' => $lead->seller_name,
                    'amount' => $remainingAmount,
                    'type' => 'credit',
                    'voucher' => 'Receipt',
                    'bill_id' => ''
                ]);
            }

        }
        $collection = $collection->sortBy('date');
        if($collection->isNotEmpty()) {
            $this->response['date']['from'] = $collection->first()['date'];
            $this->response['date']['to'] = $collection->last()['date'];
        }
        $this->response['list'] = $collection->values()->all();
        return response()->json($this->response);
    }
    public function customerPurchase($table, $id) {
        $this->response = [
            'seller_name' => '',
            'email' => '',
            'phone' => '',
            'address' => '',
            'date' => ['from' => '', 'to' => ''],
            'list' => [],
        ];
        $collection = collect([]);
        if($table == 'hotel') {
            $hotel = DB::table('project_detail')->select('project_name as seller_name', 'city', 'website_url', 'address', 'email', 'phone', 'mobile')->where('project_id', $id)->first();
            if($hotel) {
                $this->response['seller_name'] = $hotel->seller_name;
                $this->response['address'] = $hotel->address;
                $this->response['email'] = $hotel->email;
                $this->response['phone'] = $hotel->phone.', '.$hotel->mobile;
                
                $leads = DB::table('lead_detail')->select('lead_id', 'reference_number as booking_number', 'enq_adv_pay_val', 'enq_check_out', 'enq_name as customer_name')->where([
                    'enq_hotel' => $hotel->seller_name,
                    'enq_city' => $hotel->city,
                    'enq_website' => $hotel->website_url
                ])->get();
                
                foreach($leads as $lead) {
                    $lsm = current(DB::select('select amount, commission from lead_send_mail where lead_id = ? and status = ? order by lsm_id desc limit 1', [$lead->lead_id, 'booked']));
        
                    $bookingDate = current(DB::select('select mail_date as date from lead_send_mail where lead_id = ? and status = ? order by lsm_id limit 1', [$lead->lead_id, 'booked']));
                    $paidAmount = 0;
                    if($lead->enq_adv_pay_val) {
                        $purchaseAmount = round($lead->enq_adv_pay_val) + round($lsm->amount) - round($lsm->commission);
                    } else {
                        $purchaseAmount = round($lsm->amount) - round($lsm->commission);
                    }
        
                    $collection->push([
                        'date' => $bookingDate->date,
                        'particular' => 'Hotel Room Booking',
                        'amount' => $purchaseAmount,
                        'type' => 'credit',
                        'voucher' => 'Purchase',
                        'bill_id' => $lead->id
                    ]);

                    // $advanceAmount = DB::table('lead_advance_details')->where('lead_id', $lead->lead_id)->sum('adv_amount');
                    
                    $transfer = DB::table('lead_payment_transfer')
                    ->select('transfer_amount as amount', 'transfer_payment_mode as particular', 'transfer_update as date')
                    ->where('transfer_status', 'transfer_completed')
                    ->where('lead_id', $lead->id)->get();
                    foreach($transfer as $row) {
                        $collection->push([
                            'date' => $row->date,
                            'particular' => $row->particular,
                            'amount' => round($row->amount),
                            'type' => 'debit',
                            'voucher' => 'Payment',
                            'bill_id' => $lead->id
                        ]);
                        $paidAmount += round($row->amount);
                    }
                    unset($transfer);
                    $remainingAmount = $purchaseAmount - $paidAmount;
                    if($remainingAmount > 0 && strtotime($lead->enq_check_out) < time()) {
                        $collection->push([
                            'date' => $lead->enq_check_out,
                            'particular' => $lead->customer_name,
                            'amount' => $remainingAmount,
                            'type' => 'debit',
                            'voucher' => 'Payment',
                            'bill_id' => ''
                        ]);
                    }
                }
    
            }
            $collection = $collection->sortBy('date');
            if($collection->isNotEmpty()) {
                $this->response['date']['from'] = $collection->first()['date'];
                $this->response['date']['to'] = $collection->last()['date'];
            }
            $this->response['list'] = $collection->values()->all();
        }


        return response()->json($this->response);
    }
}
