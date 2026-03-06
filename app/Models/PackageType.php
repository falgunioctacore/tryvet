<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PackageType extends Model
{
    use SoftDeletes;
    protected $guarded=[];

    public function subscriptions(){
        return $this->hasMany(Subscription::class,'package_id');
    }
    
     public function mainGroup(){
        return $this->belongsTo(MainGroup::class,'main_group_id');
    }

}
