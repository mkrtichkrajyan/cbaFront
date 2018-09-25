<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductsMortgagesPurposeType extends Model
{
    protected $table = 'products_mortgages_purpose_types';

    public function currPurposeInfo()
    {
        return $this->hasOne('App\Models\MortgagePurposeType', 'id','purpose_type');
    }
}
