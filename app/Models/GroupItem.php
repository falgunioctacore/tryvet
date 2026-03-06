<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupItem extends Model
{
    protected $guarded=[];

    public function group(){
        return $this->belongsTo(GroupItem::class,'group_id');
    }

    public function district(){
        return $this->belongsTo(District::class,'district_id');
    }
}
