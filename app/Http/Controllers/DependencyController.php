<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Region\Schedule;
use Illuminate\Validation\Rule;
use App\Imports\ImportScheduleList;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Admin\Meter\MeterType;
use App\Models\Admin\Meter\MeterBrand;
use Maatwebsite\Excel\HeadingRowImport;
use App\Models\Admin\Feeder\TradingZone;
use Illuminate\Support\Facades\Validator;

class DependencyController extends Controller
{
    private $header = ["sn", "allocated_meter_number", "account_number", "meter_type", "map", "customer_name", "address", "phone_no", "feeder_name", "business_unit", "state", "total_billings", "total_settlement"];
    private $headings  = "S/N,	ALLOCATED METER NUMBER,	ACCOUNT NUMBER,	METER TYPE,	MAP,	CUSTOMER NAME,	ADDRESS	PHONE NO,	FEEDER NAME,	Business Unit,	STATE,	Total Billings, 	Total Settlement";
    
    public function index(){
        
        try {
            return Inertia::render('SuperAdmin/Dependency');
        } catch (\Throwable $e) {
            logError($e->getMessage());
        }
    }


    public function searchScheduleList($query)
    {

        try {
            $data = Schedule::where('allocated_meter_number', 'like', '%' . $query . '%')
                ->orWhere('account_number', 'like', '%' . $query . '%')
                ->orWhere('customer_name', 'like', '%' . $query . '%')
                ->orWhere('phone_no', 'like', '%' . $query . '%')->limit(15)->get();
            return pushData($data);
        } catch (\Throwable $e) {
            logError($e->getMessage());
        }
    }


    
    public function schedules(){
        
        try {
            // logError(getRegionPid());
            $data = Schedule::with('region')->where('region_pid',getRegionPid())->paginate(50);
            return Inertia::render('Region/Schedule', ['data' => $data]);
        } catch (\Throwable $e) {
            logError($e->getMessage());
        }
    }

    public function scheduleList(){
        
        try {
            $data = Schedule::with('region')->where('region_pid',getRegionPid())->limit(50)->get();
           return pushData($data);
        } catch (\Throwable $e) {
            logError($e->getMessage());
        }
    }

    public function uploadSchedule(Request $request){
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);
        try {
            $headings = (new HeadingRowImport)->toArray($request->file('file'));
            if($headings[0][0] !== $this->header){
                return back()->with('warning', "Use the template without changing/touching the headings!!!". PHP_EOL." {$this->headings}" );
            }
            Excel::import(new ImportScheduleList, $request->file('file'));
            return back()->with('message', 'File imported successfully!');
        } catch (\Throwable $e) {
            logError($e->getMessage());
            return back()->with('error', 'Failed to import file!');
        }
    }


    public function loadTradingZone()
    {

        try {
            $data = TradingZone::with('state')->get();
            return pushData($data, 'Zones loaded');
        } catch (\Throwable $e) {
            logError($e->getMessage());
            return pushData([], STS_500);
        }
    }
    
    public function loadMeterTypes()
    {

        try {
            $data = MeterType::get();
            return pushData($data, 'Zones loaded');
        } catch (\Throwable $e) {
            logError($e->getMessage());
            return pushData([], STS_500);
        }
    }

    public function loadMeterBrands()
    {

        try {
            $data = MeterBrand::get();
            return pushData($data, 'Zones loaded');
        } catch (\Throwable $e) {
            logError($e->getMessage());
            return pushData([], STS_500);
        }
    }

    public function createTradingZone(Request $request){

        $validator =  Validator::make($request->all(), [
            'state_id' => 'required',
            'zone' => ['required','string',Rule::unique('trading_zones')->where(function($q) use($request){
                $q->where('pid', '<>', $request->pid);
            })],
        ]);

        if (!$validator->fails()) {
            try {
                $data = [
                    'zone' => $request->zone,
                    'state_id' => $request->state_id,
                    'pid' => $request->pid ?? public_id(),
                ];
                $result = $this->addOrEditZone($data);
                return pushResponse($result, $request->pid ? 'Trading Zone Updated' : 'Trading Zone Added');
            } catch (\Throwable $e) {
                logError($e->getMessage());
                return responseMessage(status: 204, data: [], msg: STS_500);
            }
        }
        return responseMessage(data: $validator->errors()->toArray(), status: 422, msg: STS_422);
    }

    public function createMeterBrand(Request $request){

        $validator =  Validator::make($request->all(), [
            'brand' => ['required','string',Rule::unique('meter_brands')->where(function($q) use($request){
                $q->where('id', '<>', $request->id);
            })],
        ]);

        if (!$validator->fails()) {
            try {
                $data = [
                    'brand' => strtoupper($request->brand),
                    'id' => $request->id ?? null,
                ];
                $result = $this->addOrEditBrand($data);
                return pushResponse($result, $request->pid ? 'Meter Brand Updated' : 'Meter Brand Added');
            } catch (\Throwable $e) {
                logError($e->getMessage());
                return responseMessage(status: 204, data: [], msg: STS_500);
            }
        }
        return responseMessage(data: $validator->errors()->toArray(), status: 422, msg: STS_422);
    }

    public function createMeterType(Request $request){

        $validator =  Validator::make($request->all(), [
            'type' => ['required','string',Rule::unique('meter_types')->where(function($q) use($request){
                $q->where('id', '<>', $request->id);
            })],
        ]);

        if (!$validator->fails()) {
            try {
                $data = [
                    'type' => strtoupper($request->type),
                    'id' => $request->id ?? null,
                ];
                $result = $this->addOrEditType($data);
                return pushResponse($result, $request->pid ? 'Meter Type Updated' : 'Meter Type Added');
            } catch (\Throwable $e) {
                logError($e->getMessage());
                return responseMessage(status: 204, data: [], msg: STS_500);
            }
        }
        return responseMessage(data: $validator->errors()->toArray(), status: 422, msg: STS_422);
    }


    private function addOrEditZone(array $data){
        try {
            return TradingZone::updateOrCreate(['pid' => $data['pid'] ], $data);
        } catch (\Throwable $e) {
            logError($e->getMessage());
            return false;
        }
    }

    private function addOrEditBrand(array $data){
        try {
            return MeterBrand::updateOrCreate(['id' => $data['id'] ], $data);
        } catch (\Throwable $e) {
            logError($e->getMessage());
            return false;
        }
    }

    private function addOrEditType(array $data){
        try {
            return MeterType::updateOrCreate(['id' => $data['id'] ], $data);
        } catch (\Throwable $e) {
            logError($e->getMessage());
            return false;
        }
    }

}
