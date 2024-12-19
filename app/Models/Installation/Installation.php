<?php

namespace App\Models\Installation;

use App\Models\State;
use App\Models\Admin\Feeder\Feeder11;
use App\Models\Admin\Feeder\Feeder33;
use App\Models\Region\Region;
use App\Models\Region\Team;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Installation extends Model
{
    use HasFactory;
    protected $fillable = [
        'region_pid', 'pid', 'meter_number', 'preload', 'state' , 'doi', 'dt_name', 'dt_type', 'upriser' , 'pole' , 'tariff' , 'advtariff' , 'fullname' ,
        'gsm' , 'email' , 'premises' ,  'phase', 'address', 'remark', 'feeder_33kv',  'feeder_11kv', 'meter_type',  'meter_brand', 'meter_tech' ,
        'estimated' ,  'account_no' ,  'business_unit', 'x_cordinate', 'y_cordinate' ,  'installer' , 'supervisor' , 'rf_channel' ,
        'din' ,  'seal' ,  'dt_code' , 'trading_zone', 'team_pid'
    ];




    protected $appends = ['date'];


    public function feeder33kv()
    {
        return $this->belongsTo(Feeder33::class, 'feeder_33kv', 'pid');
    }

    public function feeder11kv()
    {
        return $this->belongsTo(Feeder11::class, 'feeder_11kv', 'pid');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_pid', 'id');
    }

    public function origin()
    {
        return $this->belongsTo(State::class, 'state', 'id');
    }
    public function region()
    {
        return $this->belongsTo(Region::class, 'region_pid', 'pid');
    }


    protected function date(): Attribute
    {
        return Attribute::make(
            get: fn() => isset($this->attributes['doi']) ? formatDate($this->attributes['doi']) : null
        );
    }




}
