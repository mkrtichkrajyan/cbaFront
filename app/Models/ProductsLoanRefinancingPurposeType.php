<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductsLoanRefinancingPurposeType extends Model
{
    protected $table = 'products_loan_refinancing_purpose_types';

    public function currPurposeInfo()
    {
        return $this->hasOne('App\Models\LoanRefinancingPurposeType', 'id','purpose_type');
    }
}
