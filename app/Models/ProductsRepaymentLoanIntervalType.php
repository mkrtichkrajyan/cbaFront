<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductsRepaymentLoanIntervalType extends Model
{
    protected $table = 'products_repayment_loan_interval_types';

    public function currIntervalTypeInfo()
    {
        return $this->hasOne('App\Models\RepaymentLoanIntervalType', 'id','repayment_loan_interval_type_id');
    }
}
