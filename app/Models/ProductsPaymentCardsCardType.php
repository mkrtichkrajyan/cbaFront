<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductsPaymentCardsCardType extends Model
{
    protected $table = 'products_payment_cards_card_types';


    public function currCardTypeInfo()
    {
        return $this->hasOne('App\Models\PaymentCardProductType', 'id','card_type_id');
    }
}
