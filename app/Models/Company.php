<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $table = 'companies';

    public function companiesTypes()
    {
        return $this->belongsTo('App\Models\CompaniesTypes','type','id');
    }

    public function companyBranches()
    {
        return $this->hasMany('App\Models\CompanyBranch','company_id','id');
    }

    public function companyBankomats()
    {
        return $this->hasMany('App\Models\CompanyBankomat','company_id','id');
    }

    public function companyBelonging()
    {
        return $this->belongsTo('App\Models\Belonging');
    }

    public function adminCompany()
    {
        return $this->hasMany('App\Models\AdminCompanyTypesManage','company_id','name');
    }
    public function phonesInfo()
    {
        return $this->hasMany('App\Models\CompanyPhone','company_id','id');
    }
}
