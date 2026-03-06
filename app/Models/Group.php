<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $guarded=[];

    public function groupItems(){
        return $this->hasMany(GroupItem::class,'group_id');
    }
    
    public function prices(){
        return $this->hasMany(Price::class,'title_id');
    }
    
     public function mainGroup(){
        return $this->belongsTo(MainGroup::class,'main_group_id');
    }
}
