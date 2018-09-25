<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchCarLoansRequest;
use App\Models\AgricLoan;
use App\Models\Belonging;
use App\Models\CarLoan;
use App\Models\CarType;
use App\Models\ConsumerCredit;
use App\Models\Country;
use App\Models\CreditLoan;
use App\Models\CreditPurposeTypes;
use App\Models\Deposit;
use App\Models\DepositCapitalizationsList;
use App\Models\DepositInterestRatesPayment;
use App\Models\DepositsSpecialsList;
use App\Models\DepositTypesList;
use App\Models\GoldAssayType;
use App\Models\GoldLoan;
use App\Models\GoldPledgeType;
use App\Models\LoanCurrenciesType;
use App\Models\LoanRefinancing;
use App\Models\LoanRefinancingPurposeType;
use App\Models\MoneyTransfer;
use App\Models\MoneyTransferCurrenciesAllType;
use App\Models\Mortgage;
use App\Models\MortgagePurposeType;
use App\Models\OnlineLoan;
use App\Models\PaymentCard;
use App\Models\PaymentCardCurrency;
use App\Models\PaymentCardProductType;
use App\Models\PaymentCardRegion;
use App\Models\PaymentCardType;
use App\Models\PaymentExtraCard;
use App\Models\PaymentSpecialCard;
use App\Models\PercentageType;
use App\Models\ProductByBelongingsView;
use App\Models\ProvidingType;
use App\Models\PurposeType;
use App\Models\RepaymentLoanIntervalType;
use App\Models\RepaymentPercentIntervalType;
use App\Models\RepaymentType;
use App\Models\SecurityType;
use App\Models\StudentLoan;
use App\Models\TimeType;
use App\Models\TransferBank;
use App\Models\TransferSystem;
use App\Models\TravelInsurance;
use App\Models\YesNo;
use App\Models\YesNoAllAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class HomeController extends MainController
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
     * Give the previousUrl.
     *
     * @return \Illuminate\Http\Response
     */
    public function loansPreviousUrl(Request $request)
    {
        return url('/');
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

        $time_types = TimeType::all();

        $yes_no_answers = YesNo::all();

        $repayment_loan_interval_types = RepaymentLoanIntervalType::all();

        $repayment_percent_interval_types = RepaymentPercentIntervalType::all();

        $car_cost_max_query = DB::table('car_loans')->select(DB::raw('max(loan_amount_to + prepayment_to) as cost'))->first();

        $car_cost_min_query = DB::table('car_loans')->select(DB::raw('min(loan_amount_from + prepayment_from) as cost'))->first();

        $car_cost_min = $car_cost_min_query->cost;

        $car_cost_max = $car_cost_max_query->cost;

        $loan_term_from_periodicity_type = $request->input('loan_term_from_periodicity_type');

        $productPercentageTypesArr = ["1" => "+", "2" => "-", "3" => "±"];

        $time_type = $request->time_type;

        $car_cost = $request->car_cost;

        $prepayment = $request->prepayment;

        $loan_term = $request->loan_term;

        if (is_null($prepayment)) {
            $prepayment_final = 0;
        } else {
            $prepayment_final = $prepayment;
        }

        if (!is_null($car_cost)) {
            $loan_amount = $car_cost - $prepayment_final;
        } else {
            $loan_amount = NULL;
        }

        if ($time_type == 1 || $time_type == "" || is_null($time_type)) {

            $loan_term_search_in_days = $loan_term;

        } else if ($time_type == 2) {

            $loan_term_search_in_days = $loan_term * 30;

        } else if ($time_type == 3) {

            $loan_term_search_in_days = $loan_term * 365;
        }

        if (count($request->all()) > 0) {
            $validator = Validator::make($request->all(), [
                'car_cost' => 'required|numeric',

                'loan_term' => 'required|numeric',

                'prepayment' => 'nullable|numeric',
            ]);

            $errors = $validator->errors();

            if ($errors->count() > 0) {

                $products = NULL;

                $productsGroupByCompany = NULL;

                $request_results_count = 0;

                $productsFiltersSingleCounts = NULL;

                $car_types = NULL;

                $percentage_types = NULL;

                $providing_types = NULL;

                $security_types = NULL;

                $repayment_types = NULL;

                $privileged_term_having_products_count = NULL;

                $privileged_term_no_having_products_count = NULL;

                $special_projects_having_products_count = NULL;

                $special_projects_no_having_products_count = NULL;
            }
            else {
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
                $request_results_count = $products->count();

                $productsFiltersSingleCounts    =   $this->compareProductsGetSomeFilters($belonging_id,$products);

                $car_types = $productsFiltersSingleCounts["car_types"];

                $percentage_types = $productsFiltersSingleCounts["percentage_types"];

                $providing_types = $productsFiltersSingleCounts["providing_types"];

                $security_types = $productsFiltersSingleCounts["security_types"];

                $repayment_types = $productsFiltersSingleCounts["repayment_types"];

                $privileged_term_having_products_count = $products->where('privileged_term_checked', 1)->count();

                $privileged_term_no_having_products_count = $products->where('privileged_term_checked','!=', 1)->count();

                $special_projects_having_products_count = $products->where('special_projects', 1)->count();

                $special_projects_no_having_products_count = $products->where('special_projects','!=', 1)->count();
            }
        }
        else {
            $validator = Validator::make($request->all(), []);

            $products = NULL;

            $productsGroupByCompany = NULL;

            $request_results_count = 0;

            $productsFiltersSingleCounts = NULL;

            $car_types = NULL;

            $percentage_types = NULL;

            $providing_types = NULL;

            $security_types = NULL;

            $repayment_types = NULL;

            $privileged_term_having_products_count = NULL;

            $privileged_term_no_having_products_count = NULL;

            $special_projects_having_products_count = NULL;

            $special_projects_no_having_products_count = NULL;
        }

        $errors = $validator->errors();

        $previousUrl = $this->loansPreviousUrl($request);

        return view('compare.compareCarLoans',
            [
                "belongings" => $belongings,

                "currProductByBelongingsView" => $currProductByBelongingsView,

                "belonging_id" => $belonging_id,

                "repayment_loan_interval_types" => $repayment_loan_interval_types,

                "repayment_percent_interval_types" => $repayment_percent_interval_types,

                "car_cost_max" => $car_cost_max,

                "car_cost_min" => $car_cost_min,

                "time_types" => $time_types,

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

                "previousUrl" => $previousUrl,

                "productsFiltersSingleCounts" => $productsFiltersSingleCounts,

                "car_types" => $car_types,

                "percentage_types" => $percentage_types,

                "providing_types" => $providing_types,

                "security_types" => $security_types,

                "repayment_types" => $repayment_types,

                "privileged_term_having_products_count" => $privileged_term_having_products_count,

                "privileged_term_no_having_products_count" => $privileged_term_no_having_products_count,

                "special_projects_having_products_count" => $special_projects_having_products_count,

                "special_projects_no_having_products_count" => $special_projects_no_having_products_count,
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

        $currBelonging = Belonging::where("id", $belonging_id)->first();

        $time_types = TimeType::all();

        $gold_pledge_types = GoldPledgeType::all();

        $yes_no_all_answers = YesNoAllAnswer::all();

        $yes_no_answers = YesNo::all();

        $repayment_loan_interval_types = RepaymentLoanIntervalType::all();

        $repayment_percent_interval_types = RepaymentPercentIntervalType::all();

        $time_type = $request->time_type;

        $loan_term = $request->loan_term;

        $loan_amount = $request->loan_amount;

        $gold_pledge_type = $request->gold_pledge_type;

        if ($time_type == 1 || $time_type == "" || is_null($time_type)) {

            $loan_term_search_in_days = $loan_term;

        } else if ($time_type == 2) {

            $loan_term_search_in_days = $loan_term * 30;

        } else if ($time_type == 3) {

            $loan_term_search_in_days = $loan_term * 365;
        }

        if (count($request->all()) > 0) {
            $validator = Validator::make($request->all(), [

                'loan_term' => 'required|numeric',

                'loan_amount' => 'required|numeric',

                'gold_pledge_type' => 'required',
            ]);

            $errors = $validator->errors();

            if ($errors->count() > 0) {

                $products = NULL;

                $productsGroupByCompany = NULL;

                $request_results_count = 0;
            } else {

                if ($gold_pledge_type == 3) {

                    $gold_pledge_types_arr = $gold_pledge_types->pluck('id')->toArray();
                } else {
                    $gold_pledge_types_arr = array($gold_pledge_type);
                }

                $products = GoldLoan::with('companyInfo')
                    ->with('loanTermFromPeriodicityTypeInfo')
                    ->with('loanTermToPeriodicityTypeInfo')
                    ->with('goldPledgeTypeInfo');

                if (!is_null($loan_amount)) {
                    $products->where(function ($query) use ($loan_amount) {

                        $query->where('loan_amount_from', '<=', (float)$loan_amount);

                        $query->where('loan_amount_to', '>=', (float)$loan_amount);
                    });
                }
                if (!is_null($gold_pledge_type)) {
                    $products->where(function ($query) use ($gold_pledge_types_arr) {

                        $query->whereIn('gold_pledge_type', $gold_pledge_types_arr);
                    });
                }
                if (!is_null($loan_term_search_in_days)) {
                    $products->where(function ($query) use ($loan_term_search_in_days) {
                        $query->where('loan_term_from_in_days', '<=', (float)$loan_term_search_in_days);
                        $query->where('loan_term_to_in_days', '>=', (float)$loan_term_search_in_days);
                    });
                }

                $products = $products->get();

                $productsGroupByCompanyIds = array_unique($products->pluck('company_id')->toArray());

                $productsGroupByCompany = [];

                foreach ($productsGroupByCompanyIds as $productCompanyId) {
                    $productsGroupByCompany[] = $products->where('company_id', $productCompanyId);
                }

                $request_results_count = $products->count();

                $productsFiltersSingleCounts    =   $this->compareProductsGetSomeFilters($belonging_id,$products);

                $percentage_types = $productsFiltersSingleCounts["percentage_types"];

                $providing_types = $productsFiltersSingleCounts["providing_types"];

                $repayment_types = $productsFiltersSingleCounts["repayment_types"];

                $gold_assay_types = $productsFiltersSingleCounts["gold_assay_types"];

                $privileged_term_having_products_count = $products->where('privileged_term_checked', 1)->count();

                $privileged_term_no_having_products_count = $products->where('privileged_term_checked','!=', 1)->count();
            }
        }
        else {
            $validator = Validator::make($request->all(), []);

            $products = NULL;

            $productsGroupByCompany = NULL;

            $request_results_count = 0;

            $productsFiltersSingleCounts = NULL;

            $percentage_types = NULL;

            $providing_types = NULL;

            $repayment_types = NULL;

            $gold_assay_types = NULL;

            $privileged_term_having_products_count = NULL;

            $privileged_term_no_having_products_count = NULL;
        }

        $errors = $validator->errors();

        $previousUrl = $this->loansPreviousUrl($request);

        return view('compare.compareGoldLoans',
            [
                "belongings" => $belongings,

                "currProductByBelongingsView" => $currProductByBelongingsView,

                "currBelonging" => $currBelonging,

                "belonging_id" => $belonging_id,

                "gold_assay_types" => $gold_assay_types,

                "gold_pledge_types" => $gold_pledge_types,

                "yes_no_all_answers" => $yes_no_all_answers,

                "yes_no_answers" => $yes_no_answers,

                "repayment_loan_interval_types" => $repayment_loan_interval_types,

                "repayment_percent_interval_types" => $repayment_percent_interval_types,

                "time_types" => $time_types,

                "time_type" => $time_type,

                "loan_term" => $loan_term,

                "gold_pledge_type" => $gold_pledge_type,

                "loan_amount" => $loan_amount,

                "errors" => $errors,

                "products" => $products,

                "productsGroupByCompany" => $productsGroupByCompany,

                "request_results_count" => $request_results_count,

                "previousUrl" => $previousUrl,

                "productsFiltersSingleCounts" => $productsFiltersSingleCounts,

                "percentage_types" => $percentage_types,

                "providing_types" => $providing_types,

                "repayment_types" => $repayment_types,

                "gold_assay_types" => $gold_assay_types,

                "privileged_term_having_products_count" => $privileged_term_having_products_count,

                "privileged_term_no_having_products_count" => $privileged_term_no_having_products_count,
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

        $currBelonging = Belonging::where("id", $belonging_id)->first();

        $time_types = TimeType::all();

        $yes_no_answers = YesNo::all();

        $repayment_loan_interval_types = RepaymentLoanIntervalType::all();

        $repayment_percent_interval_types = RepaymentPercentIntervalType::all();

        $time_type = $request->time_type;

        $loan_term = $request->loan_term;

        $cost = $request->cost;

        $prepayment = $request->prepayment;

        if (is_null($prepayment)) {
            $prepayment_final = 0;
        } else {
            $prepayment_final = $prepayment;
        }

        if (!is_null($cost)) {
            $loan_amount = $cost - $prepayment_final;
        } else {
            $loan_amount = NULL;
        }

        if ($time_type == 1 || $time_type == "" || is_null($time_type)) {
            $loan_term_search_in_days = $loan_term;

        } else if ($time_type == 2) {
            $loan_term_search_in_days = $loan_term * 30;

        } else if ($time_type == 3) {
            $loan_term_search_in_days = $loan_term * 365;
        }

        if (count($request->all()) > 0) {
            $validator = Validator::make($request->all(), [
                'loan_term' => 'required|numeric',

                'cost' => 'required|numeric',

                'prepayment' => 'nullable|numeric',
            ]);

            $errors = $validator->errors();

            if ($errors->count() > 0) {

                $products = NULL;

                $productsGroupByCompany = NULL;

                $request_results_count = 0;

                $productsFiltersSingleCounts = NULL;

                $creditPurposeTypes = NULL;

                $percentage_types = NULL;

                $providing_types = NULL;

                $security_types = NULL;

                $repayment_types = NULL;

                $privileged_term_having_products_count = NULL;

                $privileged_term_no_having_products_count = NULL;

                $special_projects_having_products_count = NULL;

                $special_projects_no_having_products_count = NULL;
            }
            else {
                $products = CreditLoan::with('companyInfo')
                    ->with('loanTermFromPeriodicityTypeInfo')
                    ->with('loanTermToPeriodicityTypeInfo');

                if (!is_null($loan_amount)) {
                    $products->where(function ($query) use ($loan_amount) {

                        $query->where('loan_amount_from', '<=', (float)$loan_amount);

                        $query->where('loan_amount_to', '>=', (float)$loan_amount);
                    });
                }
                if (!is_null($loan_term_search_in_days)) {

                    $products->where(function ($query) use ($loan_term_search_in_days) {

                        $query->where('loan_term_from_in_days', '<=', (float)$loan_term_search_in_days);

                        $query->where('loan_term_to_in_days', '>=', (float)$loan_term_search_in_days);
                    });
                }

                $products = $products->get();

                $productsGroupByCompanyIds = array_unique($products->pluck('company_id')->toArray());

                $productsGroupByCompany = [];

                foreach ($productsGroupByCompanyIds as $productCompanyId) {
                    $productsGroupByCompany[] = $products->where('company_id', $productCompanyId);
                }

                $request_results_count = $products->count();

                $productsFiltersSingleCounts    =   $this->compareProductsGetSomeFilters($belonging_id,$products);

                $creditPurposeTypes = $productsFiltersSingleCounts["creditPurposeTypes"];

                $percentage_types = $productsFiltersSingleCounts["percentage_types"];

                $providing_types = $productsFiltersSingleCounts["providing_types"];

                $security_types = $productsFiltersSingleCounts["security_types"];

                $repayment_types = $productsFiltersSingleCounts["repayment_types"];

                $privileged_term_having_products_count = $products->where('privileged_term_checked', 1)->count();

                $privileged_term_no_having_products_count = $products->where('privileged_term_checked','!=', 1)->count();

                $special_projects_having_products_count = $products->where('special_projects', 1)->count();

                $special_projects_no_having_products_count = $products->where('special_projects','!=', 1)->count();
            }
        }
        else {
            $validator = Validator::make($request->all(), []);

            $products = NULL;

            $productsGroupByCompany = NULL;

            $request_results_count = 0;

            $productsFiltersSingleCounts = NULL;

            $creditPurposeTypes = NULL;

            $percentage_types = NULL;

            $providing_types = NULL;

            $security_types = NULL;

            $repayment_types = NULL;

            $privileged_term_having_products_count = NULL;

            $privileged_term_no_having_products_count = NULL;

            $special_projects_having_products_count = NULL;

            $special_projects_no_having_products_count = NULL;
        }

        $errors = $validator->errors();

        $previousUrl = $this->loansPreviousUrl($request);

        return view('compare.compareCredits',
            [
                "belongings" => $belongings,

                "currProductByBelongingsView" => $currProductByBelongingsView,

                "currBelonging" => $currBelonging,

                "belonging_id" => $belonging_id,

                "yes_no_answers" => $yes_no_answers,

                "repayment_loan_interval_types" => $repayment_loan_interval_types,

                "repayment_percent_interval_types" => $repayment_percent_interval_types,

                "time_types" => $time_types,

                "time_type" => $time_type,

                "loan_term" => $loan_term,

                "cost" => $cost,

                "prepayment" => $prepayment,

                "loan_amount" => $loan_amount,

                "errors" => $errors,

                "products" => $products,

                "productsGroupByCompany" => $productsGroupByCompany,

                "request_results_count" => $request_results_count,

                "previousUrl" => $previousUrl,

                "productsFiltersSingleCounts" => $productsFiltersSingleCounts,

                "creditPurposeTypes" => $creditPurposeTypes,

                "percentage_types" => $percentage_types,

                "providing_types" => $providing_types,

                "security_types" => $security_types,

                "repayment_types" => $repayment_types,

                "privileged_term_having_products_count" => $privileged_term_having_products_count,

                "privileged_term_no_having_products_count" => $privileged_term_no_having_products_count,

                "special_projects_having_products_count" => $special_projects_having_products_count,

                "special_projects_no_having_products_count" => $special_projects_no_having_products_count,
            ]);
    }

    /**
     * compare Student Loans.
     *
     * @return \Illuminate\Http\Response
     */
    public
    function compareStudentLoans(Request $request)
    {
        $belonging_id = 4;

        $currProductByBelongingsView = ProductByBelongingsView::where("belonging_id", $belonging_id)->first();

        $belongings = Belonging::with('productsByBelongingInfo')->get();

        $currBelonging = Belonging::where("id", $belonging_id)->first();

        $time_types = TimeType::all();

        $yes_no_answers = YesNo::all();

        $repayment_loan_interval_types = RepaymentLoanIntervalType::all();

        $repayment_percent_interval_types = RepaymentPercentIntervalType::all();

        $time_type = $request->time_type;

        $loan_term = $request->loan_term;

        $loan_amount = $request->loan_amount;

        if ($time_type == 1 || $time_type == "" || is_null($time_type)) {

            $loan_term_search_in_days = $loan_term;

        } else if ($time_type == 2) {

            $loan_term_search_in_days = $loan_term * 30;

        } else if ($time_type == 3) {

            $loan_term_search_in_days = $loan_term * 365;
        }

        if (count($request->all()) > 0) {
            $validator = Validator::make($request->all(), [
                'loan_term' => 'required|numeric',

                'loan_amount' => 'required|numeric',
            ]);

            $errors = $validator->errors();

            if ($errors->count() > 0) {

                $products = NULL;

                $productsGroupByCompany = NULL;

                $request_results_count = 0;
            } else {
                $products = StudentLoan::with('companyInfo')
                    ->with('securityTypes')
                    ->with('loanTermFromPeriodicityTypeInfo')
                    ->with('loanTermToPeriodicityTypeInfo');

                if (!is_null($loan_amount)) {
                    $products->where(function ($query) use ($loan_amount) {

                        $query->where('loan_amount_from', '<=', (float)$loan_amount);

                        $query->where('loan_amount_to', '>=', (float)$loan_amount);
                    });
                }
                if (!is_null($loan_term_search_in_days)) {
                    $products->where(function ($query) use ($loan_term_search_in_days) {
                        $query->where('loan_term_from_in_days', '<=', (float)$loan_term_search_in_days);

                        $query->where('loan_term_to_in_days', '>=', (float)$loan_term_search_in_days);
                    });
                }

                $products = $products->get();

                $productsGroupByCompanyIds = array_unique($products->pluck('company_id')->toArray());

                $productsGroupByCompany = [];

                foreach ($productsGroupByCompanyIds as $productCompanyId) {
                    $productsGroupByCompany[] = $products->where('company_id', $productCompanyId);
                }

                $request_results_count = $products->count();

                $productsFiltersSingleCounts    =   $this->compareProductsGetSomeFilters($belonging_id,$products);

                $percentage_types = $productsFiltersSingleCounts["percentage_types"];

                $providing_types = $productsFiltersSingleCounts["providing_types"];

                $security_types = $productsFiltersSingleCounts["security_types"];

                $repayment_types = $productsFiltersSingleCounts["repayment_types"];

                $privileged_term_having_products_count = $products->where('privileged_term_checked', 1)->count();

                $privileged_term_no_having_products_count = $products->where('privileged_term_checked','!=', 1)->count();

                $special_projects_having_products_count = $products->where('special_projects', 1)->count();

                $special_projects_no_having_products_count = $products->where('special_projects','!=', 1)->count();
            }
        }
        else {
            $validator = Validator::make($request->all(), []);

            $products = NULL;

            $productsGroupByCompany = NULL;

            $request_results_count = 0;

            $productsFiltersSingleCounts = NULL;

            $percentage_types = NULL;

            $providing_types = NULL;

            $security_types = NULL;

            $repayment_types = NULL;

            $privileged_term_having_products_count = NULL;

            $privileged_term_no_having_products_count = NULL;

            $special_projects_having_products_count = NULL;

            $special_projects_no_having_products_count = NULL;
        }

        $errors = $validator->errors();

        $previousUrl = $this->loansPreviousUrl($request);

        return view('compare.compareStudentLoans',
            [
                "belongings" => $belongings,

                "currProductByBelongingsView" => $currProductByBelongingsView,

                "currBelonging" => $currBelonging,

                "belonging_id" => $belonging_id,

                "repayment_types" => $repayment_types,

                "percentage_types" => $percentage_types,

                "yes_no_answers" => $yes_no_answers,

                "repayment_loan_interval_types" => $repayment_loan_interval_types,

                "repayment_percent_interval_types" => $repayment_percent_interval_types,

                "time_types" => $time_types,

                "time_type" => $time_type,

                "loan_term" => $loan_term,

                "loan_amount" => $loan_amount,

                "errors" => $errors,

                "products" => $products,

                "productsGroupByCompany" => $productsGroupByCompany,

                "request_results_count" => $request_results_count,

                "previousUrl" => $previousUrl,

                "productsFiltersSingleCounts" => $productsFiltersSingleCounts,

                "percentage_types" => $percentage_types,

                "providing_types" => $providing_types,

                "security_types" => $security_types,

                "repayment_types" => $repayment_types,

                "privileged_term_having_products_count" => $privileged_term_having_products_count,

                "privileged_term_no_having_products_count" => $privileged_term_no_having_products_count,

                "special_projects_having_products_count" => $special_projects_having_products_count,

                "special_projects_no_having_products_count" => $special_projects_no_having_products_count,
            ]);
    }

    /**
     * compare Agric Loans.
     *
     * @return \Illuminate\Http\Response
     */
    public
    function compareAgricLoans(Request $request)
    {
        $belonging_id = 5;

        $currProductByBelongingsView = ProductByBelongingsView::where("belonging_id", $belonging_id)->first();

        $belongings = Belonging::with('productsByBelongingInfo')->get();

        $currBelonging = Belonging::where("id", $belonging_id)->first();

        $time_types = TimeType::all();

        $loanCurrenciesTypes = LoanCurrenciesType::all();

        $yes_no_all_answers = YesNoAllAnswer::all();

        $yes_no_answers = YesNo::all();

        $repayment_loan_interval_types = RepaymentLoanIntervalType::all();

        $repayment_percent_interval_types = RepaymentPercentIntervalType::all();

        $time_type = $request->time_type;

        $loan_term = $request->loan_term;

        $loan_amount = $request->loan_amount;

        $currency = $request->currency;

        if ($time_type == 1 || $time_type == "" || is_null($time_type)) {

            $loan_term_search_in_days = $loan_term;

        } else if ($time_type == 2) {

            $loan_term_search_in_days = $loan_term * 30;

        } else if ($time_type == 3) {

            $loan_term_search_in_days = $loan_term * 365;
        }

        if (count($request->all()) > 0) {
            $validator = Validator::make($request->all(), [

                'loan_term' => 'required|numeric',

                'loan_amount' => 'required|numeric',

                'currency' => 'required',
            ]);

            $errors = $validator->errors();

            if ($errors->count() > 0) {

                $products = NULL;

                $productsGroupByCompany = NULL;

                $request_results_count = 0;
            } else {
                $products = AgricLoan::with('companyInfo')
                    ->with('loanTermFromPeriodicityTypeInfo')
                    ->with('loanTermToPeriodicityTypeInfo');

                if (!is_null($loan_amount)) {
                    $products->where(function ($query) use ($loan_amount) {

                        $query->where('loan_amount_from', '<=', (float)$loan_amount);

                        $query->where('loan_amount_to', '>=', (float)$loan_amount);
                    });
                }
                if (!is_null($loan_term_search_in_days)) {
                    $products->where(function ($query) use ($loan_term_search_in_days) {

                        $query->where('loan_term_from_in_days', '<=', (float)$loan_term_search_in_days);

                        $query->where('loan_term_to_in_days', '>=', (float)$loan_term_search_in_days);
                    });
                }
                if (!is_null($currency)) {
                    $products->where(function ($query) use ($currency) {

                        $query->where('currency', (float)$currency);
                    });
                }

                $products = $products->get();

                $productsGroupByCompanyIds = array_unique($products->pluck('company_id')->toArray());

                $productsGroupByCompany = [];

                foreach ($productsGroupByCompanyIds as $productCompanyId) {
                    $productsGroupByCompany[] = $products->where('company_id', $productCompanyId);
                }

                $request_results_count = $products->count();

                $productsFiltersSingleCounts    =   $this->compareProductsGetSomeFilters($belonging_id,$products);

                $purposeTypes = $productsFiltersSingleCounts["purposeTypes"];

                $percentage_types = $productsFiltersSingleCounts["percentage_types"];

                $providing_types = $productsFiltersSingleCounts["providing_types"];

                $security_types = $productsFiltersSingleCounts["security_types"];

                $repayment_types = $productsFiltersSingleCounts["repayment_types"];

                $privileged_term_having_products_count = $products->where('privileged_term_checked', 1)->count();

                $privileged_term_no_having_products_count = $products->where('privileged_term_checked','!=', 1)->count();

                $special_projects_having_products_count = $products->where('special_projects', 1)->count();

                $special_projects_no_having_products_count = $products->where('special_projects','!=', 1)->count();
            }
        }
        else {
            $validator = Validator::make($request->all(), []);

            $products = NULL;

            $productsGroupByCompany = NULL;

            $request_results_count = 0;

            $productsFiltersSingleCounts = NULL;

            $purposeTypes = NULL;

            $percentage_types = NULL;

            $providing_types = NULL;

            $security_types = NULL;

            $repayment_types = NULL;

            $privileged_term_having_products_count = NULL;

            $privileged_term_no_having_products_count = NULL;

            $special_projects_having_products_count = NULL;

            $special_projects_no_having_products_count = NULL;
        }

        $errors = $validator->errors();

        $previousUrl = $this->loansPreviousUrl($request);

        return view('compare.compareAgricLoans',
            [
                "belongings" => $belongings,

                "currProductByBelongingsView" => $currProductByBelongingsView,

                "currBelonging" => $currBelonging,

                "belonging_id" => $belonging_id,

                "loanCurrenciesTypes" => $loanCurrenciesTypes,

                "yes_no_all_answers" => $yes_no_all_answers,

                "yes_no_answers" => $yes_no_answers,

                "repayment_loan_interval_types" => $repayment_loan_interval_types,

                "repayment_percent_interval_types" => $repayment_percent_interval_types,

                "privileged_term_having_products_count" => $privileged_term_having_products_count,

                "special_projects_having_products_count" => $special_projects_having_products_count,

                "time_types" => $time_types,

                "time_type" => $time_type,

                "loan_term" => $loan_term,

                "loan_amount" => $loan_amount,

                "currency" => $currency,

                "errors" => $errors,

                "products" => $products,

                "productsGroupByCompany" => $productsGroupByCompany,

                "request_results_count" => $request_results_count,

                "previousUrl" => $previousUrl,

                "productsFiltersSingleCounts" => $productsFiltersSingleCounts,

                "purposeTypes" => $purposeTypes,

                "percentage_types" => $percentage_types,

                "providing_types" => $providing_types,

                "security_types" => $security_types,

                "repayment_types" => $repayment_types,

                "privileged_term_having_products_count" => $privileged_term_having_products_count,

                "privileged_term_no_having_products_count" => $privileged_term_no_having_products_count,

                "special_projects_having_products_count" => $special_projects_having_products_count,

                "special_projects_no_having_products_count" => $special_projects_no_having_products_count,
            ]);
    }

    /**
     * compare Consumer Credits.
     *
     * @return \Illuminate\Http\Response
     */
    public
    function compareConsumerCredits(Request $request)
    {
        $belonging_id = 6;

        $currProductByBelongingsView = ProductByBelongingsView::where("belonging_id", $belonging_id)->first();

        $belongings = Belonging::with('productsByBelongingInfo')->get();

        $currBelonging = Belonging::where("id", $belonging_id)->first();

        $time_types = TimeType::all();

        $yes_no_all_answers = YesNoAllAnswer::all();

        $yes_no_answers = YesNo::all();

        $repayment_loan_interval_types = RepaymentLoanIntervalType::all();

        $repayment_percent_interval_types = RepaymentPercentIntervalType::all();

        $time_type = $request->time_type;

        $loan_term = $request->loan_term;

        $loan_amount = $request->loan_amount;

        if ($time_type == 1 || $time_type == "" || is_null($time_type)) {

            $loan_term_search_in_days = $loan_term;

        } else if ($time_type == 2) {

            $loan_term_search_in_days = $loan_term * 30;

        } else if ($time_type == 3) {

            $loan_term_search_in_days = $loan_term * 365;
        }

        if (count($request->all()) > 0) {

            $validator = Validator::make($request->all(), [
                'loan_term' => 'required|numeric',

                'loan_amount' => 'required|numeric',
            ]);

            $errors = $validator->errors();

            if ($errors->count() > 0) {

                $products = NULL;

                $productsGroupByCompany = NULL;

                $request_results_count = 0;

                $productsFiltersSingleCounts = NULL;

                $percentage_types = NULL;

                $providing_types = NULL;

                $security_types = NULL;

                $repayment_types = NULL;

                $privileged_term_having_products_count = NULL;

                $privileged_term_no_having_products_count = NULL;

                $special_projects_having_products_count = NULL;

                $special_projects_no_having_products_count = NULL;
            }
            else {
                $products = ConsumerCredit::with('companyInfo')
                    ->with('loanTermFromPeriodicityTypeInfo')
                    ->with('loanTermToPeriodicityTypeInfo');

                if (!is_null($loan_amount)) {
                    $products->where(function ($query) use ($loan_amount) {

                        $query->where('loan_amount_from', '<=', (float)$loan_amount);

                        $query->where('loan_amount_to', '>=', (float)$loan_amount);
                    });
                }
                if (!is_null($loan_term_search_in_days)) {
                    $products->where(function ($query) use ($loan_term_search_in_days) {
                        $query->where('loan_term_from_in_days', '<=', (float)$loan_term_search_in_days);
                        $query->where('loan_term_to_in_days', '>=', (float)$loan_term_search_in_days);
                    });
                }

                $products = $products->get();

                $productsGroupByCompanyIds = array_unique($products->pluck('company_id')->toArray());

                $productsGroupByCompany = [];

                foreach ($productsGroupByCompanyIds as $productCompanyId) {
                    $productsGroupByCompany[] = $products->where('company_id', $productCompanyId);
                }

                $request_results_count = $products->count();

                $productsFiltersSingleCounts    =   $this->compareProductsGetSomeFilters($belonging_id,$products);

                $percentage_types = $productsFiltersSingleCounts["percentage_types"];

                $providing_types = $productsFiltersSingleCounts["providing_types"];

                $security_types = $productsFiltersSingleCounts["security_types"];

                $repayment_types = $productsFiltersSingleCounts["repayment_types"];

                $privileged_term_having_products_count = $products->where('privileged_term_checked', 1)->count();

                $privileged_term_no_having_products_count = $products->where('privileged_term_checked','!=', 1)->count();

                $special_projects_having_products_count = $products->where('special_projects', 1)->count();

                $special_projects_no_having_products_count = $products->where('special_projects','!=', 1)->count();
            }
        }
        else {
            $validator = Validator::make($request->all(), []);

            $products = NULL;

            $productsGroupByCompany = NULL;

            $request_results_count = 0;

            $productsFiltersSingleCounts = NULL;

            $percentage_types = NULL;

            $providing_types = NULL;

            $security_types = NULL;

            $repayment_types = NULL;

            $privileged_term_having_products_count = NULL;

            $privileged_term_no_having_products_count = NULL;

            $special_projects_having_products_count = NULL;

            $special_projects_no_having_products_count = NULL;
        }

        $errors = $validator->errors();

        $previousUrl = $this->loansPreviousUrl($request);

        return view('compare.compareConsumerCredits',
            [
                "belongings" => $belongings,

                "currProductByBelongingsView" => $currProductByBelongingsView,

                "currBelonging" => $currBelonging,

                "belonging_id" => $belonging_id,

                "yes_no_all_answers" => $yes_no_all_answers,

                "yes_no_answers" => $yes_no_answers,

                "repayment_loan_interval_types" => $repayment_loan_interval_types,

                "repayment_percent_interval_types" => $repayment_percent_interval_types,

                "time_types" => $time_types,

                "time_type" => $time_type,

                "loan_term" => $loan_term,

                "loan_amount" => $loan_amount,

                "errors" => $errors,

                "products" => $products,

                "productsGroupByCompany" => $productsGroupByCompany,

                "request_results_count" => $request_results_count,

                "previousUrl" => $previousUrl,

                "productsFiltersSingleCounts" => $productsFiltersSingleCounts,

                "percentage_types" => $percentage_types,

                "providing_types" => $providing_types,

                "security_types" => $security_types,

                "repayment_types" => $repayment_types,

                "privileged_term_having_products_count" => $privileged_term_having_products_count,

                "privileged_term_no_having_products_count" => $privileged_term_no_having_products_count,

                "special_projects_having_products_count" => $special_projects_having_products_count,

                "special_projects_no_having_products_count" => $special_projects_no_having_products_count,
            ]);
    }

    /**
     * compare Loan Refinancings.
     *
     * @return \Illuminate\Http\Response
     */
    public
    function compareLoanRefinancings(Request $request)
    {
        $belonging_id = 11;

        $currProductByBelongingsView = ProductByBelongingsView::where("belonging_id", $belonging_id)->first();

        $belongings = Belonging::with('productsByBelongingInfo')->get();

        $currBelonging = Belonging::where("id", $belonging_id)->first();

        $time_types = TimeType::all();

        $loanCurrenciesTypes = LoanCurrenciesType::all();

        $loanRefinancingPurposeType = LoanRefinancingPurposeType::all();

        $repayment_types = RepaymentType::all();

        $percentage_types = PercentageType::all();

        $providing_types = ProvidingType::all();

        $security_types = SecurityType::all();

        $yes_no_all_answers = YesNoAllAnswer::all();

        $yes_no_answers = YesNo::all();

        $repayment_loan_interval_types = RepaymentLoanIntervalType::all();

        $repayment_percent_interval_types = RepaymentPercentIntervalType::all();

        $special_projects_having_products_count = LoanRefinancing::where('special_projects', 1)->count();

        $privileged_term_having_products_count = LoanRefinancing::where('privileged_term_checked', 1)->count();

        $time_type = $request->time_type;

        $loan_term = $request->loan_term;

        $loan_amount = $request->loan_amount;

        $currency = $request->currency;

        if ($time_type == 1 || $time_type == "" || is_null($time_type)) {

            $loan_term_search_in_days = $loan_term;

        } else if ($time_type == 2) {

            $loan_term_search_in_days = $loan_term * 30;

        } else if ($time_type == 3) {

            $loan_term_search_in_days = $loan_term * 365;
        }

        if (count($request->all()) > 0) {
            $validator = Validator::make($request->all(), [

                'loan_term' => 'required|numeric',

                'loan_amount' => 'required|numeric',

                'currency' => 'required',
            ]);

            $errors = $validator->errors();

            if ($errors->count() > 0) {

                $products = NULL;

                $productsGroupByCompany = NULL;

                $request_results_count = 0;
            } else {
                $products = LoanRefinancing::with('companyInfo')
                    ->with('loanTermFromPeriodicityTypeInfo')
                    ->with('loanTermToPeriodicityTypeInfo');

                if (!is_null($loan_amount)) {
                    $products->where(function ($query) use ($loan_amount) {

                        $query->where('loan_amount_from', '<=', (float)$loan_amount);

                        $query->where('loan_amount_to', '>=', (float)$loan_amount);
                    });
                }
                if (!is_null($loan_term_search_in_days)) {
                    $products->where(function ($query) use ($loan_term_search_in_days) {

                        $query->where('loan_term_from_in_days', '<=', (float)$loan_term_search_in_days);

                        $query->where('loan_term_to_in_days', '>=', (float)$loan_term_search_in_days);
                    });
                }
//                if (!is_null($currency)) {
//                    $products->where(function ($query) use ($currency) {
//
//                        $query->where('currency', (float)$currency);
//                    });
//                }

                $products = $products->get();

                $productsGroupByCompanyIds = array_unique($products->pluck('company_id')->toArray());

                $productsGroupByCompany = [];

                foreach ($productsGroupByCompanyIds as $productCompanyId) {
                    $productsGroupByCompany[] = $products->where('company_id', $productCompanyId);
                }

                $request_results_count = $products->count();
            }
        } else {
            $validator = Validator::make($request->all(), []);

            $products = NULL;

            $productsGroupByCompany = NULL;

            $request_results_count = 0;
        }

        $errors = $validator->errors();
        //Session::forget('previousUrl');

        $previousUrl = $this->loansPreviousUrl($request);

        return view('compare.compareLoanRefinancings',
            [
                "belongings" => $belongings,

                "currProductByBelongingsView" => $currProductByBelongingsView,

                "currBelonging" => $currBelonging,

                "belonging_id" => $belonging_id,

                "loanRefinancingPurposeType" => $loanRefinancingPurposeType,

                "loanCurrenciesTypes" => $loanCurrenciesTypes,

                "repayment_types" => $repayment_types,

                "percentage_types" => $percentage_types,

                "providing_types" => $providing_types,

                "security_types" => $security_types,

                "yes_no_all_answers" => $yes_no_all_answers,

                "yes_no_answers" => $yes_no_answers,

                "repayment_loan_interval_types" => $repayment_loan_interval_types,

                "repayment_percent_interval_types" => $repayment_percent_interval_types,

                "privileged_term_having_products_count" => $privileged_term_having_products_count,

                "special_projects_having_products_count" => $special_projects_having_products_count,

                "time_types" => $time_types,

                "time_type" => $time_type,

                "loan_term" => $loan_term,

                "loan_amount" => $loan_amount,

                "currency" => $currency,

                "errors" => $errors,

                "products" => $products,

                "productsGroupByCompany" => $productsGroupByCompany,

                "request_results_count" => $request_results_count,

                "previousUrl" => $previousUrl,
            ]);
    }

    /**
     * compare Deposits.
     *
     * @return \Illuminate\Http\Response
     */
    public
    function compareDeposits(Request $request)
    {
        $belonging_id = 7;

        $currProductByBelongingsView = ProductByBelongingsView::where("belonging_id", $belonging_id)->first();

        $belongings = Belonging::with('productsByBelongingInfo')->get();

        $currBelonging = Belonging::where("id", $belonging_id)->first();

        $time_types = TimeType::all();

        $loanCurrenciesTypes = LoanCurrenciesType::all();

        $loanRefinancingPurposeType = LoanRefinancingPurposeType::all();

        $repayment_types = RepaymentType::all();

        $percentage_types = PercentageType::all();

        $providing_types = ProvidingType::all();

        $security_types = SecurityType::all();

        $yes_no_all_answers = YesNoAllAnswer::all();

        $yes_no_answers = YesNo::all();

        $repayment_loan_interval_types = RepaymentLoanIntervalType::all();

        $repayment_percent_interval_types = RepaymentPercentIntervalType::all();

        $deposit_types_list = DepositTypesList::all();

        $deposit_interest_rates_payments = DepositInterestRatesPayment::all();

        $deposit_capitalizations_list = DepositCapitalizationsList::all();

        $deposits_specials_list = DepositsSpecialsList::all();

        $deposit_money_min = Deposit::min('deposit_money_from');

        $deposit_money_max = Deposit::max('deposit_money_to');

        $time_type = $request->time_type;

        $loan_term = $request->loan_term;

        $loan_amount = $request->loan_amount;

        $currency = $request->currency;

        $deposit_type = $request->deposit_type;

        if ($time_type == 1 || $time_type == "" || is_null($time_type)) {

            $loan_term_search_in_days = $loan_term;

        } else if ($time_type == 2) {

            $loan_term_search_in_days = $loan_term * 30;

        } else if ($time_type == 3) {

            $loan_term_search_in_days = $loan_term * 365;
        }

        if (count($request->all()) > 0) {
            $validator = Validator::make($request->all(), [

                'loan_term' => 'required|numeric',

                'loan_amount' => 'required|numeric',

                'currency' => 'required',

                'deposit_type' => 'required',
            ]);

            $errors = $validator->errors();

            if ($errors->count() > 0) {

                $products = NULL;

                $productsGroupByCompany = NULL;

                $request_results_count = 0;
            } else {
                $products = Deposit::with('companyInfo')
                    ->with('loanTermFromPeriodicityTypeInfo')
                    ->with('loanTermToPeriodicityTypeInfo');

                if (!is_null($loan_amount)) {
                    $products->where(function ($query) use ($loan_amount) {

                        $query->where('deposit_money_from', '<=', (float)$loan_amount);

                        $query->where('deposit_money_to', '>=', (float)$loan_amount);
                    });
                }
                if (!is_null($loan_term_search_in_days)) {
                    $products->where(function ($query) use ($loan_term_search_in_days) {

                        $query->where('deposit_term_from_in_days', '<=', (float)$loan_term_search_in_days);

                        $query->where('deposit_term_to_in_days', '>=', (float)$loan_term_search_in_days);
                    });
                }
                if (!is_null($currency)) {
                    $products->where(function ($query) use ($currency) {

                        $query->where('currency', (float)$currency);
                    });
                }
                if (!is_null($deposit_type)) {
                    $products->where(function ($query) use ($deposit_type) {

                        $query->where('deposit_type', (float)$deposit_type);
                    });
                }

                $products = $products->get();

                $productsGroupByCompanyIds = array_unique($products->pluck('company_id')->toArray());

                $productsGroupByCompany = [];

                foreach ($productsGroupByCompanyIds as $productCompanyId) {
                    $productsGroupByCompany[] = $products->where('company_id', $productCompanyId);
                }

                $request_results_count = $products->count();
            }
        } else {
            $validator = Validator::make($request->all(), []);

            $products = NULL;

            $productsGroupByCompany = NULL;

            $request_results_count = 0;
        }

        $errors = $validator->errors();

        $previousUrl = $this->loansPreviousUrl($request);

        return view('compare.compareDeposits',
            [
                "belongings" => $belongings,

                "currProductByBelongingsView" => $currProductByBelongingsView,

                "currBelonging" => $currBelonging,

                "belonging_id" => $belonging_id,

                "loanRefinancingPurposeType" => $loanRefinancingPurposeType,

                "loanCurrenciesTypes" => $loanCurrenciesTypes,

                "repayment_types" => $repayment_types,

                "percentage_types" => $percentage_types,

                "providing_types" => $providing_types,

                "security_types" => $security_types,

                "yes_no_all_answers" => $yes_no_all_answers,

                "yes_no_answers" => $yes_no_answers,

                "repayment_loan_interval_types" => $repayment_loan_interval_types,

                "repayment_percent_interval_types" => $repayment_percent_interval_types,

                "deposit_types_list" => $deposit_types_list,

                "deposit_interest_rates_payments" => $deposit_interest_rates_payments,

                "deposit_capitalizations_list" => $deposit_capitalizations_list,

                "deposits_specials_list" => $deposits_specials_list,

                "deposit_money_min" => $deposit_money_min,

                "deposit_money_max" => $deposit_money_max,

                "time_types" => $time_types,

                "time_type" => $time_type,

                "loan_term" => $loan_term,

                "loan_amount" => $loan_amount,

                "currency" => $currency,

                "deposit_type" => $deposit_type,

                "errors" => $errors,

                "products" => $products,

                "productsGroupByCompany" => $productsGroupByCompany,

                "request_results_count" => $request_results_count,

                "previousUrl" => $previousUrl,
            ]);
    }

    /**
     * compare Online Loans.
     *
     * @return \Illuminate\Http\Response
     */
    function compareOnlineLoans(Request $request)
    {
        $belonging_id = 13;

        $currProductByBelongingsView = ProductByBelongingsView::where("belonging_id", $belonging_id)->first();

        $belongings = Belonging::with('productsByBelongingInfo')->get();

        $currBelonging = Belonging::where("id", $belonging_id)->first();

        $time_types = TimeType::all();

        $yes_no_all_answers = YesNoAllAnswer::all();

        $yes_no_answers = YesNo::all();

        $repayment_loan_interval_types = RepaymentLoanIntervalType::all();

        $repayment_percent_interval_types = RepaymentPercentIntervalType::all();

        $time_type = $request->time_type;

        $loan_term = $request->loan_term;

        $loan_amount = $request->loan_amount;

        if ($time_type == 1 || $time_type == "" || is_null($time_type)) {

            $loan_term_search_in_days = $loan_term;

        } else if ($time_type == 2) {

            $loan_term_search_in_days = $loan_term * 30;

        } else if ($time_type == 3) {

            $loan_term_search_in_days = $loan_term * 365;
        }

        if (count($request->all()) > 0) {
            $validator = Validator::make($request->all(), [
                'loan_term' => 'required|numeric',

                'loan_amount' => 'required|numeric',
            ]);

            $errors = $validator->errors();

            if ($errors->count() > 0) {

                $products = NULL;

                $productsGroupByCompany = NULL;

                $request_results_count = 0;

                $productsFiltersSingleCounts = NULL;

                $percentage_types = NULL;

                $providing_types = NULL;

                $security_types = NULL;

                $repayment_types = NULL;

                $privileged_term_having_products_count = NULL;

                $privileged_term_no_having_products_count = NULL;

                $special_projects_having_products_count = NULL;

                $special_projects_no_having_products_count = NULL;
            }
            else {
                $products = OnlineLoan::with('companyInfo')
                    ->with('loanTermFromPeriodicityTypeInfo')
                    ->with('loanTermToPeriodicityTypeInfo');

                if (!is_null($loan_amount)) {
                    $products->where(function ($query) use ($loan_amount) {
                        $query->where('loan_amount_from', '<=', (float)$loan_amount);

                        $query->where('loan_amount_to', '>=', (float)$loan_amount);
                    });
                }
                if (!is_null($loan_term_search_in_days)) {
                    $products->where(function ($query) use ($loan_term_search_in_days) {
                        $query->where('loan_term_from_in_days', '<=', (float)$loan_term_search_in_days);

                        $query->where('loan_term_to_in_days', '>=', (float)$loan_term_search_in_days);
                    });
                }

                $products = $products->get();

                $productsGroupByCompanyIds = array_unique($products->pluck('company_id')->toArray());

                $productsGroupByCompany = [];

                foreach ($productsGroupByCompanyIds as $productCompanyId) {
                    $productsGroupByCompany[] = $products->where('company_id', $productCompanyId);
                }

                $request_results_count = $products->count();

                $productsFiltersSingleCounts    =   $this->compareProductsGetSomeFilters($belonging_id,$products);

                $percentage_types = $productsFiltersSingleCounts["percentage_types"];

                $providing_types = $productsFiltersSingleCounts["providing_types"];

                $security_types = $productsFiltersSingleCounts["security_types"];

                $repayment_types = $productsFiltersSingleCounts["repayment_types"];

                $privileged_term_having_products_count = $products->where('privileged_term_checked', 1)->count();

                $privileged_term_no_having_products_count = $products->where('privileged_term_checked','!=', 1)->count();

                $special_projects_having_products_count = $products->where('special_projects', 1)->count();

                $special_projects_no_having_products_count = $products->where('special_projects','!=', 1)->count();
            }
        }
        else {
            $validator = Validator::make($request->all(), []);

            $products = NULL;

            $productsGroupByCompany = NULL;

            $request_results_count = 0;

            $productsFiltersSingleCounts = NULL;

            $percentage_types = NULL;

            $providing_types = NULL;

            $security_types = NULL;

            $repayment_types = NULL;

            $privileged_term_having_products_count = NULL;

            $privileged_term_no_having_products_count = NULL;

            $special_projects_having_products_count = NULL;

            $special_projects_no_having_products_count = NULL;
        }

        $errors = $validator->errors();

        $previousUrl = $this->loansPreviousUrl($request);

        return view('compare.compareOnlineLoans',
            [
                "belongings" => $belongings,

                "currProductByBelongingsView" => $currProductByBelongingsView,

                "currBelonging" => $currBelonging,

                "belonging_id" => $belonging_id,

                "yes_no_all_answers" => $yes_no_all_answers,

                "yes_no_answers" => $yes_no_answers,

                "repayment_loan_interval_types" => $repayment_loan_interval_types,

                "repayment_percent_interval_types" => $repayment_percent_interval_types,

                "special_projects_having_products_count" => $special_projects_having_products_count,

                "privileged_term_having_products_count" => $privileged_term_having_products_count,

                "time_types" => $time_types,

                "time_type" => $time_type,

                "loan_term" => $loan_term,

                "loan_amount" => $loan_amount,

                "errors" => $errors,

                "products" => $products,

                "productsGroupByCompany" => $productsGroupByCompany,

                "request_results_count" => $request_results_count,

                "previousUrl" => $previousUrl,

                "productsFiltersSingleCounts" => $productsFiltersSingleCounts,

                "percentage_types" => $percentage_types,

                "providing_types" => $providing_types,

                "security_types" => $security_types,

                "repayment_types" => $repayment_types,

                "privileged_term_having_products_count" => $privileged_term_having_products_count,

                "privileged_term_no_having_products_count" => $privileged_term_no_having_products_count,

                "special_projects_having_products_count" => $special_projects_having_products_count,

                "special_projects_no_having_products_count" => $special_projects_no_having_products_count,
            ]);
    }

    /**
     * compare Mortgages.
     *
     * @return \Illuminate\Http\Response
     */
    function compareMortgages(Request $request)
    {
        $belonging_id = 8;

        $currProductByBelongingsView = ProductByBelongingsView::where("belonging_id", $belonging_id)->first();

        $belongings = Belonging::with('productsByBelongingInfo')->get();

        $currBelonging = Belonging::where("id", $belonging_id)->first();

        $time_types = TimeType::all();

        $loanCurrenciesTypes = LoanCurrenciesType::all();

        $mortgagePurposeTypes = MortgagePurposeType::all();

        $yes_no_all_answers = YesNoAllAnswer::all();

        $yes_no_answers = YesNo::all();

        $loan_amount_min = Mortgage::min('loan_amount_from');

        $loan_amount_max = Mortgage::max('loan_amount_to');

        $repayment_loan_interval_types = RepaymentLoanIntervalType::all();

        $repayment_percent_interval_types = RepaymentPercentIntervalType::all();

        $purpose_type = $request->purpose_type;

        $currency = $request->currency;

        $time_type = $request->time_type;

        $loan_term = $request->loan_term;

        $loan_amount = $request->loan_amount;

        $amount = $request->amount;

        $prepayment = $request->prepayment;

        if (is_null($prepayment)) {

            $prepayment_final = 0;
        } else {
            $prepayment_final = $prepayment;
        }

        if ($time_type == 1 || $time_type == "" || is_null($time_type)) {

            $loan_term_search_in_days = $loan_term;

        } else if ($time_type == 2) {

            $loan_term_search_in_days = $loan_term * 30;

        } else if ($time_type == 3) {

            $loan_term_search_in_days = $loan_term * 365;
        }

        if (count($request->all()) > 0) {
            $validator = Validator::make($request->all(), [
                'loan_term' => 'required|numeric',

                'loan_amount' => 'required|numeric',

                'amount' => 'required|numeric',

                'prepayment' => 'nullable|numeric',

                'purpose_type' => 'required',

                'currency' => 'required',
            ]);

            $errors = $validator->errors();

            if ($errors->count() > 0) {

                $products = NULL;

                $productsGroupByCompany = NULL;

                $request_results_count = 0;

                $productsFiltersSingleCounts = NULL;

                $percentage_types = NULL;

                $providing_types = NULL;

                $security_types = NULL;

                $repayment_types = NULL;

                $privileged_term_having_products_count = NULL;

                $privileged_term_no_having_products_count = NULL;

                $special_projects_having_products_count = NULL;

                $special_projects_no_having_products_count = NULL;
            }
            else {
                $products = Mortgage::with('companyInfo')
                    ->with('loanTermFromPeriodicityTypeInfo')
                    ->with('loanTermToPeriodicityTypeInfo');

                if (!is_null($loan_amount)) {
                    $products->where(function ($query) use ($loan_amount) {
                        $query->where('loan_amount_from', '<=', (float)$loan_amount);

                        $query->where('loan_amount_to', '>=', (float)$loan_amount);
                    });
                }
                if (!is_null($loan_term_search_in_days)) {
                    $products->where(function ($query) use ($loan_term_search_in_days) {
                        $query->where('loan_term_from_in_days', '<=', (float)$loan_term_search_in_days);

                        $query->where('loan_term_to_in_days', '>=', (float)$loan_term_search_in_days);
                    });
                }
                if (!is_null($currency)) {
                    $products->where(function ($query) use ($currency) {
                        $query->where('currency', (float)$currency);
                    });
                }

                $products = $products->get();

                $productsGroupByCompanyIds = array_unique($products->pluck('company_id')->toArray());

                $productsGroupByCompany = [];

                foreach ($productsGroupByCompanyIds as $productCompanyId) {
                    $productsGroupByCompany[] = $products->where('company_id', $productCompanyId);
                }

                $request_results_count = $products->count();

                $productsFiltersSingleCounts    =   $this->compareProductsGetSomeFilters($belonging_id,$products);

                $percentage_types = $productsFiltersSingleCounts["percentage_types"];

                $providing_types = $productsFiltersSingleCounts["providing_types"];

                $security_types = $productsFiltersSingleCounts["security_types"];

                $repayment_types = $productsFiltersSingleCounts["repayment_types"];

                $privileged_term_having_products_count = $products->where('privileged_term_checked', 1)->count();

                $privileged_term_no_having_products_count = $products->where('privileged_term_checked','!=', 1)->count();

                $special_projects_having_products_count = $products->where('special_projects', 1)->count();

                $special_projects_no_having_products_count = $products->where('special_projects','!=', 1)->count();
            }
        }
        else {
            $validator = Validator::make($request->all(), []);

            $products = NULL;

            $productsGroupByCompany = NULL;

            $request_results_count = 0;

            $productsFiltersSingleCounts = NULL;

            $percentage_types = NULL;

            $providing_types = NULL;

            $security_types = NULL;

            $repayment_types = NULL;

            $privileged_term_having_products_count = NULL;

            $privileged_term_no_having_products_count = NULL;

            $special_projects_having_products_count = NULL;

            $special_projects_no_having_products_count = NULL;
        }

        $errors = $validator->errors();

        $previousUrl = $this->loansPreviousUrl($request);

        return view('compare.compareMortgages',
            [
                "belongings" => $belongings,

                "currProductByBelongingsView" => $currProductByBelongingsView,

                "currBelonging" => $currBelonging,

                "belonging_id" => $belonging_id,

                "mortgagePurposeTypes" => $mortgagePurposeTypes,

                "yes_no_all_answers" => $yes_no_all_answers,

                "yes_no_answers" => $yes_no_answers,

                "loan_amount_min" => $loan_amount_min,

                "loan_amount_max" => $loan_amount_max,

                "repayment_loan_interval_types" => $repayment_loan_interval_types,

                "repayment_percent_interval_types" => $repayment_percent_interval_types,

                "time_types" => $time_types,

                "loanCurrenciesTypes" => $loanCurrenciesTypes,

                "purpose_type" => $purpose_type,

                "currency" => $currency,

                "time_type" => $time_type,

                "loan_term" => $loan_term,

                "amount" => $amount,

                "loan_amount" => $loan_amount,

                "prepayment" => $prepayment,

                "errors" => $errors,

                "products" => $products,

                "productsGroupByCompany" => $productsGroupByCompany,

                "previousUrl" => $previousUrl,

                "request_results_count" => $request_results_count,

                "productsFiltersSingleCounts" => $productsFiltersSingleCounts,

                "percentage_types" => $percentage_types,

                "providing_types" => $providing_types,

                "security_types" => $security_types,

                "repayment_types" => $repayment_types,

                "privileged_term_having_products_count" => $privileged_term_having_products_count,

                "privileged_term_no_having_products_count" => $privileged_term_no_having_products_count,

                "special_projects_having_products_count" => $special_projects_having_products_count,

                "special_projects_no_having_products_count" => $special_projects_no_having_products_count,
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

        $payment_card_currencies_types = PaymentCardCurrency::all();

        $payment_card_types = PaymentCardType::all();

        $payment_card_product_types = PaymentCardProductType::all();

        $payment_card_regions = PaymentCardRegion::all();

        $payment_extra_cards = PaymentExtraCard::all();

        $payment_specials_cards = PaymentSpecialCard::all();

        $currency  = $request->input('currency');

        if (count($request->all()) > 0) {
            $validator = Validator::make($request->all(), [
                'currency' => 'required',
            ]);

            $errors = $validator->errors();

            if ($errors->count() > 0) {
                $products = NULL;

                $productsGroupByCompany = NULL;

                $request_results_count = 0;
            }
            else {
                $products = PaymentCard::with('companyInfo')
                    ->with('creditLineInfo')
                    ->with('productsPaymentCardsType')
                    ->with('productsPaymentCardsCurrencies')
                    ->with('productsPaymentCardsCardType')
                    ->with('productsPaymentCardsRegion')
                    ->with('productsSpecialsCardsType')
                    ->with('productsPaymentCardsExtraType')
                    ->with('attachmentCardInfo');

                $products = $products->get();

                $productsGroupByCompanyIds = array_unique($products->pluck('company_id')->toArray());

                $productsGroupByCompany = [];

                foreach ($productsGroupByCompanyIds as $productCompanyId) {
                    $productsGroupByCompany[] = $products->where('company_id', $productCompanyId);
                }

                $request_results_count = $products->count();
            }
        }
        else {
            $validator = Validator::make($request->all(), []);

            $products = NULL;

            $productsGroupByCompany = NULL;

            $request_results_count = 0;
        }

        $errors = $validator->errors();

        $previousUrl = $this->loansPreviousUrl($request);

        return view('compare.comparePaymentCards',
            [
                "belongings" => $belongings,

                "currProductByBelongingsView" => $currProductByBelongingsView,

                "belonging_id" => $belonging_id,

                "time_types" => $time_types,

                "payment_card_currencies_types" => $payment_card_currencies_types,

                "payment_card_types" => $payment_card_types,

                "payment_card_product_types" => $payment_card_product_types,

                "payment_card_regions" => $payment_card_regions,

                "payment_extra_cards" => $payment_extra_cards,

                "payment_specials_cards" => $payment_specials_cards,

                "currency" => $currency,

                "errors" => $errors,

                "products" => $products,

                "productsGroupByCompany" => $productsGroupByCompany,

                "request_results_count" => $request_results_count,

                "previousUrl" => $previousUrl,
            ]);
    }

    /**
     * compare Money Transfers.
     *
     * @return \Illuminate\Http\Response
     */
    public
    function compareMoneyTransfers(Request $request)
    {
        $belonging_id = 10;

        $currProductByBelongingsView = ProductByBelongingsView::where("belonging_id", $belonging_id)->first();

        $belongings = Belonging::with('productsByBelongingInfo')->get();

        $currBelonging = Belonging::where("id", $belonging_id)->first();

        $countries = Country::all();

        $money_transfer_currencies_all_types = MoneyTransferCurrenciesAllType::all();

        $transfer_systems = TransferSystem::all();

        $transfer_banks = TransferBank::all();

        $money_transfer_amount_min = MoneyTransfer::min('money_transfer_amount_from');

        $money_transfer_amount_max = MoneyTransfer::max('money_transfer_amount_to');

        if(is_null($money_transfer_amount_min)){
            $money_transfer_amount_min  =   0;
        }

        $yes_no_all_answers = YesNoAllAnswer::all();

        $yes_no_answers = YesNo::all();

        $currency = $request->currency;

        $country = $request->country;

        $transfer_amount = $request->transfer_amount;

        if (count($request->all()) > 0) {
            $validator = Validator::make($request->all(), [

                'transfer_amount' => 'required|numeric',

                'country' => 'required',

                'currency' => 'required',
            ]);

            $errors = $validator->errors();

            if ($errors->count() > 0) {

                $products = NULL;

                $productsGroupByCompany = NULL;

                $request_results_count = 0;
            }
            else {
                $products = MoneyTransfer::with('companyInfo')
                    ->with('transferType')
                    ->with('countriesInfo');

                if (!is_null($transfer_amount)) {
                    $products->where(function ($query) use ($transfer_amount) {

                        $query->where('money_transfer_amount_from', '<=', (float)$transfer_amount);

                        $query->where('money_transfer_amount_to', '>=', (float)$transfer_amount);
                    });
                }

                if ( !is_null($country)) {
                    $products->whereHas('countriesInfo', function ($q) use ($country) {

                        $q->where('country_id', 1);
                     });
                }
//                if ( !is_null($currency)) {
//                    $products->whereHas('currenciesInfo', function ($q) use ($currency) {
//
//                        $q->where('currency_id', $currency);
//                     });
//                }

                $products = $products->get();

                $productsGroupByCompanyIds = array_unique($products->pluck('company_id')->toArray());

                $productsGroupByCompany = [];

                foreach ($productsGroupByCompanyIds as $productCompanyId) {
                    $productsGroupByCompany[] = $products->where('company_id', $productCompanyId);
                }

                $request_results_count = $products->count();
            }
        }
        else {
            $validator = Validator::make($request->all(), []);

            $products = NULL;

            $productsGroupByCompany = NULL;

            $request_results_count = 0;
        }

        $errors = $validator->errors();

        $previousUrl = $this->loansPreviousUrl($request);

        return view('compare.compareMoneyTransfers',
            [
                "belongings" => $belongings,

                "currProductByBelongingsView" => $currProductByBelongingsView,

                "currBelonging" => $currBelonging,

                "belonging_id" => $belonging_id,

                "yes_no_all_answers" => $yes_no_all_answers,

                "yes_no_answers" => $yes_no_answers,

                "countries" => $countries,

                "money_transfer_currencies_all_types" => $money_transfer_currencies_all_types,

                "transfer_systems" => $transfer_systems,

                "transfer_banks" => $transfer_banks,

                "money_transfer_amount_min" => $money_transfer_amount_min,

                "money_transfer_amount_max" => $money_transfer_amount_max,

                "currency" => $currency,

                "country" => $country,

                "transfer_amount" => $transfer_amount,

                "errors" => $errors,

                "products" => $products,

                "productsGroupByCompany" => $productsGroupByCompany,

                "request_results_count" => $request_results_count,

                "previousUrl" => $previousUrl,
            ]);
    }

    /**
     * compare Travel Insurances.
     *
     * @return \Illuminate\Http\Response
     */
    function compareTravelInsurances(Request $request)
    {
        $belonging_id = 12;

        $currProductByBelongingsView = ProductByBelongingsView::where("belonging_id", $belonging_id)->first();

        $belongings = Belonging::with('productsByBelongingInfo')->get();

        $currBelonging = Belonging::where("id", $belonging_id)->first();

        $countries = Country::all();

        $time_types = TimeType::all();

        $yes_no_all_answers = YesNoAllAnswer::all();

        $yes_no_answers = YesNo::all();

        $non_recoverable_amount_having_products_count = TravelInsurance::where('non_recoverable_amount','!=' , 2)->count();

        $time_type = $request->time_type;

        $loan_term = $request->loan_term;

        $age = $request->age;

        $country = $request->country;

        if ($time_type == 1 || $time_type == "" || is_null($time_type)) {

            $loan_term_search_in_days = $loan_term;

        } else if ($time_type == 2) {

            $loan_term_search_in_days = $loan_term * 30;

        } else if ($time_type == 3) {

            $loan_term_search_in_days = $loan_term * 365;
        }

        if (count($request->all()) > 0) {
            $validator = Validator::make($request->all(), [
                'loan_term' => 'required|numeric',

                'age' => 'required|numeric',
            ]);

            $errors = $validator->errors();

            if ($errors->count() > 0) {

                $products = NULL;

                $productsGroupByCompany = NULL;

                $request_results_count = 0;
            } else {
                $products = TravelInsurance::with('companyInfo');
//
//                if (!is_null($loan_amount)) {
//                    $products->where(function ($query) use ($loan_amount) {
//
//                        $query->where('loan_amount_from', '<=', (float)$loan_amount);
//
//                        $query->where('loan_amount_to', '>=', (float)$loan_amount);
//                    });
//                }
//                if (!is_null($loan_term_search_in_days)) {
//                    $products->where(function ($query) use ($loan_term_search_in_days) {
//                        $query->where('loan_term_from_in_days', '<=', (float)$loan_term_search_in_days);
//                        $query->where('loan_term_to_in_days', '>=', (float)$loan_term_search_in_days);
//                    });
//                }

                $products = $products->get();

                $productsGroupByCompanyIds = array_unique($products->pluck('company_id')->toArray());

                $productsGroupByCompany = [];

                foreach ($productsGroupByCompanyIds as $productCompanyId) {
                    $productsGroupByCompany[] = $products->where('company_id', $productCompanyId);
                }

                $request_results_count = $products->count();
            }
        } else {
            $validator = Validator::make($request->all(), []);

            $products = NULL;

            $productsGroupByCompany = NULL;

            $request_results_count = 0;
        }

        $errors = $validator->errors();

        $previousUrl = $this->loansPreviousUrl($request);

        return view('compare.compareTravelInsurances',
            [
                "belongings" => $belongings,

                "currProductByBelongingsView" => $currProductByBelongingsView,

                "currBelonging" => $currBelonging,

                "belonging_id" => $belonging_id,

                "yes_no_all_answers" => $yes_no_all_answers,

                "yes_no_answers" => $yes_no_answers,

                "countries" => $countries,

                "non_recoverable_amount_having_products_count" => $non_recoverable_amount_having_products_count,

                "time_types" => $time_types,

                "time_type" => $time_type,

                "loan_term" => $loan_term,

                "age" => $age,

                "country" => $country,

                "errors" => $errors,

                "products" => $products,

                "productsGroupByCompany" => $productsGroupByCompany,

                "request_results_count" => $request_results_count,

                "previousUrl" => $previousUrl,
            ]);
    }
}