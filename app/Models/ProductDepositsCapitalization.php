<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductDepositsCapitalization extends Model
{
    protected $table = 'product_deposits_capitalizations';

    public function currCapitalizationInfo()
    {
        return $this->hasOne('App\Models\DepositCapitalizationsList', 'id', 'type_id');
    }
}
