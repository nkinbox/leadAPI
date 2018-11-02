<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\FormRequest;
use App\FormFields;

class FormFieldController extends Controller
{
    private $response;
    public function __construct()
    {
        $this->response = [];
    }
    public function index($form_id, $operation = null, $id = null) {
        if($operation == "add" || $operation == null) {
            $method = "POST";
            $action = route('formField.create');
        } elseif($operation == "edit") {
            $method = "PUT";
            $action = route('formField.update');
        } elseif($operation == "delete") {
            $method = "DELETE";
            $action = route('formField.delete');
        }
        $this->response['method'] = $method;
        $this->response['action'] = $action;
        $this->response['operation'] = $operation?ucwords($operation):"Add";
        $this->response['row'] = FormFields::find($id);
        $this->response['form'] = FormRequest::find($form_id);
        $this->response['rows'] = $this->response['form']->Fields;
        return view('formfields', $this->response);
    }
    public function create(Request $request) {
        $this->validate($request, [
            "form_request_id" => "required|exists:form_requests,id",
            "name" => "required|string|max:50",
        ]);
        $field = new FormFields;
        $field->form_request_id = $request->form_request_id;
        $field->name = $request->name;
        $field->save();
        if(!$this->updateSHA1($request->form_request_id)) {
            $field->forceDelete();
            return redirect()->route('formField', ["form_id" => $request->form_request_id])->with("error", "Similar Form Already Exists.");
        }
        return redirect()->route('formField', ["form_id" => $request->form_request_id])->with("message", "Field Created Successfully.");
    }
    public function update(Request $request) {
        $this->validate($request, [
            "id" => "required|exists:form_fields",
            "form_request_id" => "required|exists:form_requests,id",
            "name" => "required|string|max:50",
        ]);
        $field = FormFields::find($request->id);
        $oldName = $field->name;
        $field->form_request_id = $request->form_request_id;
        $field->name = $request->name;
        $field->save();
        if(!$this->updateSHA1($request->form_request_id)) {
            $field->forceDelete();
            $field->name = $oldName;
            $field->save();
            return redirect()->route('formField', ["form_id" => $request->form_request_id])->with("error", "Similar Form Already Exists.");
        }
        return redirect()->route('formField', ["form_id" => $request->form_request_id])->with("message", "Field Updated Successfully.");
    }
    public function delete(Request $request) {
        $this->validate($request, [
            "id" => "required|exists:form_fields",
            "form_request_id" => "required|exists:form_requests,id",
        ]);
        $field = FormFields::find($request->id);
        $field->forceDelete();
        return redirect()->route('formField', ["form_id" => $request->form_request_id])->with("message", "Field Deleted Successfully.");
    }
    public function updateSHA1($id) {
        $form = FormRequest::find($id);
        if($form) {
            $fields = [];
            foreach($form->Fields as $field) {
                $fields[] = $field->name;
            }
            sort($fields);
            $fields = implode("", $fields);
            $form->sha1 = sha1($fields);
            $exists = FormRequest::where("sha1", $form->sha1)->first();
            if($exists && $exists->id != $id) {
                return false;
            }
            $form->save();
            return true;
        }
    }
}
