<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentLoan extends Model
{
    protected $table = 'student_loans';

    public function belongingId() {

        return 4;
    }

    public function ProductStatus()
    {
        return $this->hasOne('App\Models\Status', 'id','status');
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

    public function securityTypes()
    {
        return $this->hasMany('App\Models\ProductsSecurityType','product_id','id')->where('belonging_id',$this->belongingId());

    }

}
