<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CrmTable;
class TableController extends Controller
{
    private $response;
    public function __construct()
    {
        $this->response = [];
    }
    public function index($operation = null, $id = null) {
        if($operation == "add" || $operation == null) {
            $method = "POST";
            $action = route('table.create');
        } elseif($operation == "edit") {
            $method = "PUT";
            $action = route('table.update');
        } elseif($operation == "delete") {
            $method = "DELETE";
            $action = route('table.delete');
        }
        $this->response['method'] = $method;
        $this->response['action'] = $action;
        $this->response['operation'] = $operation?ucwords($operation):"Add";
        $this->response['row'] = CrmTable::find($id);
        $this->response['rows'] = CrmTable::all();
        return view('crmtable', $this->response);
    }
    public function create(Request $request) {
        $this->validate($request, [
            "name" => "required|string|max:50",
        ]);
        $table = new CrmTable;
        $table->name = $request->name;
        $table->save();
        return redirect()->route('table')->with("message", "Table Created Successfully.");
    }
    public function update(Request $request) {
        $this->validate($request, [
            "id" => "required|exists:crm_tables",
            "name" => "required|string|max:50",
        ]);
        $table = CrmTable::find($request->id);
        $table->name = $request->name;
        $table->save();
        return redirect()->route('table')->with("message", "Table Updated Successfully.");
    }
    public function delete(Request $request) {
        $this->validate($request, [
            "id" => "required|exists:crm_tables",
        ]);
        $table = CrmTable::find($request->id);
        $table->Columns()->forceDelete();
        $table->forceDelete();
        return redirect()->route('table')->with("message", "Table Deleted Successfully.");
    }
}
