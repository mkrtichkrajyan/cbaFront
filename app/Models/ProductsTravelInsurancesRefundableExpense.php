<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductsTravelInsurancesRefundableExpense extends Model
{
    protected $table = 'products_travel_insurances_refundable_expenses';

    public function currRefundableExpenseInfo()
    {
        return $this->hasOne('App\Models\RefundableExpense', 'id', 'refundable_expense_id');
    }

}
