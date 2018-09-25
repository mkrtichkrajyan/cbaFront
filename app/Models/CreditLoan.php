<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditLoan extends Model
{
    protected $table = 'credit_loans';

    public function ProductStatus()
    {
        return $this->hasOne('App\Models\Status', 'id','status');
    }

    public function companyInfo()
    {
        return $this->hasOne('App\Models\Company', 'id', 'company_id');
    }
    public function ProductPurposes()
    {
        return $this->hasMany('App\Models\CreditsPurpose', 'product_id','id');
    }
    public function loanTermFromPeriodicityTypeInfo()
    {
        return $this->hasOne('App\Models\TimeType', 'id', 'loan_term_from_periodicity_type');
    }
    public function loanTermToPeriodicityTypeInfo()
    {
        return $this->hasOne('App\Models\TimeType', 'id', 'loan_term_to_periodicity_type');
    }

    public function belongingId() {

        return 3;
    }

    public function securityTypes()
    {
        return $this->hasMany('App\Models\ProductsSecurityType','product_id','id')->where('belonging_id',$this->belongingId());

    }

}
