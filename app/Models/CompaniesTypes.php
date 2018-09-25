<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompaniesTypes extends Model
{
    public function companies()
    {
        return $this->hasMany('App\Models\Companies','type');
        //  return $this->hasMany('App\Models\Company','type','id');
    }
    public function adminCompanyTypes()
    {
        return $this->hasMany('App\Models\AdminCompanyTypesManage','company_type_id','name');
    }
}
