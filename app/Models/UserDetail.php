<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserDetail extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_pid', 'firstname', 'lastname' , 'othername' , 'path' , 'gender', 'religion' , 'dob' , 'state_of_origin' ,
        'lga_of_origin' ,  'address' , 'creator' , 'region_pid','username', 'gsm' , 'path'
        ];

    public function setGsmAttribute($value): void
    {
        $this->attributes['gsm'] = normalizeGsmNumber($value);
    }

    protected $appends = ['date'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_pid', 'pid');
    }
    public function origin()
    {
        return $this->belongsTo(State::class, 'state_of_origin', 'id');
    }
    public function lga()
    {
        return $this->belongsTo(StateLga::class, 'lga_of_origin', 'id');
    }

    protected function date() : Attribute
    {
        return Attribute::make(
            get:fn() => isset($this->attributes['dob']) ? formatDate($this->attributes['dob']) : null
        );
    }
    
    protected function firstname() : Attribute
    {
        return Attribute::make(
            get:fn($val) => ucfirst($val) 
        );
    }


    protected function lastname() : Attribute
    {
        return Attribute::make(
            get:fn($val) => ucfirst($val) 
        );
    }














}
