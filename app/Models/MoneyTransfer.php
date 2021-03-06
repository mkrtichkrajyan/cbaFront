<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MoneyTransfer extends Model
{
    protected $table = 'money_transfers';

    public function belongingId()
    {
        return 10;
    }

    public function ProductStatus()
    {
        return $this->hasOne('App\Models\Status', 'id', 'status');
    }

    public function companyInfo()
    {
        return $this->hasOne('App\Models\Company', 'id', 'company_id');
    }

    public function transferType()
    {
        return $this->hasOne('App\Models\TransferType', 'id', 'transfer_type');
    }

    public function securityTypes()
    {
        return $this->hasMany('App\Models\ProductsSecurityType', 'product_id', 'id')->where('belonging_id', $this->belongingId());
    }

    public function transferSystemsInfo()
    {
        return $this->hasMany('App\Models\ProductMoneyTransferSystem', 'product_id', 'id')->where('belonging_id', $this->belongingId());
    }

    public function countriesInfo()
    {
        return $this->hasMany('App\Models\ProductMoneyTransferCountry', 'product_id', 'id')->where('belonging_id', $this->belongingId());
    }

    public function currenciesInfo()
    {
        return $this->hasMany('App\Models\ProductMoneyTransferCurrency', 'product_id', 'id')->where('belonging_id', $this->belongingId());
    }

    public function currencyInfo()
    {
        return $this->hasOne('App\Models\MoneyTransferCurrenciesAllType', 'id', 'currencies');
    }

    public function transferBanksInfo()
    {
        return $this->hasMany('App\Models\ProductMoneyTransferBank', 'product_id', 'id')->where('belonging_id', $this->belongingId());
    }

    public function moneyTransferAmountsTermsCommissionFee()
    {
        return $this->hasMany('App\Models\MoneyTransferAmountsTermsCommissionFee', 'product_id', 'id')->where('belonging_id', $this->belongingId())->orderBy('id', 'asc');
    }

}