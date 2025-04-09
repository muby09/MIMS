<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StaffController extends Controller
{
    //
    public function loadStaff(){
        try {
            $data = UserDetail::with('user')->with('user.roles')->with('origin')->with('lga')->where('region_pid',getRegionPid())->get();
            return pushData($data, 'Users loaded');
        } catch (\Throwable $e) {
            logError($e);
            
            return responseMessage(status: 204, data: [], msg: STS_500);

        }
    }
    public function loadAdminStaff(){
        try {
            $data = UserDetail::with('user')->with('user.roles')->with('origin')->with('lga')->get();
            return pushData($data, 'Users loaded');
        } catch (\Throwable $e) {
            logError($e);
            
            return responseMessage(status: 204, data: [], msg: STS_500);

        }
    }
    //
    public function searchStaff($query){
        try {
            $data = UserDetail::with('user')->with('user.roles')->with('origin')->with('lga')->where('region_pid',getRegionPid())->where(function($q) use ($query){
                $q->where('firstname','like','%'.$query.'%')
                ->orWhere('lastname','like','%'.$query.'%')
                ->orWhere('othername','like','%'.$query.'%')
                ->orWhere('username','like','%'.$query.'%')
                ->orWhere('gsm','like','%'.$query.'%');
            })->get();
            return pushData($data, 'Users loaded');
        } catch (\Throwable $e) {
            logError($e);
            return responseMessage(status: 204, data: [], msg: STS_500);
        }
    }

    public function createStaff(Request $request){
       
        $validator = Validator::make($request->all(),[
            'email' => ['required',Rule::unique('users')->where(function($q) use($request){
                $q->where('pid','<>',$request->pid);
            })],
            'firstname' => 'required',
            'lastname' => 'required',
            'username' => ['nullable', Rule::unique('user_details')->where(function ($q) use ($request) {
                $q->where('user_pid', '<>', $request->pid);
            })],
            'gender' => 'required',
            'role' => 'required_if:region,null',
            'region' => 'required_if:role,null',
            // 'religion' => 'required',
            'dob' => 'required',
            'state_of_origin' => 'required',
            'lga_of_origin' => 'required',
            // 'state_of_residence' => 'required',
            // 'lga_of_residence' => 'required',
            'address' => 'required',
            'file' => 'required_if:pid,null',
        ]);

        if(!$validator->fails()){

            try {
                $data = [
                    // 'user_pid' =>,
                    'firstname' => $request->firstname ,
                    // 'staff_id',
                    'lastname' => $request->lastname,
                    'othername' => $request->othername ,
                    'username' => $request->username ,
                    'gsm' => $request->gsm ,
                    // 'path',
                    'gender' => $request->gender ,
                    // 'religion' => $request->religion ,
                    'dob' => $request->dob ,
                    'state_of_origin' => $request->state_of_origin ,
                    'lga_of_origin' => $request->lga_of_origin ,
                    // 'state_of_residence' => $request->state_of_residence,
                    // 'lga_of_residence' => $request->lga_of_residence,
                    'address' => $request->address ,
                    'region_pid' => $request->region ?? getRegionPid() ,
                    'creator' => getUserPid()
                ];

                // 431
                $user = [
                    'pid' => $request->pid ?? public_id(),
                    'type' => '431',
                    'email' => $request->email,
                    'password' => '1234',
                ];

                DB::beginTransaction();
                if(!isset($request->pid)){
                    $user = User::updateOrCreate(['pid' => $user['pid'] ],$user);
                    if (isset($request->region)) {
                        $user->assignRole('region admin');
                    } else {
                        $user->assignRole($request->role);
                    }
                }else{
                    $user = User::where('pid', $request->pid)->first();
                    $user->roles()->detach();
                    if (isset($request->region)) {
                        $user->assignRole('region admin');
                    } else {
                        $user->assignRole($request->role);
                    }
                }

                if ($request->file('file')) {
                    $data['path'] = $request->file('file')->store('files/bio', 'public'); // Store in public/uploads
                }
                $data['user_pid'] = $user->pid;
                $result = UserDetail::updateOrCreate(['user_pid' => $user->pid] , $data);
                Db::commit();
                return pushResponse($result, "Staff Account Added");

            } catch (\Throwable $e) {
                logError($e);
                DB::rollBack();
                return responseMessage(status: 204, data: [], msg: STS_500);
            }
        }

        return responseMessage(data: $validator->errors()->toArray(), status: 422, msg: STS_422);

    }



    public function updatePassword(Request $request){
        $validator = Validator::make($request->all(), [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'min:5', 'confirmed'],
        ]);

        if (!$validator->fails()) {
            try {
                $user = User::where('pid',getUserPid())->first();
                $user->password = $request->password;
                $result = $user->save();
                return pushResponse($result, "Password Updated");

            } catch (\Throwable $e) {
                logError($e);
                return responseMessage(status: 204, data: [], msg: STS_500);

            }
        }
        return responseMessage(data: $validator->errors()->toArray(), status: 422, msg: STS_422);

        
    }

   
        
}
