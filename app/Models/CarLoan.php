<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarLoan extends Model
{
    protected $table = 'car_loans';

    public function belongingId()
    {

        return 1;
    }

    public function providingTypeInfo()
    {
        return $this->hasOne('App\Models\ProvidingType', 'id', 'providing_type');
    }

    public function repaymentTypeInfo()
    {
        return $this->hasOne('App\Models\RepaymentType', 'id', 'checked_repayment_types');
    }

    public function ProductStatus()
    {
        return $this->hasOne('App\Models\Status', 'id', 'status');
    }

    public function CarSalons()
    {
        return $this->hasOne('App\Models\CarLoanCarSalon', 'product_id', 'id');
    }

    public function securityTypes()
    {
        return $this->hasMany('App\Models\ProductsSecurityType', 'product_id', 'id')->where('belonging_id', $this->belongingId());
    }

    public function companyInfo()
    {
        return $this->hasOne('App\Models\Company', 'id', 'company_id');
    }

    public function carInfo()
    {
        return $this->hasOne('App\Models\CarType', 'id', 'car_type');
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
