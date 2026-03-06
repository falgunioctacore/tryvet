<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    protected $guarded=[];

    public function priceItems(){
        return $this->hasMany(PriceItem::class,'price_id');
    }

    public function state(){
        return $this->belongsTo(State::class,'state_id');
    }

    public function group(){
        return $this->belongsTo(Group::class,'title_id');
    }
    
     public function category(){
        return $this->belongsTo(Category::class,'category_id');
    }
    
    public function mainGroup(){
        return $this->belongsTo(MainGroup::class,'main_group_id');
    }
}
 