<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QRCode extends Model
{
    // use SoftDeletes;
    protected $guarded=[];
    public function mainGroup(){
        return $this->belongsTo(MainGroup::class,'main_group_id');
    }
}
