<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\FormRequest;
use App\FormFields;
use App\FormMap;
use App\Website;
use App\WebsiteMap;
use App\AddProjectClientSeo;
use DB;
use Validator;

class APIController extends Controller
{
    private $response;
    private $form_request;
    private $crm_table;
    private $request_map;
    private $website;
    private $addprojectclientseo;
    private $fields;
    private $validator;

    public function __construct() {
        $this->response = [
            "success" => 0,
            "error" => [],
            "message" => ""
        ];
    }
    public function insert(Request $request) {
        if($this->identifyRequest($request)) {
            $this->setTableFieldsAndValidator();
            $map = $this->mapRequestToTable();
            foreach($map as $key => $value) {
                if(is_array($value)) {
                    $temp = [];
                    foreach($value as $field) {
                        $temp[] = $field. ' => ' .$request->{$field};
                    }
                    $this->fields[$key] = implode(", ", $temp);
                } else {
                    $this->fields[$key] = $request->{$value};
                }
            }
            $validator = Validator::make($this->fields, $this->validator);
            if($validator->fails()) {
                $this->response["error"] = $validator->errors();
            } else {
                DB::table($this->crm_table->name)->insert($this->fields);
                $this->response["success"] = 1;
                $this->response["message"] = "Successful";
            }
        }
        return response()->json($this->response);
    }
    private function identifyRequest(Request $request) {
        if($request->api_token) {
            if(!$this->verifyAPIToken($request->api_token))
            return false;
        } else {
            //return false; #TODO
        }
        $keys = [];
        foreach($request->all() as $key => $val) {
            if($key != "access_token" && $key != "domain_name" && $key != "api_token")
            $keys[] = $key;
        }
        sort($keys);
        $sha1 = sha1(implode("", $keys));
        if($request->access_token) {
            $formMap = FormMap::where('access_token', $request->access_token)->first();
            if($formMap) {
                $this->form_request = $formMap->FormRequest;
                if($this->form_request->sha1 != $sha1)
                return false;
                $this->crm_table = $formMap->CrmTable;
                $this->request_map = $formMap->FormRequestRoute;
                return $this->form_request->authorized;
            }
        }
        if($request->domain_name) {
            $this->website = Website::select('id', 'domain')->where('domain', $request->domain_name)->first();
            if(!$this->website)
            return false;
            $this->addprojectclientseo = AddProjectClientSeo::where('website_url', 'like', '%' .$this->website->domain. '%')->first();
        } else {
            $this->website = null;
            return false;
        }
        $form_request = FormRequest::where('sha1', $sha1)->first();
        if($form_request) {
            $this->form_request = $form_request;
            $websiteMap = WebsiteMap::where(['website_id' => $this->website->id, "form_request_id" => $this->form_request->id])->first();
            $formMap = $websiteMap->FormMap;
            if($formMap) {
                $this->crm_table = $formMap->CrmTable;
                $this->request_map = $formMap->FormRequestRoute;
                return $this->form_request->authorized;
            } else {
                return false;
            }
        } else {
            $this->createUnidentifiedRequest($sha1, $keys);
            return false;
        }
    }
    private function createUnidentifiedRequest($sha1, $fields) {
        $form_request = new FormRequest;
        $form_request->sha1 = $sha1;
        $form_request->save();
        $this->form_request = $form_request;
        foreach($fields as $field) {
            $formField = new FormFields;
            $formField->form_request_id = $form_request->id;
            $formField->name = $field;
            $formField->save();
        }
        if($this->website) {
            $websiteMap = new WebsiteMap;
            $websiteMap->website_id = $this->website->id;
            $websiteMap->form_request_id = $this->form_request->id;
            $websiteMap->save();
        }
    }
    private function setTableFieldsAndValidator() {
        $fields = $this->crm_table->Columns;
        foreach($fields as $field) {
            $validator = [];
            $match = [];
            $value = $field->default_value;
            if(preg_match('/eval\((.*)\)/', $field->default_value, $match)) {
                $value = eval('return '.$match[1].';');
            }
            if($field->sometimes || $value == "") {
                $validator[] = "sometimes";
            }
            if($field->required) {
                $validator[] = "required";
            }
            if($value == null) {
                $validator[] = "nullable";
            }
            if($field->type) {
                $validator[] = $field->type;
            } else {
                $validator[] = "string";
            }
            if($field->type && $field->max_length && $field->type == "string") {
                $validator[] = "max:".$field->max_length;
            }
            $this->fields[$field->name] = $value;
            $this->validator[$field->name] = implode("|", $validator);
        }
    }
    private function mapRequestToTable() {
        $table = [];
        $form = [];
        $map = [];
        $fields = $this->crm_table->Columns;
        foreach($fields as $field) {
            $table[$field->id] = $field->name;
        }
        $fields = $this->form_request->Fields;
        foreach($fields as $field) {
            $form[$field->id] = $field->name;
        }
        foreach($this->request_map as $i) {
            if(isset($map[$table[$i->crm_column_id]])) {
                if(is_array($map[$table[$i->crm_column_id]])) {
                    $map[$table[$i->crm_column_id]][] = $form[$i->form_field_id];
                } else {
                    $map[$table[$i->crm_column_id]] = [$map[$table[$i->crm_column_id]]];
                    $map[$table[$i->crm_column_id]][] = $form[$i->form_field_id];
                }
            } else {
                $map[$table[$i->crm_column_id]] = $form[$i->form_field_id];
            }
        }
        return $map;
    }
    private function verifyAPIToken($api_token) {
        return true;
    }
}
