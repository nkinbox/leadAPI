<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\FormRequest;
use App\CrmTable;

class FormRequestController extends Controller
{
    private $response;
    public function __construct()
    {
        $this->response = [];
    }
    public function index($operation = null, $id = null) {
        if($operation == "add" || $operation == null) {
            $method = "POST";
            $action = route('formRequest.create');
        } elseif($operation == "edit") {
            $method = "PUT";
            $action = route('formRequest.update');
        } elseif($operation == "delete") {
            $method = "DELETE";
            $action = route('formRequest.delete');
        }
        $this->response['method'] = $method;
        $this->response['action'] = $action;
        $this->response['operation'] = $operation?ucwords($operation):"Add";
        $this->response['row'] = FormRequest::find($id);
        $this->response['rows'] = FormRequest::all();
        return view('formrequest', $this->response);
    }
    public function create(Request $request) {
        $this->validate($request, [
            "name" => "required|string|max:50",
            "authorized" => "required|boolean",
        ]);
        $formRequest = new FormRequest;
        $formRequest->sha1 = "";
        $formRequest->name = $request->name;
        $formRequest->authorized = $request->authorized;
        $formRequest->save();
        return redirect()->route('formRequest')->with("message", "Request Created Successfully.");
    }
    public function update(Request $request) {
        $this->validate($request, [
            "id" => "required|exists:form_requests",
            "name" => "required|string|max:50",
            "authorized" => "required|boolean",
        ]);
        $formRequest = FormRequest::find($request->id);
        $formRequest->name = $request->name;
        $formRequest->authorized = $request->authorized;
        $formRequest->save();
        return redirect()->route('formRequest')->with("message", "Request Updated Successfully.");
    }
    public function delete(Request $request) {
        $this->validate($request, [
            "id" => "required|exists:form_requests",
        ]);
        $FormRequest = FormRequest::find($request->id);
        $FormRequest->Fields()->forceDelete();
        $FormRequest->forceDelete();
        return redirect()->route('formRequest')->with("message", "Request Deleted Successfully.");
    }
}
