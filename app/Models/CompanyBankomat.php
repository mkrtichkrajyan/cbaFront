<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyBankomat extends Model
{
    protected $table = 'companies_bankomats';

    public function cityInfo()
    {
        return $this->hasOne('App\Models\City','id','city');
    }
}
