<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchCarLoansRequest;
use App\Models\Belonging;
use App\Models\CarLoan;
use App\Models\CarType;
use App\Models\CreditLoan;
use App\Models\GoldLoan;
use App\Models\PaymentCard;
use App\Models\PercentageType;
use App\Models\ProductByBelongingsView;
use App\Models\ProvidingType;
use App\Models\RepaymentLoanIntervalType;
use App\Models\RepaymentPercentIntervalType;
use App\Models\RepaymentType;
use App\Models\SecurityType;
use App\Models\TimeType;
use App\Models\YesNo;
use App\Models\YesNoAllAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    /**
     * Show the application home dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $belongings = Belonging::whereIn('extra', [2, 3])->with('productsByBelongingInfo')->get();

        return view('home', ["belongings" => $belongings]);
    }

    /**
     * Show the application home dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function loans()
    {
        $belongings = Belonging::whereIn('extra', [1, 3])->with('productsByBelongingInfo')->get();

        return view('loans', ["belongings" => $belongings]);
    }

    /**
     * compare Car Loans Filters.
     *
     * @return \Illuminate\Http\Response
     */
    public
    function carLoansFilters(Request $request)
    {
        $car_types = $request->car_types;

        $percentage_types = $request->percentage_types;

        $providing_types = $request->providing_types;

        $special_project_answers = $request->special_project_answers;

        $privileged_term_answers = $request->privileged_term_answers;

        $repayment_types = $request->repayment_types;

        $security_types = $request->security_types;

        $repayment_loan_interval_type = $request->repayment_loan_interval_type;

        $repayment_percent_interval_type = $request->repayment_percent_interval_type;

        $loan_term_search = $request->loan_term_search;

        $time_type_search = $request->time_type_search;

        $car_cost = $request->car_cost;

        $prepayment = $request->prepayment;

        if ($time_type_search == 1 || $time_type_search == "" || is_null($time_type_search)) {

            $loan_term_search_in_days = $loan_term_search;

        } else if ($time_type_search == 2) {

            $loan_term_search_in_days = $loan_term_search * 30;

        } else if ($time_type_search == 3) {

            $loan_term_search_in_days = $loan_term_search * 365;
        }

        $products = CarLoan::with('companyInfo')
            ->with('carInfo')
            ->with('loanTermFromPeriodicityTypeInfo')
            ->with('loanTermToPeriodicityTypeInfo');


        if (is_array($car_types) && count($car_types) > 0) {
            $products->where(function ($query) use ($car_types) {
                $query->whereIn('car_type', array_merge($car_types, array(3)));
            });
        }
        if (is_array($repayment_types) && count($repayment_types) > 0) {
            $products->where(function ($query) use ($repayment_types) {
                $query->whereIn('checked_repayment_types', array_merge($repayment_types, array(1)));
            });
        }
        if (is_array($percentage_types) && count($percentage_types) > 0) {
            $products->where(function ($query) use ($percentage_types) {
                $query->whereIn('percentage_type', array_merge($percentage_types, array(1)));
            });
        }
        if (is_array($providing_types) && count($providing_types) > 0) {
            $products->where(function ($query) use ($providing_types) {
                $query->whereIn('providing_type', array_merge($providing_types, array(3)));
            });
        }
        if (is_array($security_types) && count($security_types) > 0) {
            $products->whereHas('securityTypes', function ($q) use ($security_types) {

                $q->whereIn('security_type', $security_types);
            });
        }
        if (is_array($privileged_term_answers) && count($privileged_term_answers) > 0) {
            if (in_array(2, $privileged_term_answers)) {
                $products->where(function ($query) use ($privileged_term_answers) {
                    $query->whereIn('privileged_term_checked', $privileged_term_answers);
                    $query->orWhereNull('privileged_term_checked');
                });
            } else {
                $products->where(function ($query) use ($privileged_term_answers) {
                    $query->whereIn('privileged_term_checked', $privileged_term_answers);
                });
            }
        }

        if (is_array($special_project_answers) && count($special_project_answers) > 0) {
            if (in_array(2, $special_project_answers)) {
                $products->where(function ($query) use ($special_project_answers) {
                    $query->where('special_projects', 0);
                    $query->orWhereNull('special_projects');
                });
            } else {
                $products->where(function ($query) use ($special_project_answers) {
                    $query->whereIn('special_projects', $special_project_answers);
                });
            }
        }

        $products = $products->get();

        $products_arr = $products->toArray();

        $productsGroupByCompanyIds = array_unique($products->pluck('company_id')->toArray());

        $productsGroupByCompany = array();

        foreach ($productsGroupByCompanyIds as $productCompanyId) {
            $productsGroupByCompany[$productCompanyId] = $products->where('company_id', $productCompanyId)->toArray();
        }

        return json_encode($productsGroupByCompany);
    }

    /**
     * compare Car Loans.
     *
     * @return \Illuminate\Http\Response
     */
    public
    function compareCarLoans(Request $request)
    {
        $belonging_id = 1;

        $currProductByBelongingsView = ProductByBelongingsView::where("belonging_id", $belonging_id)->first();

        $belongings = Belonging::with('productsByBelongingInfo')->get();

        $car_types = CarType::all();

        $time_types = TimeType::all();

        $repayment_types = RepaymentType::all();

        $percentage_types = PercentageType::all();

        $providing_types = ProvidingType::all();

        $security_types = SecurityType::all();

        $yes_no_all_answers = YesNoAllAnswer::all();

        $yes_no_answers = YesNo::all();

        $repayment_loan_interval_types = RepaymentLoanIntervalType::all();

        $repayment_percent_interval_types = RepaymentPercentIntervalType::all();

        $loan_amount_to_max = CarLoan::max('loan_amount_to');

        $prepayment_to_max = CarLoan::max('prepayment_to');

        $prepayment_from_min = CarLoan::min('prepayment_from');

        if (is_null($prepayment_from_min)) {
            $prepayment_from_min = 0;
        }

        if (is_null($prepayment_to_max)) {
            $prepayment_to_max = 0;
        }

        $special_projects_having_products_count = CarLoan::where('special_projects', 1)->count();

        $privileged_term_having_products_count = CarLoan::where('privileged_term_checked', 1)->count();

        $car_cost_max_query = DB::table('car_loans')->select(DB::raw('max(loan_amount_to + prepayment_to) as cost'))->first();

        $car_cost_min_query = DB::table('car_loans')->select(DB::raw('min(loan_amount_from + prepayment_from) as cost'))->first();

        $car_cost_min = $car_cost_min_query->cost;

        $car_cost_max = $car_cost_max_query->cost;

        if (!$loan_amount_to_max) {

            $loan_amount_to_max = 0;
        }

        $loan_term_from_periodicity_type = $request->input('loan_term_from_periodicity_type');

        $productPercentageTypesArr = ["1" => "+", "2" => "-", "3" => "±"];

        $loan_term_search = $request->loan_term_search;

        $time_type = $request->time_type;

        $car_cost   =   $request->car_cost;

        $prepayment   =   $request->prepayment;

        $loan_term   =   $request->loan_term;

        if(is_null($prepayment)){

            $prepayment_final =   0;
        }
        else{
            $prepayment_final =   $prepayment;
        }

        if(!is_null($car_cost)){
            $loan_amount   =   $car_cost - $prepayment_final;
        }
        else{
            $loan_amount    =   NULL;
        }

        if ($time_type == 1 || $time_type == "" || is_null($time_type)) {

            $loan_term_search_in_days = $loan_term;

        } else if ($time_type == 2) {

            $loan_term_search_in_days = $loan_term * 30;

        } else if ($time_type == 3) {

            $loan_term_search_in_days = $loan_term * 365;
        }

       //dd($loan_term_search_in_days);
        if (count($request->all()) > 0) {
            $validator = Validator::make($request->all(), [
                'car_cost' => 'required|numeric',

                'loan_term' => 'required|numeric',

                'prepayment' => 'nullable|numeric',
            ]);

            $errors = $validator->errors();

            if($errors->count() > 0){

                $products = NULL;

                $productsGroupByCompany = NULL;

                $request_results_count  =   0;
            }
            else{
                $products = CarLoan::with('companyInfo')
                    ->with('carInfo')

                    ->with('loanTermFromPeriodicityTypeInfo')

                    ->with('loanTermToPeriodicityTypeInfo');

                if (!is_null($loan_term_search_in_days)) {
                    $products->where(function ($query) use ($loan_term_search_in_days) {
                        $query->where('loan_term_from_in_days', '<=', (float)$loan_term_search_in_days);
                        $query->where('loan_term_to_in_days', '>=', (float)$loan_term_search_in_days);
                    });
                }
////                if (!is_null($loan_amount)) {
//                    $products->where(function ($query) use ($loan_amount) {
//                        $query->where('loan_amount_from', '<=', (float)$loan_amount);
//                        $query->where('loan_amount_to', '>=', (float)$loan_amount);
//                    });
//                }

                $products = $products->get();

                $productsGroupByCompanyIds = array_unique($products->pluck('company_id')->toArray());

                $productsGroupByCompany = [];

                foreach ($productsGroupByCompanyIds as $productCompanyId) {
                    $productsGroupByCompany[] = $products->where('company_id', $productCompanyId);
                }
                $request_results_count  =   $products->count();
            }


        } else {
            $validator = Validator::make($request->all(), []);

            $products = NULL;

            $productsGroupByCompany = NULL;

            $request_results_count  =   0;
        }

        $errors = $validator->errors();
 //dd($request_results_count);
        return view('compare.compareCarLoans',
            [
                "belongings" => $belongings,

                "currProductByBelongingsView" => $currProductByBelongingsView,

                "belonging_id" => $belonging_id,

                "repayment_types" => $repayment_types,

                "percentage_types" => $percentage_types,

                "providing_types" => $providing_types,

                "repayment_loan_interval_types" => $repayment_loan_interval_types,

                "repayment_percent_interval_types" => $repayment_percent_interval_types,

                "prepayment_from_min" => $prepayment_from_min,

                "prepayment_to_max" => $prepayment_to_max,

                "car_cost_max" => $car_cost_max,

                "car_cost_min" => $car_cost_min,

                "security_types" => $security_types,

                "car_types" => $car_types,

                "time_types" => $time_types,

                "yes_no_all_answers" => $yes_no_all_answers,

                "yes_no_answers" => $yes_no_answers,

                "special_projects_having_products_count" => $special_projects_having_products_count,

                "privileged_term_having_products_count" => $privileged_term_having_products_count,

                "productPercentageTypesArr" => $productPercentageTypesArr,

                "loan_amount" => $loan_amount,

                "loan_term_from_periodicity_type" => $loan_term_from_periodicity_type,

                "time_type" => $time_type,

                "car_cost" => $car_cost,

                "prepayment" => $prepayment,

                "loan_term" => $loan_term,

                "products" => $products,

                "productsGroupByCompany" => $productsGroupByCompany,

                "errors" => $errors,

                "request_results_count" => $request_results_count,
            ]);
    }

    /**
     * compare Gold Loans.
     *
     * @return \Illuminate\Http\Response
     */
    public
    function compareGoldLoans(Request $request)
    {
        $belonging_id = 2;

        $currProductByBelongingsView = ProductByBelongingsView::where("belonging_id", $belonging_id)->first();

        $belongings = Belonging::with('productsByBelongingInfo')->get();

        $time_types = TimeType::all();

        $loan_amount_to_max = GoldLoan::max('loan_amount_to');

        $name = $request->input('name');

        $comp_name = $request->input('comp_name');

        $loan_amount_from = $request->input('loan_amount_from');

        $loan_amount_to = $request->input('loan_amount_to');

        $percent_val = $request->input('percent');

        $percent = (float)$percent_val;

        $loan_term_from = $request->input('loan_term_from');

        $loan_term_to = $request->input('loan_term_to');

        $loan_term_from_periodicity_type = $request->input('loan_term_from_periodicity_type');

        $loan_term_to_periodicity_type = $request->input('loan_term_to_periodicity_type');

        $name_sql_val = '%' . @$name . '%';

        $comp_name_sql_val = '%' . @$comp_name . '%';

        $products = GoldLoan::with('companyInfo')
            ->with('ProductStatus')
            ->with('goldPledgeTypeInfo');


        if (!is_null($name)) {
            $products->where(function ($query) use ($name, $name_sql_val) {
                if ($name) {
                    $query->where('name', 'like', $name_sql_val);
                }
            });
        }
        $products = $products->get();

        $productsGroupByCompanyIds = array_unique($products->pluck('company_id')->toArray());

        $productsGroupByCompany = [];

        foreach ($productsGroupByCompanyIds as $productCompanyId) {
            $productsGroupByCompany[] = $products->where('company_id', $productCompanyId);
        }

        return view('compare.compareGoldLoans',
            [
                "belongings" => $belongings,

                "currProductByBelongingsView" => $currProductByBelongingsView,

                "belonging_id" => $belonging_id,

                "time_types" => $time_types,

                "name" => $name,

                "comp_name" => $comp_name,

                "loan_amount_from" => $loan_amount_from,

                "loan_amount_to" => $loan_amount_to,

                "loan_amount_to" => $loan_amount_to,

                "percent" => $percent,

                "loan_term_from" => $loan_term_from,

                "loan_term_to" => $loan_term_to,

                "loan_term_from_periodicity_type" => $loan_term_from_periodicity_type,

                "loan_term_to_periodicity_type" => $loan_term_to_periodicity_type,

                "products" => $products,

                "productsGroupByCompany" => $productsGroupByCompany,
            ]);
    }

    /**
     * compare Credits.
     *
     * @return \Illuminate\Http\Response
     */
    public
    function compareCredits(Request $request)
    {
        $belonging_id = 3;

        $currProductByBelongingsView = ProductByBelongingsView::where("belonging_id", $belonging_id)->first();

        $belongings = Belonging::with('productsByBelongingInfo')->get();

        $time_types = TimeType::all();

        $loan_amount_to_max = CreditLoan::max('loan_amount_to');

        $name = $request->input('name');

        $comp_name = $request->input('comp_name');

        $loan_amount_from = $request->input('loan_amount_from');

        $loan_amount_to = $request->input('loan_amount_to');

        $percent_val = $request->input('percent');

        $percent = (float)$percent_val;

        $loan_term_from = $request->input('loan_term_from');

        $loan_term_to = $request->input('loan_term_to');

        $loan_term_from_periodicity_type = $request->input('loan_term_from_periodicity_type');

        $loan_term_to_periodicity_type = $request->input('loan_term_to_periodicity_type');

        $name_sql_val = '%' . @$name . '%';

        $comp_name_sql_val = '%' . @$comp_name . '%';

        $products = CreditLoan::with('companyInfo')
            ->with('ProductStatus')
            ->with('goldPledgeTypeInfo');


        if (!is_null($name)) {
            $products->where(function ($query) use ($name, $name_sql_val) {
                if ($name) {
                    $query->where('name', 'like', $name_sql_val);
                }
            });
        }

        $products = $products->get();

        $productsGroupByCompanyIds = array_unique($products->pluck('company_id')->toArray());

        $productsGroupByCompany = [];

        foreach ($productsGroupByCompanyIds as $productCompanyId) {
            $productsGroupByCompany[] = $products->where('company_id', $productCompanyId);
        }

        return view('compare.compareCredits',
            [
                "belongings" => $belongings,

                "currProductByBelongingsView" => $currProductByBelongingsView,

                "belonging_id" => $belonging_id,

                "time_types" => $time_types,

                "name" => $name,

                "comp_name" => $comp_name,

                "loan_amount_from" => $loan_amount_from,

                "loan_amount_to" => $loan_amount_to,

                "loan_amount_to" => $loan_amount_to,

                "percent" => $percent,

                "loan_term_from" => $loan_term_from,

                "loan_term_to" => $loan_term_to,

                "loan_term_from_periodicity_type" => $loan_term_from_periodicity_type,

                "loan_term_to_periodicity_type" => $loan_term_to_periodicity_type,

                "products" => $products,

                "productsGroupByCompany" => $productsGroupByCompany,
            ]);
    }

    /**
     * compare Payment Cards.
     *
     * @return \Illuminate\Http\Response
     */
    public
    function comparePaymentCards(Request $request)
    {
        $belonging_id = 9;

        $currProductByBelongingsView = ProductByBelongingsView::where("belonging_id", $belonging_id)->first();

        $belongings = Belonging::with('productsByBelongingInfo')->get();

        $time_types = TimeType::all();

        $loan_amount_to_max = CarLoan::max('loan_amount_to');

        $name = $request->input('name');

        $comp_name = $request->input('comp_name');

        $loan_amount_from = $request->input('loan_amount_from');

        $loan_amount_to = $request->input('loan_amount_to');

        $percent_val = $request->input('percent');

        $percent = (float)$percent_val;

        $loan_term_from = $request->input('loan_term_from');

        $loan_term_to = $request->input('loan_term_to');

        $loan_term_from_periodicity_type = $request->input('loan_term_from_periodicity_type');

        $loan_term_to_periodicity_type = $request->input('loan_term_to_periodicity_type');

        $name_sql_val = '%' . @$name . '%';

        $comp_name_sql_val = '%' . @$comp_name . '%';

        $products = PaymentCard::with('companyInfo')
            ->with('creditLineInfo')
            ->with('productsPaymentCardsType')
            ->with('productsPaymentCardsCurrencies')
            ->with('productsPaymentCardsCardType')
            ->with('productsPaymentCardsRegion')
            ->with('productsSpecialsCardsType')
            ->with('productsPaymentCardsExtraType')
            ->with('attachmentCardInfo');


        if (!is_null($name)) {
            $products->where(function ($query) use ($name, $name_sql_val) {
                if ($name) {
                    $query->where('name', 'like', $name_sql_val);
                }
            });
        }
        $products = $products->get();

        $productsGroupByCompanyIds = array_unique($products->pluck('company_id')->toArray());

        $productsGroupByCompany = [];

        foreach ($productsGroupByCompanyIds as $productCompanyId) {
            $productsGroupByCompany[] = $products->where('company_id', $productCompanyId);
        }

        return view('compare.comparePaymentCards',
            [
                "belongings" => $belongings,

                "currProductByBelongingsView" => $currProductByBelongingsView,

                "belonging_id" => $belonging_id,

                "time_types" => $time_types,

                "name" => $name,

                "comp_name" => $comp_name,

                "loan_amount_from" => $loan_amount_from,

                "loan_amount_to" => $loan_amount_to,

                "loan_amount_to" => $loan_amount_to,

                "percent" => $percent,

                "loan_term_from" => $loan_term_from,

                "loan_term_to" => $loan_term_to,

                "loan_term_from_periodicity_type" => $loan_term_from_periodicity_type,

                "loan_term_to_periodicity_type" => $loan_term_to_periodicity_type,

                "products" => $products,

                "productsGroupByCompany" => $productsGroupByCompany,
            ]);
    }
}