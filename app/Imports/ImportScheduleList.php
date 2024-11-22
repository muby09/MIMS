<?php

namespace App\Imports;

use App\Models\Region\Schedule;
use Maatwebsite\Excel\Concerns\ToModel;
// use Maatwebsite\Excel\HeadingRowImport;
use Maatwebsite\Excel\Concerns\WithGroupedHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImportScheduleList implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    
    public function model(array $row)
    {

        return new Schedule([
            'region_pid' => getRegionPid(),
            'pid' => public_id(),
            // 'state' => $row['state'],
            // 'region' => $row['region'],
            // 'meter_number' => $row['region'],
            'allocated_meter_number' => $row['allocated_meter_number'],
            'account_number' => $row['account_number'],
            'meter_type' => $row['meter_type'],
            'map' => $row['map'],
            'customer_name' => $row['customer_name'],
            'address' => $row['address'],
            'phone_no' => $row['phone_no'],
            'feeder_name' => $row['feeder_name'],
            'business_unit' => $row['business_unit'],
            'state' => $row['state'],
            'total_billings' => $row['total_billings'],
            'total_settlement' => $row['total_settlement'],
            // 'account_name' => $row['account_name'],
            // 'contact' => $row['contact_information'],
            // 'feeder_33' => $row['33kv_feeder'],
            // 'feeder_11' => $row['11kv_feeder'],
            // 'dt_name' => $row['dt_name'],
            // 'band' => $row['band'],
            // 'load' => $row['expected_load_in_amps'],
            // 'meter_type' => $row['meter_type'],
            // 'connection_status' => $row['connection_status'],
            // 'address' => $row['address'],

            // 'account_number' => $row['account_number'],
            // 'account_name' => $row['account_name'],
            // 'contact' => $row['contact_information'],
            // 'feeder_33' => $row['33kv_feeder'],
            // 'feeder_11' => $row['11kv_feeder'],
            // 'dt_name' => $row['dt_name'],
            // 'band' => $row['band'],
            // 'load' => $row['expected_load_in_amps'],
            // 'meter_type' => $row['meter_type'],
            // 'connection_status' => $row['connection_status'],
            // 'address' => $row['address'],
           
            'creator'  => getUserPid()
        ]);
          
    }
}
