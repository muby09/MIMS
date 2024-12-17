<?php

namespace App\Http\Controllers\Inventory;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Imports\ImportMeterList;
use Illuminate\Support\Facades\DB;
use App\Models\Inventory\MeterList;
use App\Http\Controllers\Controller;
use App\Models\Installation\Complain;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Installation\Installation;
use App\Models\Region\TeamAssignedMeter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class MeterController extends Controller
{
    // names of t7 item
    public function meterSummary(){
        try {
            $data = DB::table('meter_lists')->select(DB::raw('status,COUNT(id) as count'))->groupBy('status')->where('region_pid',getRegionPid())->get();
            return pushData($data);
        } catch (\Throwable $e) {
            logError($e->getMessage());
            return pushData([],ERR_EMT);
        }
    }
    public function meterInstallation(){
        try {
            $daily = DB::table('installations as i')->join('meter_lists as m', 'm.meter_number', 'i.meter_number')
                        ->select(DB::raw('m.status,doi,COUNT(i.id) as count'))
                        ->groupBy('i.doi')
                        ->groupBy('m.status')->where('i.region_pid',getRegionPid())
                        // ->whereMonth('i.doi',date('m'))->whereYear('i.doi',date('Y'))
                        ->get();

            $monthly = DB::table('installations')
                        ->select(DB::raw('MONTHNAME(doi) as month,COUNT(id) as count'))
                        ->groupBy(DB::raw('MONTHNAME(doi)'))
                        ->where('region_pid',getRegionPid())->whereYear('doi',date('Y'))->get()->toArray();
            // $data = DB::table('installations as i')->join('meter_lists as m', 'm.meter_number', 'i.meter_number')
            //             ->select(DB::raw('m.status,doi,COUNT(i.id) as count'))
            //             ->groupBy('i.doi')
            //             ->groupBy('m.status')->where('i.region_pid',getRegionPid())->get();
            $data = ['daily' => $daily , 'monthly' => $monthly];
            return responseMessage(status: 200, data: $data, msg: '');
        } catch (\Throwable $e) {
            logError($e->getMessage());
            return pushData([],ERR_EMT);
        }
    }
    
    // names of t7 item
    public function index(){
        try {
            $data = MeterList::paginate(20);
            return pushData($data);
            return Inertia::render('Inventory/MeterList',['data' => $data]);
        } catch (\Throwable $e) {
            logError($e->getMessage());
            return pushData([],ERR_EMT);
        }
    }
    public function searchMeterList($query){
        try {
            $data = MeterList::where('meter_number', 'like', '%' . $query . '%')->paginate(20);
            return pushData($data);
            return Inertia::render('Inventory/MeterList',['data' => $data]);
        } catch (\Throwable $e) {
            logError($e->getMessage());
            return pushData([],ERR_EMT);
        }
    }

    // 
    
    public function complainList(){
        try {
            $data = Complain::with('meter')->latest()->limit(100)->paginate(20);
            return pushData($data);
        } catch (\Throwable $e) {
            logError($e->getMessage());
            return pushData([],ERR_EMT);
        }
    }
    // 
    
    public function searchComplainList($query){
        try {
            $data = Complain::from('complains as c')->join('installations as i','i.pid','c.meter_pid')->where('c.region_pid', getRegionPid())
                ->where(function ($where) use ($query) {
                    $where->where('i.account_no', 'like', '%' . $query . '%')
                        ->orWhere('i.meter_number', 'like', '%' . $query . '%')
                        ->orWhere('i.fullname', 'like', '%' . $query . '%')
                        ->orWhere('i.gsm', 'like', '%' . $query . '%');
                })->select('c.*')->with('meter')->limit(10)->paginate(10);
            return pushData($data);
        } catch (\Throwable $e) {
            logError($e->getMessage());
            return pushData([],ERR_EMT);
        }
    }

    
    public function installedList()
    {
        try {


            $data = Installation::where('region_pid', getRegionPid())->with('origin')->with('feeder11kv')->with('feeder33kv')->latest()->paginate(2);
            return pushData($data);
        } catch (\Throwable $e) {
            logError($e->getMessage());
            return pushData([], STS_500);
        }
    }


    public function searchInstalledList($query)
    {
        try {


            $data = Installation::where('region_pid', getRegionPid())
                ->where(function($where) use($query){
                    $where->where('account_no', 'like', '%' . $query . '%')
                    ->orWhere('dt_name', 'like', '%' . $query . '%')
                    ->orWhere('meter_number', 'like', '%' . $query . '%')
                    ->orWhere('fullname', 'like', '%' . $query . '%')
                    ->orWhere('gsm', 'like', '%' . $query . '%');
                })->with('origin')->with('feeder11kv')->with('feeder33kv')->latest()->limit(100)->get();
              
            return pushData($data);
        } catch (\Throwable $e) {
            logError($e->getMessage());
            return pushData([], STS_500);
        }
    }


    public function addCustomerComplain(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'old_meter_number' => ['nullable', 'exists:meter_lists', Rule::unique('installations')->where(function ($q) use ($request) {
                    $q->where('pid', '<>', $request->pid);
                })],
                'complain' => 'required',
                'resolution' => 'required',
                'status' => 'required',
            ]);

            if (!$validator->fails()) {
                try {

                    $data  = [
                        'meter_number' => $request->meter_number ,
                        'meter_pid' => $request->meter_pid ,
                        'complain' => $request->complain ,
                        'resolution' => $request->resolution ,
                        'old_meter_number' => $request->old_meter_number ?? null ,
                        'old_seal_number' => $request->old_seal_number ?? null ,
                        'status' => $request->status ,
                        'creator' => getUserPid() ,
                        'region_pid' => getRegionPid() ,
                    ];
                    DB::beginTransaction();
                    MeterList::where('meter_number', $request->meter_number)->update(['status' => matchStatus($request->status)]);
                    if(isset($request->old_meter_number)){
                        MeterList::where('meter_number', $request->old_meter_number)->update(['status' => 3]);
                    }

                    $result = $this->addOrUpdateComplain($data);
                    if ($result) {
                        DB::commit();
                        return pushResponse($result, $request->pid ? 'Complain Recorded' : "Complain updated");
                    }
                    DB::rollBack();
                    return pushResponse($result, $request->pid ? 'Record updated' : "Form recorded");
                } catch (\Throwable $e) {
                    logError($e->getMessage());
                    DB::rollBack();
                    return responseMessage(status: 204, data: [], msg: STS_500);
                }
            }
            return responseMessage(data: $validator->errors()->toArray(), status: 422, msg: STS_422);
            
        } catch (\Throwable $e) {
            logError($e->getMessage());
            return pushData([], STS_500);
        }
    }


  
    public function addMeterList(Request $request){
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);
        try {
            Excel::import(new ImportMeterList,$request->file('file'));
            return back()->with('message', 'File imported successfully!');
        } catch (\Throwable $e) {
            logError($e->getMessage());
            return back()->with('error', 'Failed to import file!');
        }
    }

    // register meter assigned to team 

    public function addMeterNumber(Request $request){
        try {
            if(!$team = getUserTeam()){
                return responseMessage(status: 204, data: [], msg: 'You are not a team member');
            }
            $pid = MeterList::where(['meter_number' => $request->meter_number , 'region_pid'  => getRegionPid() ])->pluck('pid')->first();
            if(!$pid){
                return responseMessage(status: 204, data: [], msg: 'Meter Number not found');
            }
            if(!TeamAssignedMeter::where('meter_pid',$pid)->exists()){
                return responseMessage(status: 204, data: [], msg: 'Meter Number already registered');
            }
            $data = ['region_pid' => getRegionPid(), 'date' =>justDate() ,'meter_pid' => $pid ,'creator' => getUserPid(), 'team_pid' => $team];
            $result = TeamAssignedMeter::create($data);
            return pushResponse($result,'Meter Registred!!');
        } catch (\Throwable $e) {
            logError($e->getMessage());
            return responseMessage(status: 204, data: [], msg: STS_500);
        }
    }
    // register meter assigned to team 

    public function loadTeamAssignedMeters(){
        try {
            $data = MeterList::from('meter_lists as m')->join('team_assigned_meters as a','m.pid','a.meter_pid')
                                                    ->join('teams as t','t.pid','a.team_pid')
                                                    ->where(['a.region_pid' => getRegionPid() , 'supervisor'=> getUserPid()])->select('m.*')->paginate(20);
            return pushData($data);
        } catch (\Throwable $e) {
            logError($e->getMessage());
            return responseMessage(status: 204, data: [], msg: STS_500);
        }
    }


    public function recordForm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'meter_number' => ['required','exists:meter_lists',Rule::unique('installations')->where(function($q) use($request){
                $q->where('pid','<>',$request->pid);
            })],
            'preload' => 'required',
            'zone' => 'required',
            'state' => 'required',
            'doi' => 'nullable|date',
            // 'dt_name' => 'required',
            'dt_code' => 'nullable',
            'dt_type' => 'nullable',
            'upriser' => 'nullable|numeric',
            'pole' => 'required|numeric',
            'tariff' => 'required',
            'advtariff' => 'required',
            'fullname' => 'required',
            'gsm' => 'required|digits:11',
            'email' => 'nullable',
            'premises' => 'required',
            'phase' => 'required',
            'address' => 'required',
            'remark' => 'nullable',
            'feeder_33kv' => 'required',
            'feeder_11kv' => 'required',
            'meter_type' => 'required',
            'meter_brand' => 'required',
            // 'meter_tech' => 'required',
            'estimated' => 'nullable',
            'account_no' => 'required',
            'business_unit' => 'required',
            'x_cordinate' => 'required',
            'y_cordinate' => 'required',
            'installer' => 'required',
            'seal' => 'required|numeric|unique:installations',
        ],[
            'seal.unique' => 'The seal is used for another customer' ,
            'meter_number.unique' => 'Meter Number already installed' ,
            'meter_number.exists' => 'Meter Number not in Meter list' ,
            'advtariff.required' => 'Advised Tariff is required' ,
        ]);



        if (!$validator->fails()) {
            try {
                $installer = getInstallerSupervisor($request->installer);
                if(!$installer){
                    return responseMessage(status: 422, msg: 'No Supervisor assigned to selected installer team');
                }
                // logError($installer);
                $data  = [
                    'pid' => $request->pid ?? public_id(),
                    'meter_number' => $request->meter_number,
                    'preload' => $request->preload,
                    'state' => $request->state,
                    'doi' => $request->doi ?? justDate(),
                    'dt_name' => $request->dt_name,
                    'dt_type' => $request->dt_type,
                    'upriser' => $request->upriser,
                    'pole' => $request->pole,
                    'tariff' => $request->tariff,
                    'advtariff' => $request->advtariff,
                    'fullname' => $request->fullname,
                    'gsm' => $request->gsm,
                    'email' => $request->email,
                    'premises' => $request->premises,
                    'phase' => $request->phase,
                    'address' => $request->address,
                    'remark' => $request->remark,
                    'feeder_33kv' => $request->feeder_33kv,
                    'feeder_11kv' => $request->feeder_11kv,
                    'meter_type' => $request->meter_type,
                    'meter_brand' => $request->meter_brand,
                    'meter_tech' => $request->meter_tech,
                    'estimated' => $request->estimated,
                    'account_no' => $request->account_no,
                    'business_unit' => $request->business_unit,
                    'x_cordinate' => $request->x_cordinate,
                    'y_cordinate' => $request->y_cordinate,
                    'trading_zone' => $request->zone,
                    'installer' => $request->installer,
                    'supervisor' => $installer->supervisor ,
                    'team_pid' => $installer->team_pid ,
                    // 'rf_channel',
                    // 'din',
                    'seal' => $request->seal,
                    'dt_code' => $request->dt_code,
                    'creator' => getUserPid() ,
                    'region_pid' => $request->region_pid ?? getRegionPid()
                ];
                DB::beginTransaction();
                MeterList::where('meter_number', $request->meter_number)->update(['status' => 3]);

                $result = $this->addOrEditRecord($data);
                if ($result) {
                    DB::commit();
                    return pushResponse($result, $request->pid ? 'Record updated' : "Form recorded");
                }
                DB::rollBack();
                return pushResponse($result, $request->pid ? 'Record updated' : "Form recorded");
            } catch (\Throwable $e) {
                logError($e->getMessage());
                DB::rollBack();
                return responseMessage(status: 204, data: [], msg: STS_500);
            }
        }
        return responseMessage(data: $validator->errors()->toArray(), status: 422, msg: STS_422);
    }


    private function addOrEditRecord(array $data)
    {

        try {
            return Installation::updateOrCreate(['pid' => $data['pid']], $data);
        } catch (\Throwable $e) {
            logError($e->getMessage());
            return false;
        }
    }


    private function addOrUpdateComplain(array $data)
    {

        try {
            return Complain::updateOrCreate(['meter_pid' => $data['meter_pid']], $data);
        } catch (\Throwable $e) {
            logError($e->getMessage());
            return false;
        }
    }

}
