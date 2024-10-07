<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;
    protected $fillable = [
        'region_pid',
        'name',
        'pid',
        'description',
        'unit',
        'status',
        'creator'
    ];
}
