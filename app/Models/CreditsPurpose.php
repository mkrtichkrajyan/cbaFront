<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditsPurpose extends Model
{
    protected $table = 'credits_purposes';

    public function purposeTypeInfo()
    {
        return $this->hasOne('App\Models\CreditPurposeTypes', 'id', 'purpose_type');
    }

}
