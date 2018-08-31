<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductsPaymentCardsCurrency extends Model
{
    protected $table = 'products_payment_cards_currencies';

    public function currCurrencyInfo()
    {
        return $this->hasOne('App\Models\PaymentCardCurrency', 'id','currency_id');
    }
}
