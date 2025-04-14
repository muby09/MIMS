<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MeterList extends Model
{
    use HasFactory;
    protected $fillable = [
        'region_pid' , 'pid' ,'meter_number' ,'status' ,'phase' ,'type' ,'user_pid','brand'
    ];

    private $status  =  ['', ' In store', ' Taken Out', ' Installed', 'Faulty'];

    protected $appends  = ['meter_status'];

    protected function meterStatus(): Attribute
    {
        return Attribute::make(
            get: fn() => isset($this->attributes['status']) ? $this->status[$this->attributes['status']] : null
        );
    }
}
