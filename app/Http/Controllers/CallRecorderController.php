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
            'call_type' => 'required|in:incoming,outgoing,missed,rejected,busy'
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
            $callRegister->call_type = $request->call_type;
            $callRegister->save();
            $this->response['success'] = 1;
            return response()->json($this->response);
        } else {
            $this->response['success'] = 0;
            return response()->json($this->response, 422);
        }
    }
    public function createLogs(Request $request) {
        $this->validate($request, [
            'logs' => 'required|array',
            'logs.*.id' => 'required',
            'logs.*.sim_id' => 'required|digits_between:10,50',
            'logs.*.dial_code' => 'required|string|max:5',
            'logs.*.phone_number' => 'required|string|max:15',
            'logs.*.saved_name' => 'nullable|string|max:100',
            'logs.*.duration' => 'required|integer|min:0',
            'logs.*.device_time' => 'required|date_format:Y-m-d H:i:s',
            'logs.*.call_type' => 'required|in:incoming,outgoing,missed,rejected,busy'
        ]);
        $sim_ids = array_column($request->logs, 'sim_id');
        $sims = SimAllocation::whereIn('id', $sim_ids)->get();
        $sims = $sims->mapWithKeys(function ($item) {
            return [$item->id => $item->agent_id];
        });
        $this->response['inserted'] = [];
        $inserted = [];
        foreach($request->logs as $log) {
            if(isset($sims[$log['sim_id']])) {
                $this->response['inserted'][] = $log['id'];
                $inserted[] = [
                    'sim_allocation_id' => $log['sim_id'],
                    'agent_id' => $sims[$log['sim_id']],
                    'dial_code' => $log['dial_code'],
                    'phone_number' => $log['phone_number'],
                    'saved_name' => $log['saved_name'],
                    'duration' => $log['duration'],
                    'device_time' => $log['device_time'],
                    'call_type' => $log['call_type'],
                ];
            }
        }
        if($inserted) {
            CallRegister::insert($inserted);
        }
        return response()->json($this->response);
    }
    public function displayLog(Request $request) {
        $this->validate($request, [
            'date' => 'sometimes|required|date_format:Y-m-d',
            'start_datetime' => 'sometimes|required|date_format:Y-m-d H:i:s',
            'end_datetime' => 'sometimes|required|date_format:Y-m-d H:i:s',
            'duration_start' => 'sometimes|numeric',
            'duration_end' => 'sometimes|numeric',
            'user_name' => 'sometimes|required|string',
            'department_id' => 'sometimes|required|numeric',
            'phone_number' => 'sometimes|required|numeric',
            'saved_name' => 'sometimes|required|string'
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
        ->when(($request->start_datetime && $request->end_datetime), function($query) use (&$request){
            return $query->whereBetween('device_time', [$request->start_datetime, $request->end_datetime]);
        })
        ->when(($request->duration_start || $request->duration_end), function($query) use (&$request){
            return $query->whereBetween('duration', [$request->duration_start, $request->duration_end]);
        })
        ->when(($request->date || !($request->start_datetime && $request->end_datetime)), function($query) use (&$request){
            return $query->whereDate('device_time', $request->input('date', date('Y-m-d')));
        })
        ->when($request->phone_number, function($query) use (&$request) {
            return $query->where('call_registers.phone_number', 'like', '%'.$request->phone_number.'%');
        })
        ->when($request->saved_name, function($query) use (&$request) {
            return $query->where('call_registers.saved_name', 'like', '%'.$request->saved_name.'%');
        })
        ->orderBy('device_time', 'desc')->orderBy('duration', 'desc')->get();
        $this->response['logs'] = [];
        $this->response['summary'] = [
            'overview' => [
                'total' => 0,
                'duration' => 0,
                'unique' => []
            ],
            'incoming' => [
                'total' => 0,
                'duration' => 0,
                'unique' => []
            ],
            'outgoing' => [
                'total' => 0,
                'duration' => 0,
                'unique' => []
            ],
            'missed' => [
                'total' => 0,
                'duration' => 0,
                'unique' => []
            ],
            'rejected' => [
                'total' => 0,
                'duration' => 0,
                'unique' => []
            ],
            'busy' => [
                'total' => 0,
                'duration' => 0,
                'unique' => []
            ]
        ];
        $listedLogs = [];
        foreach($logs as $log) {
            if(isset($this->listedLogs[$log->agent_id.$log->phone_number.$log->duration])) {
                // continue;
            }
            $this->response['summary']['overview']['total']++;
            $this->response['summary']['overview']['duration']+=$log->duration;
            $this->response['summary']['overview']['unique'][$log->dial_code.$log->phone_number] = null;
            $this->response['summary'][$log->call_type]['total']++;
            $this->response['summary'][$log->call_type]['duration']+=$log->duration;
            $this->response['summary'][$log->call_type]['unique'][$log->dial_code.$log->phone_number] = null;
            $this->response['logs'][] = [
                'agent_name' => $log->agent_name,
                'department_name' => $log->department_name,
                'agent_phone_number' => $log->agent_phone_number,
                'dial_code' => $log->dial_code,
                'phone_number' => $log->phone_number,
                'saved_name' => $log->saved_name,
                'duration' => $log->duration,
                'timestamp' => date('Y-m-d H:i:s', strtotime($log->device_time)),
                'call_type' => $log->call_type
            ];
            $this->listedLogs[$log->agent_id.$log->phone_number.$log->duration] = null;
        }
        $this->response['summary']['overview']['unique'] = count($this->response['summary']['overview']['unique']);
        $this->response['summary']['incoming']['unique'] = count($this->response['summary']['incoming']['unique']);
        $this->response['summary']['outgoing']['unique'] = count($this->response['summary']['outgoing']['unique']);
        $this->response['summary']['missed']['unique'] = count($this->response['summary']['missed']['unique']);
        $this->response['summary']['rejected']['unique'] = count($this->response['summary']['rejected']['unique']);
        $this->response['summary']['busy']['unique'] = count($this->response['summary']['busy']['unique']);
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
        $departments = Department::selectRaw('departments.id, departments.name, count(agents.id) as total_agents')->leftJoin('agents', 'departments.id', '=', 'agents.department_id')->groupBy('departments.id')->groupBy('departments.name')->get();
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