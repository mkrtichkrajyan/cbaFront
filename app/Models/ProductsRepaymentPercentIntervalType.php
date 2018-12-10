<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductsRepaymentPercentIntervalType extends Model
{
    protected $table = 'products_repayment_percent_interval_types';

    public function currIntervalTypeInfo()
    {
        return $this->hasOne('App\Models\RepaymentPercentIntervalType', 'id','repayment_percent_interval_type_id');
    }
}
