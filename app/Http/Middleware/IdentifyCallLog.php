<?php

namespace App\Http\Middleware;

use Closure;
use App\CallRegister;
use App\Agents;

class IdentifyCallLog
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        return $next($request);
    }
    public function terminate($request, $response)
    {
        if($response->status() == 200) {
            if(is_array($request->logs)) {
                $phone_numbers = $request->input('logs.*.phone_number');
                $agents = Agents::join('sim_allocations', 'sim_allocations.agent_id', '=', 'agents.id')->whereIn('sim_allocations.phone_number', $phone_numbers)->selectRaw('agents.id, sim_allocations.phone_number')->get();
                $agents = $agents->mapWithKeys(function ($item) {
                    return [$item->phone_number => $item->id];
                });
                $sim_ids = $request->input('logs.*.sim_id');
                $device_times = $request->input('logs.*.device_time');
                $callRegisters = CallRegister::whereIn('sim_allocation_id', $sim_ids)->whereIn('device_time', $device_times)->limit(count($request->logs))->get();
                foreach($callRegisters as $callRegister) {
                    if(isset($agents[$callRegister->phone_number])) {
                        $callRegister->identified = 'agent';
                        $callRegister->identified_id = $agents[$callRegister->phone_number];
                    } else {
                        $callRegister->identified = 'external';
                    }
                    $callRegister->save();
                }
            }
        }
    }
}
