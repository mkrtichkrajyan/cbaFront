<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Belonging extends Model
{
    protected $table = 'belongings';

//    public function company()
//    {
//        return $this->hasMany('App\Models\Company', 'belonging_id','name');
//    }

    public function productsByBelongingInfo()
    {
        return $this->hasMany('App\Models\ProductByBelongingsView','belonging_id','id');

    }
}
