<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscription extends Model
{
    use SoftDeletes;

    protected $guarded=[];

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function packageType(){
        return $this->belongsTo(PackageType::class,'package_id');
    }
    
    public function mainGroup(){
        return $this->belongsTo(MainGroup::class,'main_group_id');
    }
}
