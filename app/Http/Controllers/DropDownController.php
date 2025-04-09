<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DropDownController extends Controller
{
    //

    // load regions 
    public function loadItemName()
    {
        try {
            $result = DB::table('items')
                ->get(['pid as id', 'name as text']); //
            return responseMessage(status: 200, data: $result, msg: 'data loaded');
        } catch (\Throwable $e) {
            logError($e);
            return response()->json([]);
        }
    }
    // load quantity 
    public function loadItemQuantity()
    {
        try {
            $result = DB::table('items as i')->join('item_quantities as q','q.item_pid','i.pid')->where('q.region_pid',getRegionPid())
                ->select('item_pid as id',DB::raw("CONCAT(name, ' | ', quantity,' ',unit) AS text"))->get(); //
            return responseMessage(status: 200, data: $result, msg: 'data loaded');
        } catch (\Throwable $e) {
            logError($e);
            return response()->json([]);
        }
    }
    // load regions 
    public function loadStateRegion()
    {
        try {
            $result = DB::table('regions')->distinct('state')
                ->get(['state as id', 'state as text']); //
            return responseMessage(status: 200, data: $result, msg: 'data loaded');
        } catch (\Throwable $e) {
            logError($e);
            return response()->json([]);
        }
    }
    // load regions 
    public function loadRegions($state)
    {
        try {
            $result = DB::table('regions')->where('state',$state)
                ->get(['pid as id', 'region as text']); //
            return responseMessage(status: 200, data: $result, msg: 'data loaded');
        } catch (\Throwable $e) {
            logError($e);
            return response()->json([]);
        }
    }
   
    // load regions 
    public function loadRegionsAdmin()
    {
        try {
            $result = DB::table('regions')
                ->get(['pid as id', 'region as text']); //
            return responseMessage(status: 200, data: $result, msg: 'data loaded');
        } catch (\Throwable $e) {
            logError($e);
            return response()->json([]);
        }
    }

    // load regions 
    public function loadRegion33KvFeeder($region)
    {
        try {
            $result = DB::table('feeder33s')->where('zone_pid', $region)
                ->get(['pid as id', 'name as text']); //
            return responseMessage(status: 200, data: $result, msg: 'data loaded');
        } catch (\Throwable $e) {
            logError($e);
            return response()->json([]);
        }
    }

    // load regions 
    public function loadRegion11KvFeeder($feeder33)
    {
        try {
            $result = DB::table('feeder11s')->where('feeder_33_pid', $feeder33)
                ->get(['pid as id', 'name as text']); //
            return responseMessage(status: 200, data: $result, msg: 'data loaded');
        } catch (\Throwable $e) {
            logError($e);
            return response()->json([]);
        }
    }



    public function loadStates()
    {
        try {
            $data = DB::table('states')->select('id', 'state as text')->get();
            return responseMessage(status: 200, data: $data, msg: 'data loaded');

            return response()->json($data);
        } catch (\Throwable $e) {
            logError($e);
            return response()->json([]);
        }
    }
    
    public function loadStateLga($id)
    {
        try {
            $data = DB::table('state_lgas')->where('state_id', $id)->select('id', 'lga as text')->get();
            return responseMessage(status: 200, data: $data, msg: 'data loaded');

            return response()->json($data);
        } catch (\Throwable $e) {
            logError($e);
            return response()->json([]);
        }
    }

    
    public function loadTeams()
    {
        try {
            $data = DB::table('teams')->where('region_pid', getRegionPid())->select('pid as id', 'team as text')->get();
            return responseMessage(status: 200, data: $data, msg: 'data loaded');

            return response()->json($data);
        } catch (\Throwable $e) {
            logError($e);
            return response()->json([]);
        }
    }

    public function dropDownRoles()
    {
        try {
            $data = DB::table('roles')->select('name as id', 'name as text')->where('name','<>', 'super admin')->get();
            return responseMessage(status: 200, data: $data, msg: 'data loaded');

            return response()->json($data);
        } catch (\Throwable $e) {
            logError($e);
            return response()->json([]);
        }
    }

    public function dropDownUsers()
    {
        try {
            $data = DB::table('user_details')->select('user_pid as id', 'username as text')->get();

            return responseMessage(status: 200, data: $data, msg: 'data loaded');

        } catch (\Throwable $e) {
            logError($e);
            return response()->json([]);
        }
    }


    public function dropDownSupervisor()
    {
        try {
            $data = [];
            $users = User::from('users')->join('user_details as d','d.user_pid', 'users.pid')->where('d.region_pid',getRegionPid())->whereHas('roles', function ($query) {
                $query->where('name', 'supervisor');
            })->select('d.user_pid', 'd.username')->get();

            foreach ($users as $row) {
                $data[] = [
                    'id' => $row->user_pid,
                    'text' => $row->username,
                ];
            }

            return responseMessage(status: 200, data: $data, msg: 'data loaded');

        } catch (\Throwable $e) {
            logError($e);
            return response()->json([]);
        }
    }


    public function dropDownInstallers()
    {
        try {
            $data = [];
            $users = User::from('users')->join('user_details as d','d.user_pid', 'users.pid')->where('d.region_pid',getRegionPid())->whereHas('roles', function ($query) {
                $query->where('name', 'installer');
            })->select('d.user_pid', 'd.username')->get();

            foreach ($users as $row) {
                $data[] = [
                    'id' => $row->user_pid,
                    'text' => $row->username,
                ];
            }

            return responseMessage(status: 200, data: $data, msg: 'data loaded');

        } catch (\Throwable $e) {
            logError($e);
            return response()->json([]);
        }
    }


    // load regions 
    public function dropDownZoneState()
    {
        try {
            $result = DB::table('states as s')->join('trading_zones as z','z.state_id','s.id')->distinct('state')
                ->get(['s.id', 's.state as text']); //
            return responseMessage(status: 200, data: $result, msg: 'data loaded');
        } catch (\Throwable $e) {
            logError($e);
            return response()->json([]);
        }
    }

    // load regions 
    public function dropDownZone($state)
    {
        try {
            $result = DB::table('trading_zones')->where('state_id', $state)
                ->get(['pid as id', 'zone as text']); //
            return responseMessage(status: 200, data: $result, msg: 'data loaded');
        } catch (\Throwable $e) {
            logError($e);
            return response()->json([]);
        }
    }
    // load meter types 
    public function dropDownMeterType()
    {
        try {
            $result = DB::table('meter_types')
                ->get(['type as id', 'type as text']); //
            return responseMessage(status: 200, data: $result, msg: 'data loaded');
        } catch (\Throwable $e) {
            logError($e);
            return response()->json([]);
        }
    }
    // load meter types 
    public function dropDownMeterBrand()
    {
        try {
            $result = DB::table('meter_brands')
                ->get(['brand as id', 'brand as text']); //
            return responseMessage(status: 200, data: $result, msg: 'data loaded');
        } catch (\Throwable $e) {
            logError($e);
            return response()->json([]);
        }
    }
   

    

}
