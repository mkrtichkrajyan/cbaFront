<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductMoneyTransferCurrency extends Model
{
    protected $table = 'products_money_transfer_currencies';

    public function currencyInfo()
    {
        return $this->hasOne('App\Models\LoanCurrenciesType', 'id','currency_id');
    }
}
