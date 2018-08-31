<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompaniesType extends Model
{
    protected $table = 'companies_types';

    public function companies()
    {
        return $this->hasMany('App\Models\Companies','type');
    }
    public function adminCompanyTypes()
    {
        return $this->hasMany('App\Models\AdminCompanyTypesManage','company_type_id','name');
    }
}
