<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StateDistrictPricing extends Model
{
    use SoftDeletes;
    protected $guarded=[];

    public function state(){
        return $this->belongsTo(State::class,'state_id');
    }

    public function district(){
        return $this->belongsTo(District::class,'district_id');
    }

    
}
