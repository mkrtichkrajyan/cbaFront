<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductsVariation extends Model
{
    protected $table = 'products_variations';


    public function providingTypeInfo()
    {
        return $this->hasOne('App\Models\ProvidingType', 'id', 'providing_type');
    }

    public function percentageTypeInfo()
    {
        return $this->hasOne('App\Models\PercentageType', 'id', 'percentage_type');
    }

    public function repaymentTypeInfo()
    {
        return $this->hasOne('App\Models\RepaymentType', 'id', 'repayment_type');
    }

}