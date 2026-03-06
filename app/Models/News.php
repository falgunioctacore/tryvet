<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class News extends Model
{
    use SoftDeletes;
    protected $table="news";

    protected $guarded=[];

    // public function setUserIdAttribute($value){
    //     $this->attributes['user_id']=Auth::id();
    // }

    protected function userId(){
        return Attribute::make(
            set:fn(string $value)=>Auth::id(),
        );
    }
}
