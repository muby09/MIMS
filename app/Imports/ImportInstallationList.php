<?php

namespace App\Imports;

use App\Models\Installation\Installation;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Validation\Rule;

class ImportInstallationList implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Skip empty rows
        if (empty($row['meter_number'])) {
            return null;
        }

        // Get installer and supervisor info
        $installer = getInstallerSupervisor($row['installer']);

        return new Installation([
            'region_pid' => getRegionPid(),
            'pid' => public_id(),
            'meter_number' => $row['meter_number'],
            'fullname' => $row['fullname'],
            'gsm' => $row['gsm'],
            'account_no' => $row['account_no'],
            'address' => $row['address'],
            'state' => $row['state'],
            'pole' => $row['pole'],
            'phase' => $row['phase'],
            'premises' => $row['premises'],
            'tariff' => $row['tariff'],
            'advtariff' => $row['advtariff'],
            'feeder_33kv' => $row['feeder_33kv'],
            'feeder_11kv' => $row['feeder_11kv'],
            'meter_type' => $row['meter_type'],
            'meter_brand' => $row['meter_brand'],
            'x_cordinate' => $row['x_cordinate'],
            'y_cordinate' => $row['y_cordinate'],
            'seal' => $row['seal'],
            'business_unit' => $row['business_unit'],
            'installer' => $row['installer'],
            'supervisor' => $installer?->supervisor ?? null,
            'team_pid' => $installer?->team_pid ?? null,
            'preload' => $row['preload'] ?? 25,
            'doi' => justDate(),
            'creator' => getUserPid(),
        ]);
    }
}
