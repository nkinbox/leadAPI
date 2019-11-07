<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Agents;
use App\SimAllocation;
use App\CallRegister;
use App\Department;

class CallRecorderController extends Controller {
    private $response;
    public function __construct() {
        $this->response = [];
    }
    public function login(Request $request) {
        $this->validate($request, [
            'name' => 'required|string|max:50',
            'user_name' => 'required|max:50',
            'password' => 'required|min:6',
            'department_id' => 'required|integer|exists:departments,id',
            'sim_allocation' => 'required|array',
            'sim_allocation.*.sim_id' => 'required|digits_between:10,50',
            'sim_allocation.*.operator' => 'required|string|max:30',
            'sim_allocation.*.dial_code' => 'nullable|string|max:5',
            'sim_allocation.*.phone_number' => 'nullable|string|max:15',
        ]);
        $agent = Agents::where('user_name', $request->user_name)->first();
        if($agent) {
            if(!Hash::check($request->password, $agent->password)) {
                $this->response['success'] = 0;
                return response()->json($this->response, 422);
            }
            if($agent->department_id != $request->department_id) {
                $agent->department_id = $request->department_id;
                $agent->save();
            }
        } else {
            $agent = new Agents;
            $agent->name = $request->name;
            $agent->user_name = $request->user_name;
            $agent->password = Hash::make($request->password);
            $agent->department_id = $request->department_id;
            $agent->save();
        }
        foreach($request->sim_allocation as $sim_allocation) {
            $sim = SimAllocation::find($sim_allocation['sim_id']);
            if($sim) {
                $sim->operator = $sim_allocation['operator'];
                $sim->agent_id = $agent->id;
                $sim->save();
            } else {
                $sim = new SimAllocation;
                $sim->id = $sim_allocation['sim_id'];
                $sim->operator = $sim_allocation['operator'];
                $sim->dial_code = isset($sim_allocation['dial_code'])?$sim_allocation['dial_code']:null;
                $sim->phone_number = isset($sim_allocation['phone_number'])?$sim_allocation['phone_number']:null;
                $sim->agent_id = $agent->id;
                $sim->save();
            }
        }
        $this->response['success'] = 1;
        return response()->json($this->response);
    }
    public function registerAgent(Request $request) {
        $this->validate($request, [
            'name' => 'required|string|max:50',
            'user_name' => 'required|max:50|unique:agents',
            'password' => 'required|min:6',
        ]);
        $agent = new Agents;
        $agent->name = $request->name;
        $agent->user_name = $request->user_name;
        $agent->password = Hash::make($request->password);
        $agent->save();
        $this->response['message'] = 'success';
        return response()->json($this->response);
    }
    public function sim_allocation(Request $request) {
        $this->validate($request, [
            'api_token' => 'required|string|size:40|exists:agents',
            'sim_allocation' => 'required|array',
            'sim_allocation.*.sim_id' => 'required|digits_between:10,50',
            'sim_allocation.*.operator' => 'required|string|max:30',
            'sim_allocation.*.dial_code' => 'nullable|string|max:5',
            'sim_allocation.*.phone_number' => 'nullable|string|max:15',
        ]);
        $agent = Agents::where('api_token', $request->api_token)->first();
        foreach($request->sim_allocation as $sim_allocation) {
            $sim = SimAllocation::find($sim_allocation['sim_id']);
            if($sim) {
                $sim->operator = $sim_allocation['operator'];
                $sim->agent_id = $agent->id;
                $sim->save();
            } else {
                $sim = new SimAllocation;
                $sim->id = $sim_allocation['sim_id'];
                $sim->operator = $sim_allocation['operator'];
                $sim->dial_code = isset($sim_allocation['dial_code'])?$sim_allocation['dial_code']:null;
                $sim->phone_number = isset($sim_allocation['phone_number'])?$sim_allocation['phone_number']:null;
                $sim->agent_id = $agent->id;
                $sim->save();
            }
        }
        $this->response['message'] = 'success';
        return response()->json($this->response);
    }
    public function createLog(Request $request) {
        $this->validate($request, [
            'sim_id' => 'required|digits_between:10,50|exists:sim_allocations,id',
            'dial_code' => 'required|string|max:5',
            'phone_number' => 'required|string|max:15',
            'saved_name' => 'nullable|string|max:100',
            'duration' => 'required|integer|min:0',
            'device_time' => 'required|date_format:Y-m-d H:i:s',
            'call_type' => 'required|in:incoming,outgoing,missed',
        ]);
        $sim = SimAllocation::find($request->sim_id);
        if($sim) {
            $callRegister = new CallRegister;
            $callRegister->sim_allocation_id = $request->sim_id;
            $callRegister->agent_id = $sim->agent_id;
            $callRegister->dial_code = $request->dial_code;
            $callRegister->phone_number = $request->phone_number;
            $callRegister->saved_name = $request->saved_name;
            $callRegister->duration = $request->duration;
            $callRegister->device_time = $request->device_time;
            if($request->call_type == 'incoming') {
                $callRegister->call_type = $request->call_type;
                $callRegister->status = (intval($callRegister->duration))?1:0;
            } elseif($request->call_type == 'outgoing') {
                $callRegister->call_type = $request->call_type;
                $callRegister->status = (intval($callRegister->duration))?1:0;
            } elseif($request->call_type == 'missed') {
                $callRegister->call_type = 'incoming';
                $callRegister->status = 0;
            }
            $callRegister->save();
            $this->response['success'] = 1;
            return response()->json($this->response);
        } else {
            $this->response['success'] = 0;
            return response()->json($this->response, 422);
        }
    }
    public function displayLog(Request $request) {
        $this->validate($request, [
            'date' => 'sometimes|required|date_format:Y-m-d',
            'user_name' => 'sometimes|required|exists:agents',
            'department_id' => 'sometimes|required|exists:departments,id'
        ]);
        $logs = CallRegister::selectRaw('call_registers.*, agents.name as agent_name, departments.name as department_name, sim_allocations.phone_number as agent_phone_number')
        ->join('agents', 'agents.id', '=', 'call_registers.agent_id')
        ->join('departments', 'departments.id', '=', 'agents.department_id')
        ->join('sim_allocations', 'sim_allocations.id', '=', 'call_registers.sim_allocation_id')
        ->when($request->user_name, function($query) use (&$request) {
            return $query->where('agents.user_name', $request->user_name);
        })
        ->when($request->department_id, function($query) use (&$request) {
            return $query->where('agents.department_id', $request->department_id);
        })
        ->whereDate('device_time', $request->input('date', date('Y-m-d')))->orderBy('device_time', 'desc')->orderBy('duration', 'desc')->get();
        $this->response['logs'] = [];
        $listedLogs = [];
        foreach($logs as $log) {
            if(isset($this->listedLogs[$log->agent_id.$log->phone_number.$log->duration])) {
                // continue;
            }
            $this->response['logs'][] = [
                'agent_name' => $log->agent_name,
                'department_name' => $log->department_name,
                'agent_phone_number' => $log->agent_phone_number,
                'dial_code' => $log->dial_code,
                'phone_number' => $log->phone_number,
                'saved_name' => $log->saved_name,
                'duration' => $log->duration,
                'timestamp' => date('Y-m-d H:i:s', strtotime($log->device_time)),
                'call_type' => ($log->call_type=='incoming'&&$log->status==0)?'missed':$log->call_type,
                'status' => $log->status
            ];
            $this->listedLogs[$log->agent_id.$log->phone_number.$log->duration] = null;
        }
        return response()->json($this->response);
    }
    public function agents() {
        $agents = Agents::selectRaw('agents.*, departments.name as department_name, departments.id as department_id')->leftJoin('departments', 'departments.id', '=', 'agents.department_id')->get();
        $this->response['agents'] = [];
        foreach($agents as $agent) {
            $this->response['agents'][] = [
                'user_name' => $agent->user_name,
                'name' => $agent->name,
                'department_id' => $agent->department_id,
                'department_name' => $agent->department_name
            ];
        }
        return response()->json($this->response);
    }
    public function departments() {
        $departments = Department::selectRaw('departments.id, departments.name, count(agents.id) as total_agents')->join('agents', 'departments.id', '=', 'agents.department_id')->groupBy('departments.id')->groupBy('departments.name')->get();
        $this->response['departments'] = [];
        $this->response['departments'][] = [
            'id' => 0,
            'name' => 'All Departments',
            'total_agents' => 0
        ];
        foreach($departments as $department) {
            $this->response['departments'][0]['total_agents'] += $department->total_agents;
            $this->response['departments'][] = [
                'id' => $department->id,
                'name' => $department->name,
                'total_agents' => $department->total_agents
            ];
        }
        return response()->json($this->response);
    }
}