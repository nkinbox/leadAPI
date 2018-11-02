<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Website;
use App\WebsiteMap;

class WebsiteMapController extends Controller
{
    private $response;
    public function __construct()
    {
        $this->response = [];
    }
    public function all() {
        $this->response['websites'] = Website::select('id', 'domain')->get();
        $this->response['unmappedRequest'] = WebsiteMap::select('website_id')->where('form_map_id', 0)->get();
        $this->response['unmappedRequest'] = array_column($this->response['unmappedRequest']->toArray(), 'website_id');
        return view('websites', $this->response);
    }
    public function index($website_id, $operation = null, $id = null) {
        $this->response['website'] = Website::find($website_id);
        if(!$this->response['website']) {
            return redirect()->back()->with('error', 'Error Occured');
        }
        if($operation == "add" || $operation == null) {
            $method = "POST";
            $action = route('websiteMap.create');
        } elseif($operation == "edit") {
            $method = "PUT";
            $action = route('websiteMap.update');
        } elseif($operation == "delete") {
            $method = "DELETE";
            $action = route('websiteMap.delete');
        }
        $this->response['method'] = $method;
        $this->response['action'] = $action;
        $this->response['operation'] = $operation?ucwords($operation):"Add";
        $this->response['row'] = WebsiteMap::find($id);
        $this->response['rows'] = $this->response['website']->WebsiteMap;
        return view('websitemap', $this->response);
    }
    public function create(Request $request) {
        $this->validate($request, [
            "id" => "required|exists:website_maps",
            "form_map_id" => "required|exists:form_maps,id",
        ]);
        $WebsiteMap = WebsiteMap::find($request->id);
        $WebsiteMap->form_map_id = $request->form_map_id;
        $WebsiteMap->save();
        return redirect()->route('websiteMap', ["website_id" => $WebsiteMap->website_id])->with("message", "Map Created Successfully.");
    }
    public function delete(Request $request) {
        $this->validate($request, [
            "id" => "required|exists:website_maps"
        ]);
        $WebsiteMap = WebsiteMap::find($request->id);
        $WebsiteMap->forceDelete();
        return redirect()->route('websiteMap', ["website_id" => $WebsiteMap->website_id])->with("message", "Map Deleted Successfully.");
    }
}
