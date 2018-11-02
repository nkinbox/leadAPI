<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\FormMap;
use App\CrmTable;
use App\Formrequest;

class FormMapController extends Controller
{
    private $response;
    public function __construct()
    {
        $this->response = [];
    }
    public function index($form_id, $operation = null, $id = null) {
        $this->response['form'] = FormRequest::find($form_id);
        if(!$this->response['form']) {
            return redirect()->back()->with('error', 'Error Occured');
        }
        if($operation == "add" || $operation == null) {
            $method = "POST";
            $action = route('requestMap.create');
        } elseif($operation == "edit") {
            $method = "PUT";
            $action = route('requestMap.update');
        } elseif($operation == "delete") {
            $method = "DELETE";
            $action = route('requestMap.delete');
        }
        $this->response['method'] = $method;
        $this->response['action'] = $action;
        $this->response['operation'] = $operation?ucwords($operation):"Add";
        $this->response['row'] = FormMap::find($id);
        $this->response['rows'] = $this->response['form']->Maps;
        $this->response['tables'] = CrmTable::all();
        return view('requestmap', $this->response);
    }
    public function create(Request $request) {
        $this->validate($request, [
            "form_request_id" => "required|exists:form_requests,id",
            "crm_table_id" => "required|exists:crm_tables,id",
        ]);
        if(FormMap::where(['form_request_id' => $request->form_request_id, 'crm_table_id' => $request->crm_table_id])->first()) {
            return redirect()->route('requestMap', ["form_id" => $request->form_request_id])->with("error", "Map Already Exists.");
        }
        $map = new FormMap;
        $map->form_request_id = $request->form_request_id;
        $map->crm_table_id = $request->crm_table_id;
        $map->access_token = sha1(time().$request->form_request_id.$request->crm_table_id.time());
        $map->save();
        return redirect()->route('requestMap', ["form_id" => $request->form_request_id])->with("message", "Map Created Successfully.");
    }
    public function update(Request $request) {
        $this->validate($request, [
            "id" => "required|exists:form_maps",
            "form_request_id" => "required|exists:form_requests,id",
            "crm_table_id" => "required|exists:crm_tables,id",
        ]);
        if(!FormMap::where(['form_request_id' => $request->form_request_id, 'crm_table_id' => $request->crm_table_id])->first()) {
            $map = FormMap::find($request->id);
            $map->form_request_id = $request->form_request_id;
            $map->crm_table_id = $request->crm_table_id;
            $map->save();
            $map->FormRequestRoute()->forceDelete();
        }
        return redirect()->route('requestMap', ["form_id" => $request->form_request_id])->with("message", "Map Updated Successfully.");
    }
    public function delete(Request $request) {
        $this->validate($request, [
            "id" => "required|exists:form_maps",
        ]);
        $map = FormMap::find($request->id);
        $map->FormRequestRoute()->forceDelete();
        $map->WebsiteMap()->update(['form_map_id' => 0]);
        $map->forceDelete();
        return redirect()->route('formRequest')->with("message", "Map Deleted Successfully.");
    }
}
