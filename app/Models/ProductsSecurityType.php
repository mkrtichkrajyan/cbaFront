<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductsSecurityType extends Model
{
    protected $table = 'products_security_types';

    public function securityTypeInfo()
    {
        return $this->hasOne('App\Models\SecurityType', 'id','security_type');
    }
}
