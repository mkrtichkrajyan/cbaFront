<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyBranch extends Model
{
    protected $table = 'companies_branches';

    public function phonesInfo()
    {
        return $this->hasMany('App\Models\CompanyBranchPhone','company_branch_id','id');

    }

    public function cityInfo()
    {
        return $this->hasOne('App\Models\City','id','city');
    }

    public function aroundClockWorking()
    {
        return $this->hasOne('App\Models\YesNo','id','around_the_clock_working');
    }
}
