<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class District extends Model
{
    use SoftDeletes;
    protected $guarded=[];

    public function state(){
        return $this->belongsTo(State::class);
    }
    
    public function stateDistrictPrice(){
        return $this->hasOne(StateDistrictPricing::class,'disctrict_id');
    }

    public function priceItems(){
        return $this->hasMany(PriceItem::class,'district_id');
    }

    public function groupItems(){
        return $this->hasMany(GroupItem::class,'district_id');
    }

}
