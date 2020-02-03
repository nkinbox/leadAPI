<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class AccountsController extends Controller
{
    public function ledgerSales() {
        $this->validate($request, [
            'date_start' => 'required|date_format:Y-m-d', 
            'date_end' => 'required|date_format:Y-m-d|after_or_equal:date_start', 
        ]);
        return response()->json(['success' => 1]);
    }
    public function ledgerPurchases() {
        $this->validate($request, [
            'date_start' => 'required|date_format:Y-m-d', 
            'date_end' => 'required|date_format:Y-m-d|after_or_equal:date_start', 
        ]);
    }
}
