<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TravelInsurance extends Model
{
    protected $table = 'travel_insurances';

    public function belongingId() {

        return 12;
    }

    public function companyInfo()
    {
        return $this->hasOne('App\Models\Company', 'id', 'company_id');
    }

    public function securityTypes()
    {
        return $this->hasMany('App\Models\ProductsSecurityType','product_id','id')->where('belonging_id',$this->belongingId());

    }

    public function ProductStatus()
    {
        return $this->hasOne('App\Models\Status', 'id','status');
    }

    public function travelInsuranceAgesInfo()
    {
        return $this->hasMany('App\Models\TravelInsuranceAge','product_id','id');

    }
    public function countriesInfo()
    {
        return $this->hasMany('App\Models\ProductTravelInsuranceCountry','product_id','id')->where('belonging_id',$this->belongingId());

    }
    public function refundableExpensesInfo()
    {
        return $this->hasMany('App\Models\ProductsTravelInsurancesRefundableExpense','product_id','id');

    }
    public function accidentsInfo()
    {
        return $this->hasMany('App\Models\ProductsTravelInsurancesAccident','product_id','id');

    }
}
