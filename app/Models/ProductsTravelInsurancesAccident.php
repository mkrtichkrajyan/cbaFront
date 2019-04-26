<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductsTravelInsurancesAccident extends Model
{
    protected $table = 'products_travel_insurances_accidents';

    public function currAccidentInfo()
    {
        return $this->hasOne('App\Models\InsuranceAccident', 'id', 'insurance_accident_id');
    }

}
