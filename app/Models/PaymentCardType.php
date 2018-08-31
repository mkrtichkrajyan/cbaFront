<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentCardType extends Model
{
    protected $table = 'products_payment_cards_types';


    public function currTypeInfo()
    {
        return $this->hasOne('App\Models\PaymentCardType', 'id','type_id');
    }
}
