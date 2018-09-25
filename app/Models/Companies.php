<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Companies extends Model
{

    public function companiesTypes()
    {
        return $this->belongsTo('App\Models\CompaniesTypes');
    }
    public function companiesBelongings()
    {
        return $this->belongsTo('App\Models\Belonging');
    }
}
