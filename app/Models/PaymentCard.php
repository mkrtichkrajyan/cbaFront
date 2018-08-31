<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentCard extends Model
{
    protected $table = 'payment_cards';

    public function belongingId() {

        return 9;
    }

    public function ProductStatus()
    {
        return $this->hasOne('App\Models\Status', 'id','status');
    }

    public function attachmentCardInfo()
    {
        return $this->hasOne('App\Models\YesNoAllAnswer', 'id','attachment_card');
    }

    public function creditLineInfo()
    {
        return $this->hasOne('App\Models\YesNoAllAnswer', 'id','credit_line');
    }

    public function companyInfo()
    {
        return $this->hasOne('App\Models\Company', 'id', 'company_id');
    }

    public function productsPaymentCardsType()
    {
        return $this->hasMany('App\Models\ProductsPaymentCardsType','product_id','id')->where('belonging_id',$this->belongingId());

    }

    public function productsPaymentCardsCurrencies()
    {
        return $this->hasMany('App\Models\ProductsPaymentCardsCurrency','product_id','id')->where('belonging_id',$this->belongingId());

    }

    public function productsPaymentCardsCardType()
    {
        return $this->hasMany('App\Models\ProductsPaymentCardsCardType','product_id','id')->where('belonging_id',$this->belongingId());

    }

    public function productsPaymentCardsRegion()
    {
        return $this->hasMany('App\Models\ProductsPaymentCardsRegion','product_id','id')->where('belonging_id',$this->belongingId());

    }

    public function productsSpecialsCardsType()
    {
        return $this->hasMany('App\Models\ProductsSpecialsCardsType','product_id','id')->where('belonging_id',$this->belongingId());

    }

    public function productsPaymentCardsExtraType()
    {
        return $this->hasMany('App\Models\ProductsPaymentCardsExtraType','product_id','id')->where('belonging_id',$this->belongingId());

    }
}
