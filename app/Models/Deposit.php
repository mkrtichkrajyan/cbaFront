<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    protected $table = 'deposits';

    public function belongingId() {

        return 7;
    }

    public function ProductStatus()
    {
        return $this->hasOne('App\Models\Status', 'id','status');
    }

    public function companyInfo()
    {
        return $this->hasOne('App\Models\Company', 'id', 'company_id');
    }
    public function depositTypeInfo()
    {
        return $this->hasOne('App\Models\DepositTypesList', 'id', 'deposit_type');
    }

    public function capitalizationsInfo()
    {
        return $this->hasMany('App\Models\ProductDepositsCapitalization', 'product_id', 'id');
    }

    public function interestRatesPaymentsInfo()
    {
        return $this->hasMany('App\Models\ProductDepositsInterestRatesPayment', 'product_id', 'id');
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
