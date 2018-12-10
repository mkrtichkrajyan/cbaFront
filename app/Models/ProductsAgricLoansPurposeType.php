<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductsAgricLoansPurposeType extends Model
{
    protected $table = 'products_agric_loans_purpose_types';

    public function currPurposeInfo()
    {
        return $this->hasOne('App\Models\PurposeType', 'id','purpose_type');
    }
}
