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
    private $header = ["s/n","meter number","phase","type","brand"];
    public function meterSummary(){
        try {
            $data = DB::table('meter_lists')->select(DB::raw('status,COUNT(id) as count'))->groupBy('status')->where('region_pid',getRegionPid())->get();
            return pushData($data);
        } catch (\Throwable $e) {
            logError($e);
            return pushData([],ERR_EMT);
        }
    }


    public function meterInstallation(){
        try {
            $daily = DB::table('installations as i')->join('meter_lists as m', 'm.meter_number', 'i.meter_number')
                        ->select(DB::raw('m.status,doi,COUNT(i.id) as count'))
                        ->groupBy('i.doi')
                        ->groupBy('m.status')->where('i.region_pid',getRegionPid())
                        ->whereMonth('i.doi',date('m'))->whereYear('i.doi',date('Y'))
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
            logError($e);
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
            logError($e);
            return pushData([],ERR_EMT);
        }
    }

    public function searchMeterList($query){
        try {
            $data = MeterList::where('meter_number', 'like', '%' . $query . '%')->paginate(20);
            return pushData($data);
            return Inertia::render('Inventory/MeterList',['data' => $data]);
        } catch (\Throwable $e) {
            logError($e);
            return pushData([],ERR_EMT);
        }
    }

    //

    public function complainList(){
        try {
            $data = Complain::with('meter')->latest()->limit(100)->paginate(20);
            return pushData($data);
        } catch (\Throwable $e) {
            logError($e);
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
            logError($e);
            return pushData([],ERR_EMT);
        }
    }


    public function installedList()
    {
        try {


            $data = Installation::where('region_pid', getRegionPid())->with('origin')->with('feeder11kv')->with('feeder33kv')->with('team')->with('region')->latest()->paginate(100);
            return pushData($data);
        } catch (\Throwable $e) {
            logError($e);
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
                })->with('origin')->with('feeder11kv')->with('feeder33kv')->with('team')->with('region')->latest()->limit(20)->paginate(10);

            return pushData($data);
        } catch (\Throwable $e) {
            logError($e);
            return pushData([], STS_500);
        }
    }

    public function filterInstalledList(Request $request)
    {
        try {
            $data = Installation::where('region_pid', getRegionPid())
                ->whereBetween('doi', [$request->from, $request->to])->with('origin')->with('feeder11kv')->with('feeder33kv')->with('team')->with('region')->latest()->paginate(100);
            return pushData($data);
        } catch (\Throwable $e) {
            logError($e);
            return pushData([], STS_500);
        }
    }

    public function exportInstalledList(Request $request)
    {
        try {
            $data = Installation::where('region_pid', getRegionPid())
                ->whereBetween('doi', [$request->from, $request->to])->with('origin')->with('feeder11kv')->with('feeder33kv')->with('team')->with('region')->latest()->get();
            return pushData($data);
        } catch (\Throwable $e) {
            logError($e);
            return pushData([], STS_500);
        }
    }


    public function addCustomerComplain(Request $request)
    {

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
                    logError($e);
                    DB::rollBack();
                    return responseMessage(status: 204, data: [], msg: STS_500);
                }
            }
            return responseMessage(data: $validator->errors()->toArray(), status: 422, msg: STS_422);

        // } catch (\Throwable $e) {
        //     logError($e);
        //     return pushData([], STS_500);
        // }
    }



    public function addMeterList(Request $request){
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);
        try {

            $path = $request->file('file'); //->getRealPath();
            $resource = maatWay(model: new MeterList, path: $path);

            $header = $resource['header'];
            $data = $resource['data'];

            if ($header !== $this->header) {
                return back()->with('warning', "Use the template without changing/touching the headings!!!");
            }
            $error = '' ;
            $k = $n = 0;
            $meterArray =[];
            $result = false;
            foreach($data as $row){
                $n++;
                if(!isset($row[1])){
                    $error .= "Meter Number on  row {$n} not inserted because meter number is empty ," . PHP_EOL;
                    continue;
                }
                if(!isset($row[2])){
                   $error .= "Meter Number on  row {$n} not inserted because meter phase is empty  ," . PHP_EOL;
                   continue;
                }
                if(!isset($row[3])){
                   $error .= "Meter Number on  row {$n} not inserted because meter type is empty  ," . PHP_EOL;
                   continue;
               }

                $normalizedMeterNumber = normalizeMeterNumber($row[1]);
                if(MeterList::where('meter_number', $normalizedMeterNumber)->exists()){
                   $error .= "Meter Number on  row {$n} not inserted because {$normalizedMeterNumber} exists  ," . PHP_EOL;
                   continue;
               }

                $result = MeterList::create([
                    'region_pid' => getRegionPid(),
                    'pid' => public_id(),
                    'meter_number' => $normalizedMeterNumber,
                    'status'  => 1,
                    'phase'  => $row[2],
                    'type'  => $row[3],
                    'brand'  => $row[4] ?? 'Technovati',
                    'creator'  => getUserPid()
                ]);
                $k++;
            }

            if($result){
                return back()->with('message', 'File imported successfully! : row inserted '.$k . PHP_EOL. $error);
            }
            return back()->with('error', 'failed to import file : row inserted ' . $k  . $error);
        } catch (\Throwable $e) {
            logError($e);
            return back()->with('error', 'Failed to import file!');
        }
    }

    public function bulkInstallationUpload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        try {
            $rows = Excel::toArray(new \stdClass(), $request->file('file'))[0] ?? [];

            if (count($rows) < 2) {
                return redirect()->route('installations')->with('error', 'The uploaded file contains no data rows.');
            }

            $headers = array_map(fn($value) => strtolower(trim(str_replace("\ufeff", '', (string) $value))), $rows[0]);
            $expectedHeaders = [
                'meter_number','fullname','gsm','account_no','address','state','zone','pole','phase','premises','tariff','advtariff','feeder_33kv','feeder_11kv','meter_type','meter_brand','x_cordinate','y_cordinate','seal','business_unit','installer','preload'
            ];

            if ($headers !== $expectedHeaders) {
                return redirect()->route('installations')->with('error', 'The file header row does not match the installation template. Please download the current template and use it without changing the column names.');
            }

            $results = [
                'total_records' => 0,
                'successful' => 0,
                'failed' => 0,
                'errors' => [],
            ];

            foreach (array_slice($rows, 1) as $index => $rowData) {
                $rowNumber = $index + 2;
                $rowValues = array_map(fn($value) => trim((string) $value), $rowData);

                if (!array_filter($rowValues, fn($value) => strlen($value) > 0)) {
                    continue;
                }

                if (count($rowValues) !== count($headers)) {
                    $rowValues = array_pad($rowValues, count($headers), null);
                    $rowValues = array_slice($rowValues, 0, count($headers));
                }

                $row = array_combine($headers, $rowValues);
                $normalizedRow = $this->normalizeBulkInstallationRow($row);
                $results['total_records']++;

                if (Installation::where('meter_number', $normalizedRow['meter_number'])->exists()) {
                    $results['failed']++;
                    $results['errors'][] = [
                        'row' => $rowNumber,
                        'meter_number' => $normalizedRow['meter_number'],
                        'error' => 'Meter number already installed',
                    ];
                    continue;
                }

                $validator = Validator::make($normalizedRow, [
                    'meter_number' => ['required', 'exists:meter_lists,meter_number'],
                    'fullname' => ['required', 'string'],
                    'gsm' => ['required', 'digits:11'],
                    'account_no' => ['required'],
                    'address' => ['required', 'string'],
                    'state' => ['required', 'exists:states,id'],
                    'zone' => ['required', 'exists:trading_zones,pid'],
                    'pole' => ['required', 'numeric'],
                    'phase' => ['required'],
                    'premises' => ['required'],
                    'tariff' => ['required'],
                    'advtariff' => ['required'],
                    'feeder_33kv' => ['required', 'exists:feeder33s,pid'],
                    'feeder_11kv' => ['required', 'exists:feeder11s,pid'],
                    'meter_type' => ['required'],
                    'meter_brand' => ['required'],
                    'x_cordinate' => ['required', 'numeric'],
                    'y_cordinate' => ['required', 'numeric'],
                    'seal' => ['required', 'numeric', 'unique:installations,seal'],
                    'business_unit' => ['required'],
                    'installer' => ['required', 'exists:user_details,user_pid'],
                    'preload' => ['nullable', 'numeric'],
                ], [
                    'seal.unique' => 'The seal has already been taken.',
                ]);

                if ($validator->fails()) {
                    $results['failed']++;
                    $error = $validator->errors()->first();
                    $results['errors'][] = [
                        'row' => $rowNumber,
                        'meter_number' => $normalizedRow['meter_number'] ?? null,
                        'error' => $error,
                    ];
                    continue;
                }

                $installation = Installation::create([
                    'region_pid' => getRegionPid(),
                    'pid' => public_id(),
                    'meter_number' => $normalizedRow['meter_number'],
                    'fullname' => $normalizedRow['fullname'],
                    'gsm' => $normalizedRow['gsm'],
                    'account_no' => $normalizedRow['account_no'],
                    'address' => $normalizedRow['address'],
                    'state' => $normalizedRow['state'],
                    'pole' => $normalizedRow['pole'],
                    'phase' => $normalizedRow['phase'],
                    'premises' => $normalizedRow['premises'],
                    'tariff' => $normalizedRow['tariff'],
                    'advtariff' => $normalizedRow['advtariff'],
                    'feeder_33kv' => $normalizedRow['feeder_33kv'],
                    'feeder_11kv' => $normalizedRow['feeder_11kv'],
                    'meter_type' => $normalizedRow['meter_type'],
                    'meter_brand' => $normalizedRow['meter_brand'],
                    'x_cordinate' => $normalizedRow['x_cordinate'],
                    'y_cordinate' => $normalizedRow['y_cordinate'],
                    'seal' => $normalizedRow['seal'],
                    'business_unit' => $normalizedRow['business_unit'],
                    'installer' => $normalizedRow['installer'],
                    'trading_zone' => $normalizedRow['zone'],
                    'preload' => $normalizedRow['preload'] ?? 25,
                    'doi' => now()->toDateString(),
                    'creator' => getUserPid(),
                ]);

                if ($installation) {
                    MeterList::where('meter_number', $normalizedRow['meter_number'])->update(['status' => 3]);
                    $results['successful']++;
                } else {
                    $results['failed']++;
                    $results['errors'][] = [
                        'row' => $rowNumber,
                        'meter_number' => $normalizedRow['meter_number'],
                        'error' => 'Unable to create installation record',
                    ];
                }
            }

            if ($results['failed'] > 0) {
                return redirect()->route('installations')
                    ->with('warning', 'Bulk upload completed with ' . $results['failed'] . ' failed rows out of ' . $results['total_records'] . '.')
                    ->with('upload_summary', $results);
            }

            return redirect()->route('installations')
                ->with('success', 'Bulk upload completed successfully. ' . $results['successful'] . ' records were imported.')
                ->with('upload_summary', $results);
        } catch (\Throwable $e) {
            logError($e);
            return redirect()->route('installations')->with('error', 'Failed to process the installation upload. Please check the file and try again.');
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
            logError($e);
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
            logError($e);
            return responseMessage(status: 204, data: [], msg: STS_500);
        }
    }


    private function normalizeBulkInstallationRow(array $row): array
    {
        $normalized = array_map(fn($value) => is_string($value) ? trim($value) : $value, $row);
        $normalized['meter_number'] = normalizeMeterNumber($normalized['meter_number'] ?? null);
        $normalized['gsm'] = normalizeGsmNumber($normalized['gsm'] ?? null);

        $stateValue = $normalized['state'] ?? null;
        $normalized['state'] = $this->resolveStateId($stateValue);

        $zoneValue = $normalized['zone'] ?? null;
        $normalized['zone'] = $this->resolveZonePid($zoneValue, $normalized['state']);

        $feeder33Value = $normalized['feeder_33kv'] ?? null;
        $normalized['feeder_33kv'] = $this->resolveFeeder33Pid($feeder33Value, $normalized['zone']);

        $feeder11Value = $normalized['feeder_11kv'] ?? null;
        $normalized['feeder_11kv'] = $this->resolveFeeder11Pid($feeder11Value, $normalized['feeder_33kv'], $normalized['zone']);

        $normalized['meter_type'] = $this->resolveMeterType($normalized['meter_type'] ?? null);
        $normalized['meter_brand'] = $this->resolveMeterBrand($normalized['meter_brand'] ?? null);
        $normalized['installer'] = $this->resolveInstallerPid($normalized['installer'] ?? null);

        if (empty($normalized['preload'] ?? null)) {
            $normalized['preload'] = 25;
        }

        return $normalized;
    }

    private function resolveStateId($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $value = trim((string) $value);

        if (is_numeric($value)) {
            return $value;
        }

        $normal = preg_replace('/\s+/', ' ', strtolower(trim($value)));

        $stateId = DB::table('states')
            ->whereRaw('LOWER(TRIM(state)) = ?', [$normal])
            ->value('id');

        if ($stateId) {
            return (string) $stateId;
        }

        $stateId = DB::table('states')
            ->whereRaw('LOWER(TRIM(state)) LIKE ?', [$normal])
            ->value('id');

        return $stateId ? (string) $stateId : null;
    }

    private function resolveZonePid($value, $stateId): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $value = trim((string) $value);

        $query = DB::table('trading_zones');

        if (is_numeric($value) || str_contains($value, '-')) {
            $query->where('pid', $value);
        } else {
            $query->whereRaw('UPPER(zone) = ?', [strtoupper($value)]);
            if ($stateId) {
                $query->where('state_id', $stateId);
            }
        }

        return $query->value('pid');
    }

    private function resolveFeeder33Pid($value, $zonePid): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $value = trim((string) $value);

        $pid = DB::table('feeder33s')->where('pid', $value)->value('pid');
        if ($pid) {
            return $pid;
        }

        $query = DB::table('feeder33s')->whereRaw('UPPER(name) = ?', [strtoupper($value)]);
        if ($zonePid) {
            $query->where('zone_pid', $zonePid);
        }

        return $query->value('pid');
    }

    private function resolveFeeder11Pid($value, $feeder33Pid, $zonePid): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $value = trim((string) $value);

        $pid = DB::table('feeder11s')->where('pid', $value)->value('pid');
        if ($pid) {
            return $pid;
        }

        $query = DB::table('feeder11s')->whereRaw('UPPER(name) = ?', [strtoupper($value)]);
        if ($feeder33Pid) {
            $query->where('feeder_33_pid', $feeder33Pid);
        }
        if ($zonePid) {
            $query->where('zone_pid', $zonePid);
        }

        return $query->value('pid');
    }

    private function resolveMeterType($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $value = trim((string) $value);

        $record = DB::table('meter_types')->whereRaw('UPPER(type) = ?', [strtoupper($value)])->first();

        return $record ? $record->type : strtoupper($value);
    }

    private function resolveMeterBrand($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $value = trim((string) $value);

        $record = DB::table('meter_brands')->whereRaw('UPPER(brand) = ?', [strtoupper($value)])->first();

        return $record ? $record->brand : strtoupper($value);
    }

    private function resolveInstallerPid($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $value = trim((string) $value);

        $pid = DB::table('user_details')->where('user_pid', $value)->value('user_pid');
        if ($pid) {
            return $pid;
        }

        $pid = DB::table('user_details')->whereRaw('LOWER(username) = ?', [strtolower($value)])->value('user_pid');
        if ($pid) {
            return $pid;
        }

        return DB::table('users')->whereRaw('LOWER(email) = ?', [strtolower($value)])->value('pid');
    }

    public function recordForm(Request $request)
    {
        $request->merge([
            'meter_number' => normalizeMeterNumber($request->input('meter_number')),
            'gsm' => normalizeGsmNumber($request->input('gsm')),
        ]);

        $validator = Validator::make($request->all(), [
            'pid' => ['nullable', 'exists:installations,pid'],
            'meter_number' => ['required', 'exists:meter_lists,meter_number', Rule::unique('installations', 'meter_number')->ignore($request->input('pid'), 'pid')],
            'preload' => ['required', 'numeric', 'min:0'],
            'zone' => ['required'],
            'state' => ['required'],
            'doi' => 'nullable|date',
            // 'dt_name' => 'required',
            'dt_code' => 'nullable',
            'dt_type' => 'nullable',
            'upriser' => 'nullable|numeric',
            'pole' => 'required|numeric',
            'tariff' => ['required', 'string'],
            'advtariff' => ['required', 'string'],
            'fullname' => ['required', 'string'],
            'gsm' => 'required|digits:11',
            'email' => ['nullable', 'email'],
            'premises' => ['required', 'string'],
            'phase' => ['required', 'in:Red,Yellow,Blue'],
            'address' => ['required', 'string'],
            'remark' => ['nullable', 'string'],
            'feeder_33kv' => 'required',
            'feeder_11kv' => 'required',
            'meter_type' => 'required',
            'meter_brand' => 'required',
            // 'meter_tech' => 'required',
            'estimated' => 'nullable|numeric',
            'account_no' => ['required', 'string'],
            'business_unit' => ['required', 'string'],
            'service_center' => ['nullable', 'string', 'max:255'],
            'x_cordinate' => ['required', 'numeric'],
            'y_cordinate' => ['required', 'numeric'],
            'installer' => 'required',
            'seal' => ['required', 'numeric', Rule::unique('installations', 'seal')->ignore($request->input('pid'), 'pid')],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ],[
            'seal.unique' => 'The seal is used for another customer' ,
            'meter_number.unique' => 'Meter Number already installed' ,
            'meter_number.exists' => 'Meter Number not in Meter list' ,
            'advtariff.required' => 'Advised Tariff is required' ,
        ]);



        if (!$validator->fails()) {
            $storedPhoto = null;
            $oldPhoto = null;

            try {
                $installer = getInstallerSupervisor($request->installer);
                if(!$installer){
                    return responseMessage(status: 422, msg: 'No Supervisor assigned to selected installer team');
                }
                DB::beginTransaction();

                if ($request->filled('pid')) {
                    $oldPhoto = Installation::where('pid', $request->pid)->value('photo');
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
                    'service_center' => $request->service_center,
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

                if ($request->file('photo')) {
                    $storedPhoto = $request->file('photo')->store('files/installations', 'public');
                    if (!$storedPhoto) {
                        throw new \RuntimeException('The installation photo could not be stored.');
                    }
                    $data['photo'] = $storedPhoto;
                }

                MeterList::where('meter_number', $request->meter_number)->update(['status' => 3]);

                $result = $this->addOrEditRecord($data);
                DB::commit();

                if ($storedPhoto && $oldPhoto && $oldPhoto !== $storedPhoto) {
                    try {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($oldPhoto);
                    } catch (\Throwable $cleanupError) {
                        logError($cleanupError);
                    }
                }

                return pushResponse($result, $request->pid ? 'Record updated' : "Form recorded");
            } catch (\Throwable $e) {
                logError($e);
                if (DB::transactionLevel() > 0) {
                    DB::rollBack();
                }
                if ($storedPhoto) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($storedPhoto);
                }
                return responseMessage(status: 204, data: [], msg: STS_500);
            }
        }
        return responseMessage(data: $validator->errors()->toArray(), status: 422, msg: STS_422);
    }


    private function addOrEditRecord(array $data)
    {
        return Installation::updateOrCreate(['pid' => $data['pid']], $data);
    }


    private function addOrUpdateComplain(array $data)
    {

        try {
            return Complain::updateOrCreate(['meter_pid' => $data['meter_pid']], $data);
        } catch (\Throwable $e) {
            logError($e);
            return false;
        }
    }

}
