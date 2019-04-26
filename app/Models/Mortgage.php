<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mortgage extends Model
{
    protected $table = 'mortgages';

    public function ProductStatus()
    {
        return $this->hasOne('App\Models\Status', 'id','status');
    }

    public function belongingId() {

        return 8;
    }

    public function mainDocuments()
    {
        return $this->hasMany('App\Models\ProductsDocument', 'product_id', 'id')->where('belonging_id', $this->belongingId());
    }

    public function customDocuments()
    {
        return $this->hasMany('App\Models\ProductsCustomDocument', 'product_id', 'id')->where('belonging_id', $this->belongingId())->orderBy('id', 'asc');
    }

    public function securityTypes()
    {
        return $this->hasMany('App\Models\ProductsSecurityType','product_id','id')->where('belonging_id',$this->belongingId());

    }

    public function purposesInfo()
    {
        return $this->hasMany('App\Models\ProductsMortgagesPurposeType','product_id','id');

    }

    public function companyInfo()
    {
        return $this->hasOne('App\Models\Company', 'id', 'company_id');
    }

    public function otherPayments()
    {
        return $this->hasMany('App\Models\ProductOtherPayment', 'product_id', 'id')->where('belonging_id', $this->belongingId())->orderBy('id', 'asc');
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

    public function variations()
    {
        return $this->hasMany('App\Models\ProductsVariation', 'product_id', 'id')->where('belonging_id', $this->belongingId());
    }

}
