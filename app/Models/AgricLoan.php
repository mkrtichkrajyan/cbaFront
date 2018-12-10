<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgricLoan extends Model
{
    protected $table = 'agric_loans';

    public function belongingId()
    {
        return 5;
    }

    public function ProductStatus()
    {
        return $this->hasOne('App\Models\Status', 'id', 'status');
    }

    public function providingTypeInfo()
    {
        return $this->hasOne('App\Models\ProvidingType', 'id', 'providing_type');
    }

    public function purposesInfo()
    {
        return $this->hasMany('App\Models\ProductsAgricLoansPurposeType', 'product_id', 'id');
    }




    public function percentageTypeInfo()
    {
        return $this->hasOne('App\Models\PercentageType', 'id', 'percentage_type');
    }

    public function repaymentTypeInfo()
    {
        return $this->hasOne('App\Models\RepaymentType', 'id', 'repayment_type');
    }


    public function repaymentLoanIntervalTypes()
    {
        return $this->hasMany('App\Models\ProductsRepaymentLoanIntervalType', 'product_id', 'id')->where('belonging_id', $this->belongingId());
    }

    public function repaymentPercentIntervalTypes()
    {
        return $this->hasMany('App\Models\ProductsRepaymentPercentIntervalType', 'product_id', 'id')->where('belonging_id', $this->belongingId());
    }






    public function companyInfo()
    {
        return $this->hasOne('App\Models\Company', 'id', 'company_id');
    }

    public function otherPayments()
    {
        return $this->hasMany('App\Models\ProductOtherPayment', 'product_id', 'id')->where('belonging_id', $this->belongingId())->orderBy('id', 'asc');
    }

    public function purposeInfo()
    {
        return $this->hasOne('App\Models\PurposeType', 'id', 'checked_purposes');
    }

    public function currencyInfo()
    {
        return $this->hasOne('App\Models\LoanCurrenciesType', 'id', 'currency');
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
        return $this->hasMany('App\Models\ProductsSecurityType', 'product_id', 'id')->where('belonging_id', $this->belongingId());

    }


    public function variations()
    {
        return $this->hasMany('App\Models\ProductsVariation', 'product_id', 'id')->where('belonging_id', $this->belongingId());
    }

}