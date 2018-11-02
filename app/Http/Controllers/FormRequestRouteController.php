<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\FormMap;
use App\FormRequestRoute;

class FormRequestRouteController extends Controller
{
    private $response;
    public function __construct()
    {
        $this->response = [];
    }
    public function index($map_id) {
        $this->response['map'] = FormMap::find($map_id);
        if(!$this->response['map']) {
            return redirect()->back()->with('error', "Cannot Map this Request Form");
        }
        $this->response['fieldmap'] = [];
        foreach($this->response['map']->FormRequestRoute as $fieldmap) {
            $this->response['fieldmap'][$fieldmap->form_field_id] = $fieldmap->crm_column_id;
        }
        return view('fieldmap', $this->response);
    }
    public function create(Request $request) {
        $this->validate($request, [
            "form_map_id" => "required|exists:form_maps,id",
            "form_field_id.*" => "required|exists:form_fields,id",
            "crm_column_id.*" => "required|exists:crm_columns,id"
        ]);
        $map = FormMap::find($request->form_map_id);
        $map->FormRequestRoute()->forceDelete();
        foreach($request->form_field_id as $key => $form_field_id) {
            $route = new FormRequestRoute;
            $route->form_map_id = $map->id;
            $route->form_request_id = $map->form_request_id;
            $route->crm_table_id = $map->crm_table_id;
            $route->form_field_id = $form_field_id;
            $route->crm_column_id = $request->crm_column_id[$key];
            $route->save();
        }
        return redirect()->route('requestMap', ["form_id" => $map->form_request_id])->with("message", "Map Created Successfully.");
    }
}
