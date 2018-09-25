<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductMoneyTransferSystem extends Model
{
    protected $table = 'products_money_transfer_systems';

    public function moneyTransferSystemInfo()
    {
        return $this->hasOne('App\Models\LoanCurrenciesType', 'id','currency_id');
    }
}
