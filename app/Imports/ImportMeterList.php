<?php

namespace App\Imports;

use App\Models\Inventory\MeterList;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImportMeterList implements ToModel,WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new MeterList([
            'region_pid' => getRegionPid(),
            'pid' => public_id(),
            'meter_number' => normalizeMeterNumber($row['meter_number'] ?? null),
            'status'  => $row['status'] ?? 1 ,
            'phase'  => $row['phase'],
            'type'  => $row['type'],
            'brand'  => $row['brand'] ?? 'Technovati',
            'creator'  => getUserPid()
        ]);
    }
}
