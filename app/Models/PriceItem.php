<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PriceItem extends Model
{
    use SoftDeletes;
    protected $guarded=[];

    public function price(){
        return $this->belongsTo(Price::class,'price_id');
    }

    public function district(){
        return $this->belongsTo(District::class,'district_id');
    }
}
