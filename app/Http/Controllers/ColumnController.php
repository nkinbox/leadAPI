<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CrmColumn;
use App\CrmTable;

class ColumnController extends Controller
{
    private $response;
    public function __construct()
    {
        $this->response = [];
    }
    public function index($table_id, $operation = null, $id = null) {
        if($operation == "add" || $operation == null) {
            $method = "POST";
            $action = route('column.create');
        } elseif($operation == "edit") {
            $method = "PUT";
            $action = route('column.update');
        } elseif($operation == "delete") {
            $method = "DELETE";
            $action = route('column.delete');
        }
        $this->response['method'] = $method;
        $this->response['action'] = $action;
        $this->response['operation'] = $operation?ucwords($operation):"Add";
        $this->response['row'] = CrmColumn::find($id);
        $this->response['table'] = CrmTable::find($table_id);
        $this->response['rows'] = $this->response['table']->Columns;
        return view('crmcolumn', $this->response);
    }
    public function create(Request $request) {
        $this->validate($request, [
            "crm_table_id" => "required|exists:crm_tables,id",
            "name" => "required|string|max:50",
            "type" => "required|string|max:50",
            "required" => "required|boolean",
            "sometimes" => "required|boolean",
            "max_length" => "required|integer",
            "default_value" => "required|string|max:100"
        ]);
        $match = [];
        if(preg_match('/eval\((.*)\)/', $request->default_value, $match)) {
            if(eval('return '.$match[1].';') === FALSE) {
                return redirect()->route('column', ["table_id" => $request->crm_table_id])->with("error", "Default Value is Invalid.");
            }
        }
        $column = new CrmColumn;
        $column->crm_table_id = $request->crm_table_id;
        $column->name = $request->name;
        $column->type = $request->type;
        $column->required = $request->required;
        $column->sometimes = $request->sometimes;
        $column->max_length = $request->max_length;
        $column->default_value = $request->default_value;
        $column->save();
        return redirect()->route('column', ["table_id" => $request->crm_table_id])->with("message", "Column Created Successfully.");
    }
    public function update(Request $request) {
        $this->validate($request, [
            "id" => "required|exists:crm_columns",
            "crm_table_id" => "required|exists:crm_tables,id",
            "name" => "required|string|max:50",
            "type" => "required|string|max:50",
            "required" => "required|boolean",
            "sometimes" => "required|boolean",
            "max_length" => "required|integer",
            "default_value" => "required|string|max:100"
        ]);
        $match = [];
        if(preg_match('/eval\((.*)\)/', $request->default_value, $match)) {
            if(eval('return '.$match[1].';') === FALSE) {
                return redirect()->route('column', ["table_id" => $request->crm_table_id])->with("error", "Default Value is Invalid.");
            }
        }
        $column = CrmColumn::find($request->id);
        $column->crm_table_id = $request->crm_table_id;
        $column->name = $request->name;
        $column->type = $request->type;
        $column->required = $request->required;
        $column->sometimes = $request->sometimes;
        $column->max_length = $request->max_length;
        $column->default_value = $request->default_value;
        $column->save();
        return redirect()->route('column', ["table_id" => $request->crm_table_id])->with("message", "Column Updated Successfully.");
    }
    public function delete(Request $request) {
        $this->validate($request, [
            "id" => "required|exists:crm_columns",
            "crm_table_id" => "required|exists:crm_tables,id",
        ]);
        $column = CrmColumn::find($request->id);
        $column->forceDelete();
        return redirect()->route('column', ["table_id" => $request->crm_table_id])->with("message", "Column Deleted Successfully.");
    }
}
