<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class State extends Model
{
    use SoftDeletes;
    protected $guarded=[];

    public function districts(){
        return $this->hasMany(District::class,'state_id');
    }

    public function stateDistrictPricings(){
        return $this->hasMany(StateDistrictPricing::class,'state_id');
    }

    public function price(){
        return $this->hasMany(Price::class,'state_id');
    }
}
