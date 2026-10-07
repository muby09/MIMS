<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Installation\Installation;
use App\Models\Inventory\MeterList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class InstallationController extends Controller
{
    public function index(Request $request)
    {
        $query = Installation::query();

        if ($request->filled('region_id')) {
            $query->where('region_pid', $request->region_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('doi', [$request->date_from, $request->date_to]);
        }

        $data = $query->latest()->paginate($request->get('per_page', 50));

        return response()->json([
            'status' => 'success',
            'data' => $data->items(),
            'pagination' => [
                'total' => $data->total(),
                'per_page' => $data->perPage(),
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
            ],
        ], 200);
    }

    public function store(Request $request)
    {
        $request->merge([
            'meter_number' => normalizeMeterNumber($request->input('meter_number')),
            'gsm' => normalizeGsmNumber($request->input('gsm')),
        ]);

        $validator = Validator::make($request->all(), [
            'meter_number' => ['required', 'string', 'exists:meter_lists,meter_number'],
            'fullname' => ['required', 'string'],
            'gsm' => ['required', 'digits:11'],
            'seal' => ['required', 'string', 'unique:installations,seal'],
            'address' => ['required', 'string'],
            'region_id' => ['required', 'integer'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();

        try {
            $installation = Installation::create([
                'region_pid' => $request->region_id,
                'pid' => public_id(),
                'meter_number' => $request->meter_number,
                'preload' => $request->preload ?? 0,
                'state' => $request->state,
                'doi' => $request->installation_date ?? now()->toDateString(),
                'fullname' => $request->fullname,
                'gsm' => $request->gsm,
                'address' => $request->address,
                'feeder_33kv' => $request->feeder_33kv,
                'feeder_11kv' => $request->feeder_11kv,
                'meter_type' => $request->meter_type,
                'meter_brand' => $request->meter_brand,
                'account_no' => $request->account_no,
                'business_unit' => $request->business_unit,
                'x_cordinate' => $request->x_cordinate,
                'y_cordinate' => $request->y_cordinate,
                'installer' => $request->installer,
                'seal' => $request->seal,
                'remark' => $request->remarks,
                'creator' => $request->created_by ?? null,
            ]);

            MeterList::where('meter_number', $request->meter_number)->update(['status' => 3]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Installation recorded successfully',
                'data' => $installation,
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create installation',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        $installation = Installation::with(['region', 'team', 'feeder33kv', 'feeder11kv'])->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $installation,
        ], 200);
    }

    public function bulkUpload(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv'],
            'region_id' => ['required', 'integer'],
        ]);

        $results = [
            'total_records' => 0,
            'successful' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        $rows = Excel::toArray(new \stdClass(), $request->file('file'))[0] ?? [];
        $results['total_records'] = count($rows);

        foreach ($rows as $index => $row) {
            $row['meter_number'] = normalizeMeterNumber($row['meter_number'] ?? null);
            $row['gsm'] = normalizeGsmNumber($row['gsm'] ?? null);

            if (empty($row['meter_number'] ?? null)) {
                $results['failed']++;
                $results['errors'][] = ['row' => $index + 2, 'error' => 'Meter number is required'];
                continue;
            }

            $validator = Validator::make($row, [
                'meter_number' => ['required', 'exists:meter_lists,meter_number'],
                'seal' => ['required', 'unique:installations,seal'],
                'gsm' => ['nullable', 'digits:11'],
            ]);

            if ($validator->fails()) {
                $results['failed']++;
                $results['errors'][] = ['row' => $index + 2, 'meter_number' => $row['meter_number'] ?? null, 'error' => $validator->errors()->first()];
                continue;
            }

            $normalizedMeterNumber = normalizeMeterNumber($row['meter_number'] ?? null);

            Installation::create([
                'region_pid' => $request->region_id,
                'pid' => public_id(),
                'meter_number' => $normalizedMeterNumber,
                'fullname' => $row['fullname'] ?? null,
                'gsm' => $row['gsm'] ?? null,
                'address' => $row['address'] ?? null,
                'seal' => $row['seal'] ?? null,
                'doi' => $row['doi'] ?? now()->toDateString(),
                'creator' => $request->created_by ?? null,
            ]);

            MeterList::where('meter_number', $normalizedMeterNumber)->update(['status' => 3]);
            $results['successful']++;
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Bulk upload completed',
            'data' => $results,
        ], 201);
    }
}
