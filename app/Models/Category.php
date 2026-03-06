<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;
    protected $table="categories";
    protected $guarded=[];

    public function prices(){
        return $this->hasMany(Price::class,'category_id');
    }
}
