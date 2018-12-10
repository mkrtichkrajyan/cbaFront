<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TravelInsurance extends Model
{
    protected $table = 'travel_insurances';

    public function belongingId()
    {
        return 12;
    }

    public function ProductStatus()
    {
        return $this->hasOne('App\Models\Status', 'id', 'status');
    }

    public function companyInfo()
    {
        return $this->hasOne('App\Models\Company', 'id', 'company_id');
    }

    public function securityTypes()
    {
        return $this->hasMany('App\Models\ProductsSecurityType', 'product_id', 'id')->where('belonging_id', $this->belongingId());
    }

    public function countriesInfo()
    {
        return $this->hasMany('App\Models\ProductTravelInsuranceCountry', 'product_id', 'id')->where('belonging_id', $this->belongingId());
    }

    public function travelInsuranceTerms()
    {
        return $this->hasMany('App\Models\TravelInsuranceTerm', 'product_id', 'id')->orderBy('id', 'asc');
    }

    public function travelInsuranceAgesInfo()
    {
        return $this->hasMany('App\Models\TravelInsuranceAge', 'product_id', 'id')->orderBy('id', 'asc');
    }

    public function travelInsuranceMoneyCurrencyTariffs()
    {
        return $this->hasMany('App\Models\TravelInsuranceMoneyCurrencyTariff', 'product_id', 'id')->orderBy('id', 'asc');
    }

    public function accidentsInfo()
    {
        return $this->hasMany('App\Models\ProductsTravelInsurancesAccident', 'product_id', 'id');
    }

    public function refundableExpensesInfo()
    {
        return $this->hasMany('App\Models\ProductsTravelInsurancesRefundableExpense', 'product_id', 'id');
    }


    public function nonRecoverableAmountInfo()
    {
        return $this->hasOne('App\Models\NonRecoverableExpensesAnswer', 'id', 'non_recoverable_amount');
    }


    public function variations()
    {
        return $this->hasMany('App\Models\TravelInsurancesVariation', 'product_id', 'id')->where('belonging_id', $this->belongingId());
    }
}