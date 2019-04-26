<?php

namespace App\Http\Controllers;

use App\Models\Belonging;
use App\Models\Company;
use App\Models\CompanyBankomat;
use App\Models\CompanyBranch;
use App\Models\Social;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

class CompaniesController extends MainController
{

    public function __construct()
    {
        $getCompareInfo = $this->getCompareInfoGlobal();

        $belongings_all = Belonging::where('id', '>', 0)->with('productsByBelongingInfo')->get();

        $socials = Social::first();

        View::share('getCompareInfo', $getCompareInfo);

        View::share('belongings_all', $belongings_all);

        View::share('socials', $socials);
    }

    /**
     * Show the company Branches and Bankomats.
     *
     * @return \Illuminate\Http\Response
     */
    public function companyBranchesBankomats($company_id)
    {
        $company    =   Company::where('id',$company_id)->with('companyBranches.cityInfo')->with('companyBankomats.cityInfo')->first();

        //dd($company->companyBankomats);

        return view('companies.index', ["company"=>$company] );
    }

    /**
     * download  companies branches list.
     *
     * @return \Illuminate\Http\Response
     */
    public
    function downloadBranches($company_id,Request $request)
    {
        $headers = array(

            "Content-type" => "text/csv",

            "charset" => "UTF-8",

            "Content-Disposition" => "attachment; filename=company-branches.csv",

            "Pragma" => "no-cache",

            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",

            "Expires" => "0"
        );

        $companyBranches = CompanyBranch::where('company_id',$company_id)->with('cityInfo')->get();

        $company_branches_data  =   [];

        foreach ($companyBranches as $companyBranchCurr) {

            $curr_branch_data = [

                "id" => $companyBranchCurr->id,

                "name" => $companyBranchCurr->name,

                "city" => @$companyBranchCurr->cityInfo->name,

                "address" => $companyBranchCurr->address,

                "phone_number" => $companyBranchCurr->phone_number,

                "phone_number_2" => $companyBranchCurr->phone_number_2,

                "mondayWorkStartTime" => $companyBranchCurr->mondayWorkStartTime ? : null,

                "mondayWorkEndTime" => $companyBranchCurr->mondayWorkEndTime ? : null,

                "tuesdayWorkStartTime" => $companyBranchCurr->tuesdayWorkStartTime ? : null,

                "tuesdayWorkEndTime" => $companyBranchCurr->tuesdayWorkEndTime ? : null,

                "wednesdayWorkStartTime" => $companyBranchCurr->wednesdayWorkStartTime ? : null,

                "wednesdayWorkEndTime" => $companyBranchCurr->wednesdayWorkEndTime ? : null,

                "thursdayWorkStartTime" => $companyBranchCurr->thursdayWorkStartTime ? : null,

                "thursdayWorkEndTime" => $companyBranchCurr->thursdayWorkEndTime ? : null,

                "fridayWorkStartTime" => $companyBranchCurr->fridayWorkStartTime ? : null,

                "fridayWorkEndTime" => $companyBranchCurr->fridayWorkEndTime ? : null,

                "saturdayWorkStartTime" => $companyBranchCurr->saturdayWorkStartTime ? : null,

                "saturdayWorkEndTime" => $companyBranchCurr->saturdayWorkEndTime ? : null,

                "sundayWorkStartTime" => $companyBranchCurr->sundayWorkStartTime ? : null,

                "sundayWorkEndTime" => $companyBranchCurr->sundayWorkEndTime ? : null,

                "created_at" => $companyBranchCurr->created_at,

                "updated_at" => $companyBranchCurr->updated_at,

                "stateStart" => $companyBranchCurr->stateStart,

                "stateEnd" => $companyBranchCurr->stateEnd,
            ];

            $company_branches_data[]    =   $curr_branch_data;
        }
        //dd($company_branches_data);

        $columns = array('ID', 'Name', 'City','Address', 'Phone Number', 'Phone Number 2','monday','tuesday','wednesday','thursday','friday','saturday','sunday', 'Created At');

        $callback = function () use ($company_branches_data, $columns) {

            $BOM = "\xEF\xBB\xBF"; // UTF-8 BOM

            $file = fopen('php://output', 'w');

            fwrite($file,$BOM);

            fputcsv($file, $columns);

            foreach ($company_branches_data as $company_branch) {
                fputcsv($file, array(
                    $company_branch["id"],

                    $company_branch["name"],

                    $company_branch["city"],

                    $company_branch["address"],

                    $company_branch["phone_number"],

                    $company_branch["phone_number_2"],

                    $company_branch["mondayWorkStartTime"].' - '.$company_branch["mondayWorkEndTime"],

                    $company_branch["tuesdayWorkStartTime"].' - '.$company_branch["tuesdayWorkEndTime"],

                    $company_branch["wednesdayWorkStartTime"].' - '.$company_branch["wednesdayWorkEndTime"],

                    $company_branch["thursdayWorkStartTime"].' - '.$company_branch["thursdayWorkEndTime"],

                    $company_branch["fridayWorkStartTime"].' - '.$company_branch["fridayWorkEndTime"],

                    $company_branch["saturdayWorkStartTime"].' - '.$company_branch["saturdayWorkEndTime"],

                    $company_branch["sundayWorkStartTime"].' - '.$company_branch["sundayWorkEndTime"],

                    $company_branch["created_at"]));
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * download  companies bankomats list.
     *
     * @return \Illuminate\Http\Response
     */
    public
    function downloadBankomats($company_id,Request $request)
    {
        $headers = array(

            "Content-type" => "text/csv",

            "charset" => "UTF-8",

            "Content-Disposition" => "attachment; filename=company-bankomats.csv",

            "Pragma" => "no-cache",

            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",

            "Expires" => "0"
        );

        $companyBankomats = CompanyBankomat::where('company_id',$company_id)->with('cityInfo')->get();

        $company_bankomats_data  =   [];

        foreach ($companyBankomats as $companyBankomatCurr) {

            $curr_bankomat_data = [

                "id" => $companyBankomatCurr->id,

                "name" => $companyBankomatCurr->name,

                "city" => @$companyBankomatCurr->cityInfo->name,

                "address" => $companyBankomatCurr->address,

                "created_at" => $companyBankomatCurr->created_at,

                "updated_at" => $companyBankomatCurr->updated_at,

                "stateStart" => $companyBankomatCurr->stateStart,

                "stateEnd" => $companyBankomatCurr->stateEnd,
            ];

            $company_bankomats_data[]    =   $curr_bankomat_data;
        }
        //dd($company_branches_data);

        $columns = array('ID', 'Name', 'City','Address', 'Created At');

        $callback = function () use ($company_bankomats_data, $columns) {

            $BOM = "\xEF\xBB\xBF"; // UTF-8 BOM

            $file = fopen('php://output', 'w');

            fwrite($file,$BOM);

            fputcsv($file, $columns);

            foreach ($company_bankomats_data as $curr_bankomat) {
                fputcsv($file, array(
                    $curr_bankomat["id"],

                    $curr_bankomat["name"],

                    $curr_bankomat["city"],

                    $curr_bankomat["address"],

                    $curr_bankomat["created_at"]));
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}