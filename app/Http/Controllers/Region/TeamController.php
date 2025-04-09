<?php

namespace App\Http\Controllers\Region;

use App\Http\Controllers\Controller;
use App\Models\Region\Team;
use App\Models\Region\TeamMember;
use App\Models\Region\TeamSupervisor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TeamController extends Controller
{
    //
    public function loadTeams(){
        try {
            $teams = Team::with('supervisor')->where('region_pid',getRegionPid())->get();
            // logVar($teams);
           return pushData($teams);
        } catch (\Throwable $e) {
            logError($e);
            return pushData([],STS_500);
        }
    }
    //
    public function loadTeamMembers(){
        try {
            $teams = TeamMember::with(['user' => function($q){$q->select('username','user_pid', 'gsm');}])
                                ->with(['team'  => function($q){$q->select('team','pid');}])
                                ->where('region_pid',getRegionPid())->get();
           return pushData($teams);
        } catch (\Throwable $e) {
            logError($e);
            return pushData([],STS_500);
        }
    }
    public function loadMembers(){
        try {
            $teams = TeamMember::from('team_members as m')->join('teams as t','t.pid','team_pid')
                                        ->with(['user' => function($q){$q->select('username','user_pid', 'gsm');}])
                                        ->with(['team'  => function($q){$q->select('team','pid');}])
                                        ->where(['m.region_pid' => getRegionPid() , 't.supervisor' => getUserPid()])->get();
           return pushData($teams);
        } catch (\Throwable $e) {
            logError($e);
            return pushData([],STS_500);
        }
    }

    public function createTeam(Request $request){
        $validator = Validator::make($request->all(),[
            'team' => ['required', Rule::unique('teams')->where(function ($param) use ($request) {
                $param->where('pid', '<>', $request->pid)->where('region_pid', getRegionPid());
            })] ,
            'supervisor' => 'required|exists:users,pid' ,
        ]);

       if(!$validator->fails()){
            try {
                $data = [
                    'team' => $request->team,
                    'pid' => $request->pid ?? public_id(),
                    'supervisor' => $request->supervisor,
                    'region_pid' => getRegionPid(),
                ];
                $result = $this->addOrEditTeam($data);
                if(isset($request->supervisor)){
                    TeamSupervisor::create([
                                    'region_pid'  => getRegionPid(),
                                    'team_pid' => $result->pid ,
                                    'user_pid'  => $request->supervisor ,
                                ]);
                }
                return pushResponse($result, $request->pid ? 'Team Updated' : 'Team Created');
            } catch (\Throwable $e) {
                logError($e);
                return responseMessage(status: 204, data: [], msg: STS_500);
            }
       }


        return responseMessage(data: $validator->errors()->toArray(), status: 422, msg: STS_422);
    }
    public function addTeamMember(Request $request){
        $validator = Validator::make($request->all(),[
            'user_pid' => 'string|exists:users,pid' ,
            'team' => 'string|exists:teams,pid' ,
        ]);

       if(!$validator->fails()){
            try {
                $data = [
                    'team_pid' => $request->team,
                    'user_pid' => $request->user_pid,
                    'region_pid' => getRegionPid(),
                ];
                $result = $this->addMember($data);
                return pushResponse($result,  'Member Added to Team');
            } catch (\Throwable $e) {
                logError($e);
                return responseMessage(status: 204, data: [], msg: STS_500);
            }
       }


        return responseMessage(data: $validator->errors()->toArray(), status: 422, msg: STS_422);
    }

    private function addOrEditTeam(array $data){
        try {
            return Team::updateOrCreate(['pid' => $data['pid']],$data);
        } catch (\Throwable $e) {
            logError($e);
            return false;
        }
    }

    
    private function addMember(array $data){
        try {
            return TeamMember::updateOrCreate(['team_pid' => $data['team_pid'] ,'region_pid' => $data['region_pid'] ,'user_pid' => $data['user_pid']],$data);
        } catch (\Throwable $e) {
            logError($e);
            return false;
        }
    }


}
