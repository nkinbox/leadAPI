<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Agents;
use App\SimAllocation;
use App\CallRegister;
use App\Department;
use DB;
use Illuminate\Support\Facades\Log;

class CallRecorderController extends Controller {
    private $response;
    public function __construct() {
        $this->response = [];
    }
    public function login(Request $request) {
        // Log::info($request->all());
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
            'sim_allocation.*.sim_name' => 'nullable|string|max:50',
            'sim_allocation.*.is_personal' => 'nullable|boolean',
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
            if(!$sim) {
                $sim = new SimAllocation;
                $sim->id = $sim_allocation['sim_id'];
            }
            $sim->operator = $sim_allocation['operator'];
            $sim->dial_code = isset($sim_allocation['dial_code'])?$sim_allocation['dial_code']:null;
            $sim->phone_number = isset($sim_allocation['phone_number'])?$sim_allocation['phone_number']:null;
            $sim->agent_id = $agent->id;
            $sim->sim_name = isset($sim_allocation['sim_name'])?$sim_allocation['sim_name']:null;
            $sim->is_personal = (isset($sim_allocation['is_personal'])&&$sim_allocation['is_personal'])?1:0;
            $sim->save();
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
            if($request->call_type == 'incoming' || $request->call_type == 'outgoing')
            $callRegister->duration = $request->duration;
            else
            $callRegister->duration = 0;
            $callRegister->device_time = $request->device_time;
            $callRegister->call_type = $request->call_type;
            $callRegister->save();
            $this->lastUpdateAt([$sim->agent_id]);
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
                    'duration' => ($log['call_type'] == 'incoming' || $log['call_type'] == 'outgoing')?$log['duration']:0,
                    'device_time' => $log['device_time'],
                    'call_type' => $log['call_type'],
                ];
            }
        }
        $this->lastUpdateAt($sims);
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
            'saved_name' => 'sometimes|required|string',
            'call_log_type' => 'nullable',
            'sim_allocation_id' => 'nullable'
        ]);
        $logs = CallRegister::selectRaw('call_registers.agent_id, call_registers.dial_code, call_registers.phone_number, call_registers.saved_name, call_registers.duration, call_registers.device_time, call_registers.call_type, call_registers.identified, agents.name as agent_name, departments.name as department_name, sim_allocations.phone_number as agent_phone_number')
            ->join('agents', 'agents.id', '=', 'call_registers.agent_id')
            ->join('departments', 'departments.id', '=', 'agents.department_id')
            ->join('sim_allocations', 'sim_allocations.id', '=', 'call_registers.sim_allocation_id')
            ->when($request->sim_allocation_id, function($query) use (&$request) {
                return $query->where('sim_allocation_id', $request->sim_allocation_id);
            })
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
            ->when(($request->date && !($request->start_datetime && $request->end_datetime)), function($query) use (&$request){
                return $query->whereDate('device_time', $request->input('date', date('Y-m-d')));
            })
            ->when($request->phone_number, function($query) use (&$request) {
                return $query->where('call_registers.phone_number', 'like', '%'.$request->phone_number.'%');
            })
            ->when($request->saved_name, function($query) use (&$request) {
                return $query->where('call_registers.saved_name', 'like', '%'.$request->saved_name.'%');
            })
            ->when($request->call_log_type, function($query) use (&$request) {
                return $query->where('identified', $request->call_log_type);
            })
        ->orderBy('device_time', 'desc')->orderBy('duration', 'desc');

        DB::statement('create temporary table temp_call_logs(id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY, call_type_latest INT DEFAULT 0, latest INT DEFAULT 0, has_duration INT DEFAULT 0) '.$logs->toSql(), $logs->getBindings());
        DB::statement('create temporary table temp_unique_calls select min(id) as id from temp_call_logs group by concat(dial_code, phone_number, call_type)');
        DB::update('update temp_call_logs inner join temp_unique_calls on temp_unique_calls.id = temp_call_logs.id set call_type_latest = 1');
        DB::statement('drop temporary table temp_unique_calls');
        DB::insert('create temporary table temp_unique_calls select min(id) as id, sum(duration) as total_duration, concat(dial_code, phone_number) as dial_code_phone_number from temp_call_logs group by dial_code_phone_number');
        DB::update('update temp_call_logs inner join temp_unique_calls on temp_unique_calls.id = temp_call_logs.id set latest = 1');
        DB::update('update temp_call_logs inner join temp_unique_calls on dial_code_phone_number = concat(temp_call_logs.dial_code, temp_call_logs.phone_number) set has_duration = case when total_duration = 0 then 0 else 1 end');
        DB::statement('drop temporary table temp_unique_calls');
        
        $summary = DB::table('temp_call_logs')->selectRaw('
            count(1) as overview_total,
            sum(duration) overview_duration,
            sum(case when latest = 1 then 1 else 0 end) as overview_unique,
            sum(case when latest = 1 and has_duration = 0 then 1 else 0 end) as untouched_total,
            sum(case when latest = 1 and has_duration = 0 and (call_type = "missed" or call_type = "rejected" or call_type = "incoming") then 1 else 0 end) as untouched_incoming,
            sum(case when latest = 1 and has_duration = 0 and (call_type = "busy" or call_type = "outgoing")  then 1 else 0 end) as untouched_outgoing,
            sum(case when call_type = "incoming" then 1 else 0 end) as incoming_total,
            sum(case when call_type = "incoming" then duration else 0 end) as incoming_duration,
            sum(case when call_type = "incoming" and call_type_latest = 1 then 1 else 0 end) as incoming_unique,
            sum(case when call_type = "outgoing" then 1 else 0 end) as outgoing_total,
            sum(case when call_type = "outgoing" then duration else 0 end) as outgoing_duration,
            sum(case when call_type = "outgoing" and call_type_latest = 1 then 1 else 0 end) as outgoing_unique,
            sum(case when call_type = "missed" and latest = 1 and call_type_latest = 1 and has_duration = 1 then 1 else 0 end) as unattended_missed,
            sum(case when call_type = "rejected" and latest = 1 and has_duration = 1 and call_type_latest = 1 then 1 else 0 end) as unattended_rejected,
            sum(case when call_type = "busy" and latest = 1 and has_duration = 1 and call_type_latest = 1 then 1 else 0 end) as unattended_busy
        ')->get();
        $summary = (array) $summary->first();
        foreach($summary as $key => $val) {
            $i = explode('_', $key);
            $this->response['summary'][$i[0]][$i[1]] = [
                'value' => ($val?$val:0),
                'name' => ($i[1] == 'duration'?'':$key)
            ];
        }
        $this->response['logs'] = DB::table('temp_call_logs')->get();
        return response()->json($this->response);
    }
    public function agents() {
        $agents = Agents::selectRaw('agents.*, departments.name as department_name, departments.id as department_id')->leftJoin('departments', 'departments.id', '=', 'agents.department_id')->get();
        $sim_allocations = SimAllocation::all()->groupBy('agent_id');
        $this->response['agents'] = [];
        foreach($agents as $agent) {
            $active = strtotime($agent->last_update_at);
            $active = ($active && (time() - $active) < 3600)?1:0;
            $this->response['agents'][] = [
                'agent_id' => $agent->id,
                'user_name' => $agent->user_name,
                'name' => $agent->name,
                'department_id' => $agent->department_id,
                'department_name' => $agent->department_name,
                'is_active' => $active,
                'last_update_at' => $agent->last_update_at,
                'sim_allocations' => isset($sim_allocations[$agent->id])?$sim_allocations[$agent->id]:[]
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
    public function analytics(Request $request) {
        $this->validate($request, [
            'type' => 'required|in:time,days,months',
            'date' => 'required|date_format:Y-m-d',
            'agent_id' => 'sometimes|required|string',
            'department_id' => 'sometimes|required|numeric',
            'call_log_type' => 'nullable',
            'sim_allocation_id' => 'nullable'
        ]);
        $logs = CallRegister::when(($request->type == 'time'), function($query) use (&$request) {
            return $query->whereDate('device_time', $request->date)
            ->selectRaw('call_type, sum(case when hour(device_time) >= 0 && hour(device_time) < 2 then 1 else 0 end) as `12AM_2AM`,
            sum(case when hour(device_time) >= 2 && hour(device_time) < 4 then 1 else 0 end) as `2AM_4AM`,
            sum(case when hour(device_time) >= 4 && hour(device_time) < 6 then 1 else 0 end) as `4AM_6AM`,
            sum(case when hour(device_time) >= 6 && hour(device_time) < 8 then 1 else 0 end) as `6AM_8AM`,
            sum(case when hour(device_time) >= 8 && hour(device_time) < 10 then 1 else 0 end) as `8AM_10AM`,
            sum(case when hour(device_time) >= 10 && hour(device_time) < 12 then 1 else 0 end) as `10AM_12PM`,
            sum(case when hour(device_time) >= 12 && hour(device_time) < 14 then 1 else 0 end) as `12PM_2PM`,
            sum(case when hour(device_time) >= 14 && hour(device_time) < 16 then 1 else 0 end) as `2PM_4PM`,
            sum(case when hour(device_time) >= 16 && hour(device_time) < 18 then 1 else 0 end) as `4PM_6PM`,
            sum(case when hour(device_time) >= 18 && hour(device_time) < 20 then 1 else 0 end) as `6PM_8PM`,
            sum(case when hour(device_time) >= 20 && hour(device_time) < 22 then 1 else 0 end) as `8PM_10PM`,
            sum(case when hour(device_time) >= 22 then 1 else 0 end) as `10PM_12PM`');
        })
        ->when(($request->type == 'days'), function($query) use (&$request) {
            return $query->whereMonth('device_time', date('n', strtotime($request->date)))->whereYear('device_time', date('Y', strtotime($request->date)))
            ->selectRaw('count(1) as count, day(device_time) as day, call_type')->groupBy('day');
        })
        ->when(($request->type == 'months'), function($query) use (&$request) {
            return $query->whereYear('device_time', date('Y', strtotime($request->date)))
            ->selectRaw('count(1) as count, month(device_time) as month, call_type')->groupBy('month');
        })
        ->when($request->department_id, function($query) use (&$request) {
            return $query->join('agents', 'agents.id', '=', 'call_registers.agent_id')->where('agents.department_id', $request->department_id);
        })
        ->when($request->agent_id, function($query) use (&$request) {
            return $query->where('agent_id', $request->agent_id);
        })
        ->when($request->call_log_type, function($query) use (&$request) {
            return $query->where('identified', $request->call_log_type);
        })
        ->when($request->sim_allocation_id, function($query) use (&$request) {
            return $query->where('sim_allocation_id', $request->sim_allocation_id);
        })
        ->groupBy('call_type')->get();
        $call_type = [
            'incoming' => 0,
            'outgoing' => 1,
            'missed' => 2,
            'rejected' => 3,
            'busy' => 4
        ];
        if($request->type == 'time') {
            $emptyData = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            $categories = [
                '12AM_2AM' => 0,
                '2AM_4AM' => 1,
                '4AM_6AM' => 2,
                '6AM_8AM' => 3,
                '8AM_10AM' => 4,
                '10AM_12PM' => 5,
                '12PM_2PM' => 6,
                '2PM_4PM' => 7,
                '4PM_6PM' => 8,
                '6PM_8PM' => 9,
                '8PM_10PM' => 10,
                '10PM_12PM' => 11
            ];

            $this->response['series'] = [];
            foreach($call_type as $ct => $index) {
                $this->response['series'][$index] = [
                    'name' => $ct,
                    'data' => $emptyData
                ];
            }
            foreach($logs as $log) {
                $log = $log->toArray();
                $index = $call_type[$log['call_type']];
                unset($log['call_type']);
                foreach($log as $cat => $count)
                $this->response['series'][$index]['data'][$categories[$cat]] = $count;
            }
            $this->response['categories'] = [];
            foreach($categories as $category => $index) {
                $this->response['categories'][$index] = str_replace('_', '-', $category);
            }
        } elseif($request->type == 'days') {
            $emptyData = [];
            $lastDay = date('t', strtotime($request->date));
            $this->response['categories'] = [];
            for($i = 0; $i < $lastDay; $i++) {
                $emptyData[$i] = 0;
                $this->response['categories'][] = ($i+1). ' ' .date('M', strtotime($request->date));
            }
            foreach($call_type as $ct => $index) {
                $this->response['series'][$index] = [
                    'name' => $ct,
                    'data' => $emptyData
                ];
            }
            foreach($logs as $log) {
                $index = $log->day - 1;
                $this->response['series'][$call_type[$log->call_type]]['data'][$index]= $log->count;
            }
        } elseif($request->type == 'months') {
            $emptyData = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            $this->response['categories'] = [
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'May',
                'Jun',
                'Jul',
                'Aug',
                'Sep',
                'Oct',
                'Nov',
                'Dec'
            ];
            foreach($call_type as $ct => $index) {
                $this->response['series'][$index] = [
                    'name' => $ct,
                    'data' => $emptyData
                ];
            }
            foreach($logs as $log) {
                $index = $log->month - 1;
                $this->response['series'][$call_type[$log->call_type]]['data'][$index]= $log->count;
            }
        }
        return response()->json($this->response);
    }
    public function alerts(Request $request) {
        $this->validate($request, [
            'sim_id' => 'required'
        ]);
        Agents::join('sim_allocations', 'sim_allocations.agent_id', '=', 'agents.id')->where('sim_allocations.id', $request->sim_id)->update(['last_update_at' => null]);
    }
    public function getWebsites(Request $request) {
        return DB::table('add_project_client_seo')->select('project_client_seo_id as id', 'website_url as website', 'display_name')->when($request->user_name, function($query) use(&$request) {
            return $query->join('user_project_assign', 'user_project_assign.project_website', '=', 'add_project_client_seo.website_url')->join('user_info', 'user_info.user_email', '=', 'user_project_assign.user_email')->where('user_info.attendance_user_id', $request->user_name);
        })->orderBy('display_name')->get();
    }
    public function pushToCRM(Request $request) {
        $this->validate($request, [
            'id' => 'required_if:type,hotel|integer|exists:add_project_client_seo,project_client_seo_id',
            'phone_number' => 'required',
            'type' => 'required|in:hotel,tour',
            'user_name' => 'required|exists:agents',
            'saved_name' => 'nullable'
        ]);
        $agent = DB::table('user_info')->select('id')->where('attendance_user_id', $request->user_name)->first();
        if($request->type == 'hotel') {
            $website = DB::table('add_project_client_seo')->select('project_client_seo_id as id', 'website_url as website', 'city')->where('project_client_seo_id', $request->id)->first();
            DB::table('lead_detail')->insert([
                'project_client_seo_id' => $website->id,
                'subject' => 'TCCS',
                'to_address' => 'alok@tripclues.com',
                'enq_website' => $website->website,
                'enq_city' => $website->city,
                'enq_name' => $request->saved_name?$request->saved_name:'TCCS',
                'enq_email' => $request->phone_number.'@tripclues.com',
                'enq_mobile' => $request->phone_number,
                'enq_date' => date('Y-m-d'),
                'enq_time' => date('H:i:s'),
                'lead_date' => date('Y-m-d'),
                'lead_time' => date('H:i:s'), 
                'enq_type' => 'Desktop',
                'assigned_to' => $agent->id
            ]);
        } else {
            DB::table('tour_lead_details')->insert([
                'tour_type' => 'Tour',
                'tour_city' => 'Destination',
                'tour_package' => 'TCCS Package',
                'name' => $request->saved_name?$request->saved_name:'TCCS',
                'email' => $request->phone_number.'@tripclues.com',
                'phone' => $request->phone,
                'tour_lead_status' => 'fresh',
                'tour_lead_date' => date('Y-m-d'),
                'tour_lead_time' => date('H:i:s'),
                'tour_enq_date' => date('Y-m-d'),
                'tour_enq_time' => date('H:i:s'), 
                'enq_type' => 'Desktop',
                'assigned_to' => $agent->id
            ]);
        }
        return response()->json(['success' => 1]);
    }
    private function lastUpdateAt($agent_id) {
        Agents::whereIn('id', $agent_id)->update(['last_update_at' => date('Y-m-d H:i:s')]);
    }

    public function engagement() {
        $this->validate($request, [
            'start_datetime' => 'required|date_format:Y-m-d H:i:s',
            'end_datetime' => 'required|date_format:Y-m-d H:i:s',
        ]);
        $logs = DB::select('select agents1.user_name, agents1.name, agents2.user_name, agents2.name from call_registers inner join agents as agents1 on agents1.id = call_registers.agent_id inner join (select agents.id, agents.user_name, agents.name, sim_allocations.phone_number from agents inner join sim_allocations on sim_allocations.agent_id = agents.id) as agents2 on agents2.phone_number = call_registers.phone_number', [$request->start_datetime, $request->end_datetime]);
    }
}