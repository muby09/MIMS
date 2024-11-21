<?php

namespace App\Models\Region;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;
    protected $fillable = [
        'region_pid' , 'pid' , 'allocated_meter_number' , 'account_number' , 'meter_type' , 'map' , 'customer_name' ,
        'address' , 'phone_no' ,'feeder_name' , 'business_unit' , 'state' , 'total_billings' , 'total_settlement',
        'creator'
    ];

    public function region()
    {
        return $this->belongsTo(Region::class, 'region_pid', 'pid');
    }
}
