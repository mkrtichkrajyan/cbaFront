<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductDepositsInterestRatesPayment extends Model
{
    protected $table = 'product_deposits_interest_rates_payments';

    public function currInterestRatesPaymentInfo()
    {
        return $this->hasOne('App\Models\DepositInterestRatesPayment', 'id', 'interest_rate_id');
    }
}
