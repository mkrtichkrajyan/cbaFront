<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminCompanyTypesManage extends Model
{
    protected $table = 'cb_admin_company_types_manage';

    public function companyTypeInfo()
    {
        return $this->hasOne('App\Models\CompaniesTypes','id','company_type_id');
    }

    public function companyInfo()
    {
        return $this->hasOne('App\Models\Company','id','company_id');
    }

    public function manageTypeInfo()
    {
        return $this->hasOne('App\Models\ManageType','id','manage_type_id');
    }

    public function adminInfo()
    {
        return $this->hasOne('App\User','id','admin_id');
    }
}
