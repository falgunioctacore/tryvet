<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MainGroup extends Model
{
    use SoftDeletes;

    protected $guarded=[];

    public function groups(){
        return $this->hasMany(Group::class,'main_group_id');
    }
    
    public function prices(){
        return $this->hasMany(Price::class,'main_group_id');
    }
    
    // public function mainGroup(){
    //     return $this->hasMany(MainGroup::class,'main_group_id');
    // }
    
    public function qrCodes(){
        return $this->hasMany(QrCode::class,'main_group_id');
    }
    
    public function packageTypes(){
        return $this->hasMany(PackageType::class,'main_group_id');
    }
}
