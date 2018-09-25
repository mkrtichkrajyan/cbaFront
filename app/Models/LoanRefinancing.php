<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanRefinancing extends Model
{
    protected $table = 'loan_refinancing';

    public function belongingId() {

        return 11;
    }

    public function securityTypes()
    {
        return $this->hasMany('App\Models\ProductsSecurityType','product_id','id')->where('belonging_id',$this->belongingId());

    }

    public function ProductStatus()
    {
        return $this->hasOne('App\Models\Status', 'id','status');
    }

    public function purposesInfo()
    {
        return $this->hasMany('App\Models\ProductsLoanRefinancingPurposeType','product_id','id');

    }

    public function companyInfo()
    {
        return $this->hasOne('App\Models\Company', 'id', 'company_id');
    }

    public function loanTermFromPeriodicityTypeInfo()
    {
        return $this->hasOne('App\Models\TimeType', 'id', 'loan_term_from_periodicity_type');
    }

    public function loanTermToPeriodicityTypeInfo()
    {
        return $this->hasOne('App\Models\TimeType', 'id', 'loan_term_to_periodicity_type');
    }
}
