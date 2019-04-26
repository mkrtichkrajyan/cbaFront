<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchCarLoansRequest;
use App\Models\AbsoluteAmountORPercentOnlyPayType;
use App\Models\AbsoluteAmountOrPercentPayType;
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
use App\Models\DocumentList;
use App\Models\GoldAssayType;
use App\Models\GoldLoan;
use App\Models\GoldPledgeType;
use App\Models\LoanCurrenciesType;
use App\Models\LoanRefinancing;
use App\Models\LoanRefinancingPurposeType;
use App\Models\LoanServicePayTypes;
use App\Models\LoansInformMsg;
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
use App\Models\PeriodicityType;
use App\Models\ProductByBelongingsView;
use App\Models\ProductsVariation;
use App\Models\ProvidingType;
use App\Models\PurposeType;
use App\Models\RepaymentLoanIntervalType;
use App\Models\RepaymentPercentIntervalType;
use App\Models\RepaymentType;
use App\Models\SecurityType;
use App\Models\Social;
use App\Models\SpecialProject;
use App\Models\StudentLoan;
use App\Models\TimeType;
use App\Models\TransferBank;
use App\Models\TransferSystem;
use App\Models\TransferType;
use App\Models\TravelInsurance;
use App\Models\TravelInsurancesVariation;
use App\Models\YesNo;
use App\Models\YesNoAllAnswer;
use App\Traits\ProductsCompareInnerTrait;
use App\Traits\ProductsFiltersTrait;
use App\Traits\ProductTrait;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class HomeController extends MainController
{
    use ProductTrait;

    use ProductsCompareInnerTrait;

    use ProductsFiltersTrait;

    public function __construct()
    {
        $this->getloanCurrenciesTypes();

        $baseline_person_img = "img/baseline-person.svg";

        $main_loans_inform_msg = LoansInformMsg::where('id', 1)->first()->content;

        $share_title = "Ֆինանսական տեղեկատու համակարգ";

        $socials = Social::first();

        $other_suggestions_open_close_global_open_text = "Բացել";

        $other_suggestions_open_close_global_close_text = "Փակել";

        View::share('baseline_person_img', $baseline_person_img);

        View::share('main_loans_inform_msg', $main_loans_inform_msg);

        View::share('share_title', $share_title);

        View::share('socials', $socials);

        View::share('other_suggestions_open_close_global_open_text', $other_suggestions_open_close_global_open_text);

        View::share('other_suggestions_open_close_global_close_text', $other_suggestions_open_close_global_close_text);

        View::share('socials', $socials);
    }

    /**
     * Show the application home dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $belongings = Belonging::whereIn('extra', [2, 3])->with('productsByBelongingInfo')->get();

        $getCompareInfo = $this->getCompareInfoGlobal();

        $belongings_all = Belonging::where('id', '>', 0)->with('productsByBelongingInfo')->get();

        return view('home', ["belongings" => $belongings, "belongings_all" => $belongings_all, "getCompareInfo" => $getCompareInfo]);
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

        $getCompareInfo = $this->getCompareInfoGlobal();

        $belongings_all = Belonging::where('id', '>', 0)->with('productsByBelongingInfo')->get();

        return view('loans', ["belongings" => $belongings, "belongings_all" => $belongings_all, "getCompareInfo" => $getCompareInfo]);
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

        $repayment_loan_interval_types = RepaymentLoanIntervalType::all()->sortBy("extra");

        $repayment_percent_interval_types = RepaymentPercentIntervalType::all()->sortBy("extra");

        $car_cost_max_query = DB::table('car_loans')->select(DB::raw('max(loan_amount_to + prepayment_to) as cost'))->first();

        $car_cost_min_query = DB::table('car_loans')->select(DB::raw('min(loan_amount_from + prepayment_from) as cost'))->first();

        $car_cost_min = $car_cost_min_query->cost;

        $car_cost_max = $car_cost_max_query->cost;

        $loan_term_from_periodicity_type = $request->input('loan_term_from_periodicity_type');

        $productPercentageTypesArr = ["1" => "+", "2" => "-", "3" => "±"];

        $time_type = $request->time_type;

        $car_cost = $request->car_cost;

        $prepayment = $request->prepayment;

        $loan_term = floatval($request->loan_term);

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
            $time_type = 1;

            $loan_term_search_in_days = $loan_term;
        } else if ($time_type == 2) {

            $loan_term_search_in_days = $loan_term * 30;
        } else if ($time_type == 3) {

            $loan_term_search_in_days = $loan_term * 365;
        }


        if ($car_cost > 0) {
            $prepayment_percent = 100 * $prepayment_final / $car_cost;
        } else {
            $prepayment_percent = null;
        }

        if (count($request->all()) > 0) {
            $validator = Validator::make($request->all(), [
                'car_cost' => 'required|numeric|min:1',

                'loan_term' => 'required|numeric',

                'prepayment' => 'nullable|numeric',
            ]);

            $errors = $validator->errors();

            if ($errors->count() > 0) {

                $products = NULL;

                $productsWithVariationsGroupByCompany = NULL;

                $productsWithVariations = NULL;

                $request_results_count = 0;

                $car_types = NULL;

                $percentage_types = NULL;

                $providing_types = NULL;

                $security_types = NULL;

                $repayment_types = NULL;

                $privileged_term_statuses = null;

                $special_project_statuses = null;
            } else {
                $products = CarLoan::with('companyInfo')
                    ->with('securityTypes')->with('otherPayments')->withCount('variations')
                    ->where('status', 2)
                    ->has('variations', '>', 0);

                if (!is_null($loan_term_search_in_days)) {
                    $products->where(function ($query) use ($loan_term_search_in_days) {
                        $query->where('loan_term_from_in_days', '<=', (float)$loan_term_search_in_days);

                        $query->where('loan_term_to_in_days', '>=', (float)$loan_term_search_in_days);
                    });
                }

                if (!is_null($loan_term_search_in_days)) {
                    $products->where(function ($query) use ($loan_term_search_in_days) {
                        $query->where('loan_term_from_in_days', '<=', (float)$loan_term_search_in_days);

                        $query->where('loan_term_to_in_days', '>=', (float)$loan_term_search_in_days);
                    });
                }

                if (!is_null($loan_amount)) {
                    $products->where(function ($query) use ($loan_amount) {
                        $query->where('loan_amount_from', '<=', (float)$loan_amount);
                    });

                    $products = $products->where(function ($query) use ($loan_amount) {
                        $query->where('loan_amount_to', '>=', (float)$loan_amount)
                            ->orWhere(function ($query) use ($loan_amount) {
                                $query->where('loan_amount_to', '=', 0);
                            });
                    });
                }

                if (!is_null($prepayment_percent)) {
                    $products->where(function ($query) use ($prepayment_percent) {
                        $query->where('prepayment_from', '<=', (float)$prepayment_percent);
                    });

                    $products = $products->where(function ($query) use ($prepayment_percent) {
                        $query->where('prepayment_to', '>=', (float)$prepayment_percent)
                            ->orWhere(function ($query) use ($prepayment_percent) {
                                $query->where('prepayment_to', '=', 0);
                            });
                    });
                }

                $products = $products->get();

                $productsWithVariations = [];

                $productsWithVariationsGroupByCompany = [];

                foreach ($products as $key => $product) {

                    $privileged_term_err = 0;

                    $calculable_err = $this->checkProductParamsCalculableErr($belonging_id, $product, $loan_amount, $car_cost);

                    if ($product->privileged_term == 1) {

                        if ($product->privileged_term_loan_time_type == 1) {

                            $privileged_term_search_in_days = $product->privileged_term_loan;
                        } else if ($product->privileged_term_loan_time_type == 2) {

                            $privileged_term_search_in_days = $product->privileged_term_loan * 30;
                        } else if ($product->privileged_term_loan_time_type == 3) {

                            $privileged_term_search_in_days = $product->privileged_term_loan * 365;
                        }
                        if ($privileged_term_search_in_days >= $loan_term_search_in_days) {
                            $privileged_term_err = 1;
                        }
                    }

                    if ($calculable_err == 1 || $privileged_term_err == 1) {
                        $products->forget($key);
                    }

                    if ($privileged_term_err == 0 && $calculable_err == 0) {

                        $productsWithVariationsCurr = [];

                        $productsWithVariationsCurr["id"] = $product->id;

                        $productsWithVariationsCurr["name"] = $product->name;

                        $productsWithVariationsCurr["company_id"] = $product->company_id;

                        $productsWithVariationsCurr["companyInfo"] = $product->companyInfo;

                        $curr_variations = [];

                        foreach ($product->variations as $product_variation) {

                            $curr_variation = [];

                            $getCalculation = $this->getCalculation($product, $product_variation, $car_cost, $loan_amount, $loan_term, $loan_term_search_in_days, $prepayment_percent, $time_type);//calculate factual_percentage and other

                            if (is_numeric($getCalculation["xirr"])) {

                                $factual_percentage = 100 * $getCalculation["xirr"];

                                $require_payments = $getCalculation["require_payments"];

                                $sum_payments = $getCalculation["sum_payments"];

                                $curr_variation["id"] = $product_variation->id;

                                $curr_variation["product_id"] = $product->id;

                                $curr_variation["providing_type"] = $product_variation->providing_type;

                                $curr_variation["percentage_type"] = $product_variation->percentage_type;

                                $curr_variation["percentage"] = $product_variation->percentage;

                                $curr_variation["repayment_type"] = $product_variation->repayment_type;

                                $curr_variation["repayment_loan_interval_type_id"] = $product_variation->repayment_loan_interval_type_id;

                                $curr_variation["repayment_percent_interval_type_id"] = $product_variation->repayment_percent_interval_type_id;

                                $curr_variation["factual_percentage"] = $factual_percentage;

                                $curr_variation["require_payments"] = round($require_payments);

                                $curr_variation["sum_payments"] = $sum_payments;

                                $curr_variations[$product_variation->id] = $curr_variation;


                                $productsWithVariationsGroupByCompanyCurr = $curr_variation;

                                $productsWithVariationsGroupByCompanyCurr["name"] = $product->name;

                                $productsWithVariationsGroupByCompanyCurr["company_id"] = $product->company_id;

                                $productsWithVariationsGroupByCompanyCurr["companyInfo"] = $product->companyInfo;

                                $productsWithVariationsGroupByCompany[] = $productsWithVariationsGroupByCompanyCurr;
                            }
                        }

                        $curr_variations = $this->arrayMultisort($curr_variations, "factual_percentage");

                        $productsWithVariationsCurr["variations"] = $curr_variations;

                        $min_factual_percentage = min(array_column($curr_variations, 'factual_percentage'));

                        $productsWithVariationsCurr["min_factual_percentage"] = $min_factual_percentage;

                        $productsWithVariations[] = $productsWithVariationsCurr;

                    }
                }

                $productsWithVariationsGroupByCompany = $this->arrayMultisort($productsWithVariationsGroupByCompany, "factual_percentage");

                $productsWithVariationsGroupByCompany = collect($productsWithVariationsGroupByCompany)->groupBy('company_id');

                $productsWithVariationsGroupByCompany = $this->paginateCollection($productsWithVariationsGroupByCompany, $this->per_page, 'page_by_company');


                $productsWithVariations = collect($productsWithVariations)->sortBy('min_factual_percentage');

                $productsWithVariations = $this->paginateCollection($productsWithVariations, $this->per_page);

                $request_results_count = $products->sum('variations_count');

                $productsFiltersSingleCounts = $this->compareProductsGetSomeFilters($belonging_id, $products);

                $car_types = $productsFiltersSingleCounts["car_types"];

                $percentage_types = $productsFiltersSingleCounts["percentage_types"];

                $providing_types = $productsFiltersSingleCounts["providing_types"];

                $security_types = $productsFiltersSingleCounts["security_types"];

                $repayment_types = $productsFiltersSingleCounts["repayment_types"];

                $privileged_term_statuses = $productsFiltersSingleCounts["privileged_term_statuses"];

                $special_project_statuses = $productsFiltersSingleCounts["special_project_statuses"];
            }
        } else {
            $validator = Validator::make($request->all(), []);

            $products = NULL;

            $productsWithVariationsGroupByCompany = NULL;

            $productsWithVariations = NULL;

            $request_results_count = 0;

            $car_types = NULL;

            $percentage_types = NULL;

            $providing_types = NULL;

            $security_types = NULL;

            $repayment_types = NULL;

            $privileged_term_statuses = null;

            $special_project_statuses = null;
        }

        $errors = $validator->errors();

        $previousUrl = $this->loansPreviousUrl($request);

        $getCompareInfo = $this->removeIfDiffConfidentionCompareInfo($belonging_id, ["term" => $loan_term_search_in_days, "loan_amount" => $loan_amount, "prepayment" => $prepayment_final, "cost" => $car_cost]);

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

                "productPercentageTypesArr" => $productPercentageTypesArr,

                "loan_amount" => $loan_amount,

                "loan_term_from_periodicity_type" => $loan_term_from_periodicity_type,

                "time_type" => $time_type,

                "car_cost" => $car_cost,

                "prepayment" => $prepayment,

                "prepayment_final" => $prepayment_final,

                "loan_term" => $loan_term,

                "loan_term_search_in_days" => $loan_term_search_in_days,

                "products" => $products,

                "productsWithVariations" => $productsWithVariations,

                "productsWithVariationsGroupByCompany" => $productsWithVariationsGroupByCompany,

                "errors" => $errors,

                "request_results_count" => $request_results_count,

                "previousUrl" => $previousUrl,

                "car_types" => $car_types,

                "percentage_types" => $percentage_types,

                "providing_types" => $providing_types,

                "security_types" => $security_types,

                "repayment_types" => $repayment_types,

                "special_project_statuses" => $special_project_statuses,

                "privileged_term_statuses" => $privileged_term_statuses,


                "getCompareInfo" => $getCompareInfo,
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
            $time_type = 1;

            $loan_term_search_in_days = $loan_term;

        } else if ($time_type == 2) {
            $loan_term_search_in_days = $loan_term * 30;

        } else if ($time_type == 3) {
            $loan_term_search_in_days = $loan_term * 365;
        }

        if ($cost > 0) {
            $prepayment_percent = 100 * $prepayment_final / $cost;
        } else {
            $prepayment_percent = null;
        }

        if (count($request->all()) > 0) {
            $validator = Validator::make($request->all(), [
                'cost' => 'required|numeric|min:1',

                'loan_term' => 'required|numeric',

                'prepayment' => 'nullable|numeric',
            ]);

            $errors = $validator->errors();

            if ($errors->count() > 0) {

                $products = NULL;

                $productsWithVariationsGroupByCompany = NULL;

                $productsWithVariations = NULL;

                $request_results_count = 0;

                $creditPurposeTypes = NULL;

                $percentage_types = NULL;

                $providing_types = NULL;

                $security_types = NULL;

                $repayment_types = NULL;

                $privileged_term_statuses = null;

                $special_project_statuses = null;
            } else {
                $products = CreditLoan::with('companyInfo')
                    ->with('securityTypes')->with('otherPayments')->withCount('variations')
                    ->where('status', 2)
                    ->has('variations', '>', 0);

                if (!is_null($loan_term_search_in_days)) {
                    $products->where(function ($query) use ($loan_term_search_in_days) {
                        $query->where('loan_term_from_in_days', '<=', (float)$loan_term_search_in_days);

                        $query->where('loan_term_to_in_days', '>=', (float)$loan_term_search_in_days);
                    });
                }

                if (!is_null($loan_amount)) {
                    $products->where(function ($query) use ($loan_amount) {
                        $query->where('loan_amount_from', '<=', (float)$loan_amount);
                    });

                    $products = $products->where(function ($query) use ($loan_amount) {
                        $query->where('loan_amount_to', '>=', (float)$loan_amount)
                            ->orWhere(function ($query) use ($loan_amount) {
                                $query->where('loan_amount_to', '=', 0);
                            });
                    });
                }

                if (!is_null($prepayment_percent)) {
                    $products->where(function ($query) use ($prepayment_percent) {
                        $query->where('prepayment_from', '<=', (float)$prepayment_percent);
                    });

                    $products = $products->where(function ($query) use ($prepayment_percent) {
                        $query->where('prepayment_to', '>=', (float)$prepayment_percent)
                            ->orWhere(function ($query) use ($prepayment_percent) {
                                $query->where('prepayment_to', '=', 0);
                            });
                    });
                }

                $products = $products->get();

                $productsWithVariations = [];

                $productsWithVariationsGroupByCompany = [];

                foreach ($products as $key => $product) {

                    $privileged_term_err = 0;

                    $calculable_err = $this->checkProductParamsCalculableErr($belonging_id, $product, $loan_amount, $cost);

                    if ($product->privileged_term == 1) {

                        if ($product->privileged_term_loan_time_type == 1) {

                            $privileged_term_search_in_days = $product->privileged_term_loan;
                        } else if ($product->privileged_term_loan_time_type == 2) {

                            $privileged_term_search_in_days = $product->privileged_term_loan * 30;
                        } else if ($product->privileged_term_loan_time_type == 3) {

                            $privileged_term_search_in_days = $product->privileged_term_loan * 365;
                        }
                        if ($privileged_term_search_in_days >= $loan_term_search_in_days) {
                            $privileged_term_err = 1;
                        }
                    }

                    if ($calculable_err == 1 || $privileged_term_err == 1) {
                        $products->forget($key);
                    }

                    if ($privileged_term_err == 0 && $calculable_err == 0) {

                        $productsWithVariationsCurr = [];

                        $productsWithVariationsCurr["id"] = $product->id;

                        $productsWithVariationsCurr["name"] = $product->name;

                        $productsWithVariationsCurr["company_id"] = $product->company_id;

                        $productsWithVariationsCurr["companyInfo"] = $product->companyInfo;

                        $curr_variations = [];

                        foreach ($product->variations as $product_variation) {

                            $curr_variation = [];

                            $getCalculation = $this->getCalculation($product, $product_variation, $cost, $loan_amount, $loan_term, $loan_term_search_in_days, $prepayment_percent, $time_type);//calculate factual_percentage and other

                            if (is_numeric($getCalculation["xirr"])) {

                                $factual_percentage = 100 * $getCalculation["xirr"];

                                $require_payments = $getCalculation["require_payments"];

                                $sum_payments = $getCalculation["sum_payments"];

                                $curr_variation["id"] = $product_variation->id;

                                $curr_variation["product_id"] = $product->id;

                                $curr_variation["providing_type"] = $product_variation->providing_type;

                                $curr_variation["percentage_type"] = $product_variation->percentage_type;

                                $curr_variation["percentage"] = $product_variation->percentage;

                                $curr_variation["repayment_type"] = $product_variation->repayment_type;

                                $curr_variation["repayment_loan_interval_type_id"] = $product_variation->repayment_loan_interval_type_id;

                                $curr_variation["repayment_percent_interval_type_id"] = $product_variation->repayment_percent_interval_type_id;

                                $curr_variation["factual_percentage"] = $factual_percentage;

                                $curr_variation["require_payments"] = number_format(round($require_payments), 0, ",", " ");

                                $curr_variation["sum_payments"] = number_format(round($sum_payments), 0, ",", " ");

                                $curr_variations[$product_variation->id] = $curr_variation;


                                $productsWithVariationsGroupByCompanyCurr = $curr_variation;

                                $productsWithVariationsGroupByCompanyCurr["name"] = $product->name;

                                $productsWithVariationsGroupByCompanyCurr["company_id"] = $product->company_id;

                                $productsWithVariationsGroupByCompanyCurr["companyInfo"] = $product->companyInfo;

                                $productsWithVariationsGroupByCompany[] = $productsWithVariationsGroupByCompanyCurr;
                            }
                        }

                        $curr_variations = $this->arrayMultisort($curr_variations, "factual_percentage");

                        $productsWithVariationsCurr["variations"] = $curr_variations;

                        $min_factual_percentage = min(array_column($curr_variations, 'factual_percentage'));

                        $productsWithVariationsCurr["min_factual_percentage"] = $min_factual_percentage;

                        $productsWithVariations[] = $productsWithVariationsCurr;

                    }
                }

                $productsWithVariationsGroupByCompany = $this->arrayMultisort($productsWithVariationsGroupByCompany, "factual_percentage");

                $productsWithVariationsGroupByCompany = collect($productsWithVariationsGroupByCompany)->groupBy('company_id');

                $productsWithVariationsGroupByCompany = $this->paginateCollection($productsWithVariationsGroupByCompany, $this->per_page, 'page_by_company');


                $productsWithVariations = collect($productsWithVariations)->sortBy('min_factual_percentage');

                $productsWithVariations = $this->paginateCollection($productsWithVariations, $this->per_page);

                $request_results_count = $products->sum('variations_count');

                $productsFiltersSingleCounts = $this->compareProductsGetSomeFilters($belonging_id, $products);

                $percentage_types = $productsFiltersSingleCounts["percentage_types"];

                $providing_types = $productsFiltersSingleCounts["providing_types"];

                $security_types = $productsFiltersSingleCounts["security_types"];

                $repayment_types = $productsFiltersSingleCounts["repayment_types"];

                $privileged_term_statuses = $productsFiltersSingleCounts["privileged_term_statuses"];

                $special_project_statuses = $productsFiltersSingleCounts["special_project_statuses"];

                $creditPurposeTypes = $productsFiltersSingleCounts["creditPurposeTypes"];
            }
        } else {
            $validator = Validator::make($request->all(), []);

            $products = NULL;

            $productsWithVariationsGroupByCompany = NULL;

            $productsWithVariations = NULL;

            $request_results_count = 0;

            $creditPurposeTypes = NULL;

            $percentage_types = NULL;

            $providing_types = NULL;

            $security_types = NULL;

            $repayment_types = NULL;

            $privileged_term_statuses = null;

            $special_project_statuses = null;
        }

        $errors = $validator->errors();

        $previousUrl = $this->loansPreviousUrl($request);

        $getCompareInfo = $this->removeIfDiffConfidentionCompareInfo($belonging_id, ["term" => $loan_term_search_in_days, "loan_amount" => $loan_amount, "prepayment" => $prepayment_final, "cost" => $cost]);

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

                "loan_term_search_in_days" => $loan_term_search_in_days,

                "cost" => $cost,

                "prepayment" => $prepayment,

                "prepayment_final" => $prepayment_final,

                "loan_amount" => $loan_amount,

                "errors" => $errors,

                "products" => $products,

                "productsWithVariations" => $productsWithVariations,

                "productsWithVariationsGroupByCompany" => $productsWithVariationsGroupByCompany,

                "request_results_count" => $request_results_count,

                "previousUrl" => $previousUrl,

                "creditPurposeTypes" => $creditPurposeTypes,

                "percentage_types" => $percentage_types,

                "providing_types" => $providing_types,

                "security_types" => $security_types,

                "repayment_types" => $repayment_types,

                "special_project_statuses" => $special_project_statuses,

                "privileged_term_statuses" => $privileged_term_statuses,


                "getCompareInfo" => $getCompareInfo,
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

        $loan_amount_min = GoldLoan::min('loan_amount_from');

        $loan_amount_max = GoldLoan::max('loan_amount_to');

        $productPercentageTypesArr = ["1" => "+", "2" => "-", "3" => "±"];

        if ($time_type == 1 || $time_type == "" || is_null($time_type)) {
            $time_type = 1;

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

                $productsWithVariations = NULL;

                $productsWithVariationsGroupByCompany = NULL;

                $request_results_count = 0;

                $percentage_types = NULL;

                $providing_types = NULL;

                $repayment_types = NULL;

                $gold_assay_types = NULL;

                $privileged_term_statuses = null;

                $special_project_statuses = null;
            } else {
                if ($gold_pledge_type == 3) {
                    $gold_pledge_types_arr = $gold_pledge_types->pluck('id')->toArray();
                } else {
                    $gold_pledge_types_arr = array($gold_pledge_type);
                }

                $products = GoldLoan::with('companyInfo')
//                    ->whereIn('status', [1, 2, 5])
                    ->where('status', 2)
//                    ->has('variations', '>', 0);
                    ->withCount('variations');
//                    ->with('goldPledgeTypeInfo')
//                    ->with('otherPayments');

                if (!is_null($loan_amount)) {
                    $products->where(function ($query) use ($loan_amount) {
                        $query->where('loan_amount_from', '<=', (float)$loan_amount);
                    });

                    $products = $products->where(function ($query) use ($loan_amount) {
                        $query->where('loan_amount_to', '>=', (float)$loan_amount)
                            ->orWhere(function ($query) use ($loan_amount) {
                                $query->where('loan_amount_to', '=', 0);
                            });
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

                $productsWithVariations = [];

                $productsWithVariationsGroupByCompany = [];

                foreach ($products as $key => $product) {

                    $privileged_term_err = 0;

//                    $calculable_err = $this->checkProductParamsCalculableErr($belonging_id, $product, $loan_amount, $loan_amount);

                    if ($product->privileged_term == 1) {

                        if ($product->privileged_term_loan_time_type == 1) {

                            $privileged_term_search_in_days = $product->privileged_term_loan;
                        } else if ($product->privileged_term_loan_time_type == 2) {

                            $privileged_term_search_in_days = $product->privileged_term_loan * 30;
                        } else if ($product->privileged_term_loan_time_type == 3) {

                            $privileged_term_search_in_days = $product->privileged_term_loan * 365;
                        }
                        if ($privileged_term_search_in_days >= $loan_term_search_in_days) {
                            $privileged_term_err = 1;
                        }
                    }

                    if ($privileged_term_err == 1) {

                        $products->forget($key);
                    }

                    if ($privileged_term_err == 0) {

                        $productsWithVariationsCurr = [];

                        $productsWithVariationsCurr["id"] = $product->id;

                        $productsWithVariationsCurr["name"] = $product->name;

                        $productsWithVariationsCurr["company_id"] = $product->company_id;

                        $productsWithVariationsCurr["companyInfo"] = $product->companyInfo;

                        $curr_variations = [];

                        if (intval($product->loan_pledge_ratio) == 0) {
                            $cost = 0;
                        } else {
                            $cost = 100 * $loan_amount / $product->loan_pledge_ratio;
                        }

                        foreach ($product->variations as $product_variation) {

                            $curr_variation = [];

                            $getCalculation = $this->getCalculation($product, $product_variation, $cost, $loan_amount, $loan_term, $loan_term_search_in_days, 0, $time_type);//calculate factual_percentage and other

                            if (is_numeric($getCalculation["xirr"])) {
                                $factual_percentage = 100 * $getCalculation["xirr"];

                                $require_payments = $getCalculation["require_payments"];

                                $sum_payments = $getCalculation["sum_payments"];

                                $curr_variation["id"] = $product_variation->id;

                                $curr_variation["product_id"] = $product->id;

                                $curr_variation["providing_type"] = $product_variation->providing_type;

                                $curr_variation["percentage_type"] = $product_variation->percentage_type;

                                $curr_variation["percentage"] = $product_variation->percentage;

                                $curr_variation["repayment_type"] = $product_variation->repayment_type;

                                $curr_variation["repayment_loan_interval_type_id"] = $product_variation->repayment_loan_interval_type_id;

                                $curr_variation["repayment_percent_interval_type_id"] = $product_variation->repayment_percent_interval_type_id;

                                $curr_variation["factual_percentage"] = $factual_percentage;

                                $curr_variation["require_payments"] = round($require_payments);

                                $curr_variation["sum_payments"] = number_format(round($sum_payments), 0, ",", " ");

                                $curr_variations[$product_variation->id] = $curr_variation;


                                $productsWithVariationsGroupByCompanyCurr = $curr_variation;

                                $productsWithVariationsGroupByCompanyCurr["name"] = $product->name;

                                $productsWithVariationsGroupByCompanyCurr["company_id"] = $product->company_id;

                                $productsWithVariationsGroupByCompanyCurr["companyInfo"] = $product->companyInfo;

                                $productsWithVariationsGroupByCompany[] = $productsWithVariationsGroupByCompanyCurr;
                            }
                        }

                        $curr_variations = $this->arrayMultisort($curr_variations, "factual_percentage");

                        $productsWithVariationsCurr["variations"] = $curr_variations;

                        if (is_array(array_column($curr_variations, 'factual_percentage')) && count(array_column($curr_variations, 'factual_percentage')) > 0) {
                            $min_factual_percentage = min(array_column($curr_variations, 'factual_percentage'));

                            $productsWithVariationsCurr["min_factual_percentage"] = $min_factual_percentage;

                            $productsWithVariations[] = $productsWithVariationsCurr;
                        }
                    }
                }

                $productsWithVariationsGroupByCompany = $this->arrayMultisort($productsWithVariationsGroupByCompany, "factual_percentage");

                $productsWithVariationsGroupByCompany = collect($productsWithVariationsGroupByCompany)->groupBy('company_id');

                $productsWithVariationsGroupByCompany = $this->paginateCollection($productsWithVariationsGroupByCompany, $this->per_page, 'page_by_company');


                $productsWithVariations = collect($productsWithVariations)->sortBy('min_factual_percentage');

                $productsWithVariations = $this->paginateCollection($productsWithVariations, $this->per_page);

                $request_results_count = $products->sum('variations_count');

                $productsFiltersSingleCounts = $this->compareProductsGetSomeFilters($belonging_id, $products);

//                $productsGroupByCompanyIds = array_unique($products->pluck('company_id')->toArray());

                $gold_assay_types = $productsFiltersSingleCounts["gold_assay_types"];

                $percentage_types = $productsFiltersSingleCounts["percentage_types"];

                $providing_types = $productsFiltersSingleCounts["providing_types"];

                $repayment_types = $productsFiltersSingleCounts["repayment_types"];

                $privileged_term_statuses = $productsFiltersSingleCounts["privileged_term_statuses"];

                $special_project_statuses = $productsFiltersSingleCounts["special_project_statuses"];
            }
        } else {
            $validator = Validator::make($request->all(), []);

            $products = NULL;

//            $productsGroupByCompany = NULL;

            $productsWithVariations = NULL;

            $productsWithVariationsGroupByCompany = NULL;

            $request_results_count = 0;

            $percentage_types = NULL;

            $providing_types = NULL;

            $repayment_types = NULL;

            $gold_assay_types = NULL;

            $privileged_term_statuses = null;

            $special_project_statuses = null;
        }

        $errors = $validator->errors();

        $previousUrl = $this->loansPreviousUrl($request);

        $getCompareInfo = $this->removeIfDiffConfidentionCompareInfo($belonging_id, ["term" => $loan_term_search_in_days, "loan_amount" => $loan_amount, "gold_pledge_type" => $gold_pledge_type]);

//        $getCompareInfo = $this->getCompareInfo();

        $checked_variations = $getCompareInfo[$belonging_id]["checked_variations"];

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

                "loan_term_search_in_days" => $loan_term_search_in_days,

                "gold_pledge_type" => $gold_pledge_type,

                "loan_amount" => $loan_amount,

                "loan_amount_min" => $loan_amount_min,

                "loan_amount_max" => $loan_amount_max,

                "errors" => $errors,

                "products" => $products,

                "productsWithVariationsGroupByCompany" => $productsWithVariationsGroupByCompany,

//                "productsGroupByCompany" => $productsGroupByCompany,

                "productsWithVariations" => $productsWithVariations,

                "request_results_count" => $request_results_count,

                "previousUrl" => $previousUrl,

                "percentage_types" => $percentage_types,

                "providing_types" => $providing_types,

                "repayment_types" => $repayment_types,

                "gold_assay_types" => $gold_assay_types,

                "special_project_statuses" => $special_project_statuses,

                "privileged_term_statuses" => $privileged_term_statuses,

                "getCompareInfo" => $getCompareInfo,
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

        $loan_amount_min = StudentLoan::min('loan_amount_from');

        $loan_amount_max = StudentLoan::max('loan_amount_to');

        if ($time_type == 1 || $time_type == "" || is_null($time_type)) {
            $time_type = 1;

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

                $productsWithVariations = NULL;

                $productsWithVariationsGroupByCompany = NULL;

                $request_results_count = 0;

                $percentage_types = NULL;

                $providing_types = NULL;

                $security_types = NULL;

                $repayment_types = NULL;

                $privileged_term_statuses = null;

                $special_project_statuses = null;
            } else {
                $products = StudentLoan::with('companyInfo')->whereIn('status', [1, 2, 5])
                    ->has('variations', '>', 0)
                    ->withCount('variations');

                if (!is_null($loan_amount)) {
                    $products->where(function ($query) use ($loan_amount) {
                        $query->where('loan_amount_from', '<=', (float)$loan_amount);
                    });
                    $products->where(function ($query) use ($loan_amount) {
                        $query->where('loan_amount_to', '>=', (float)$loan_amount)
                            ->orWhere(function ($query) use ($loan_amount) {
                                $query->where('loan_amount_to', '=', 0);
                            });
                    });
                }

                if (!is_null($loan_term_search_in_days)) {
                    $products->where(function ($query) use ($loan_term_search_in_days) {
                        $query->where('loan_term_from_in_days', '<=', (float)$loan_term_search_in_days);

                        $query->where('loan_term_to_in_days', '>=', (float)$loan_term_search_in_days);
                    });
                }

                $products = $products->get();

                $productsWithVariations = [];

                $productsWithVariationsGroupByCompany = [];

                foreach ($products as $key => $product) {

                    $privileged_term_err = 0;

                    $calculable_err = $this->checkProductParamsCalculableErr($belonging_id, $product, $loan_amount, $loan_amount);

                    if ($product->privileged_term == 1) {

                        if ($product->privileged_term_loan_time_type == 1) {

                            $privileged_term_search_in_days = $product->privileged_term_loan;
                        } else if ($product->privileged_term_loan_time_type == 2) {

                            $privileged_term_search_in_days = $product->privileged_term_loan * 30;
                        } else if ($product->privileged_term_loan_time_type == 3) {

                            $privileged_term_search_in_days = $product->privileged_term_loan * 365;
                        }
                        if ($privileged_term_search_in_days >= $loan_term_search_in_days) {
                            $privileged_term_err = 1;
                        }
                    }

                    if ($calculable_err == 1 || $privileged_term_err == 1) {

                        $products->forget($key);
                    }

                    if ($privileged_term_err == 0 && $calculable_err == 0) {

                        $productsWithVariationsCurr = [];

                        $productsWithVariationsCurr["id"] = $product->id;

                        $productsWithVariationsCurr["name"] = $product->name;

                        $productsWithVariationsCurr["company_id"] = $product->company_id;

                        $productsWithVariationsCurr["companyInfo"] = $product->companyInfo;

                        $curr_variations = [];

                        if (intval($product->loan_pledge_ratio) == 0) {
                            $cost = 0;
                        } else {
                            $cost = 100 * $loan_amount / $product->loan_pledge_ratio;
                        }

                        foreach ($product->variations as $product_variation) {

                            $curr_variation = [];

                            $getCalculation = $this->getCalculation($product, $product_variation, $cost, $loan_amount, $loan_term, $loan_term_search_in_days, 0, $time_type);//calculate factual_percentage and other

                            if (is_numeric($getCalculation["xirr"])) {
                                $factual_percentage = 100 * $getCalculation["xirr"];

                                $require_payments = $getCalculation["require_payments"];

                                $sum_payments = $getCalculation["sum_payments"];

                                $curr_variation["id"] = $product_variation->id;

                                $curr_variation["product_id"] = $product->id;

                                $curr_variation["providing_type"] = $product_variation->providing_type;

                                $curr_variation["percentage_type"] = $product_variation->percentage_type;

                                $curr_variation["percentage"] = $product_variation->percentage;

                                $curr_variation["repayment_type"] = $product_variation->repayment_type;

                                $curr_variation["repayment_loan_interval_type_id"] = $product_variation->repayment_loan_interval_type_id;

                                $curr_variation["repayment_percent_interval_type_id"] = $product_variation->repayment_percent_interval_type_id;

                                $curr_variation["factual_percentage"] = $factual_percentage;

                                $curr_variation["require_payments"] = round($require_payments);

                                $curr_variation["sum_payments"] = number_format(round($sum_payments), 0, ",", " ");

                                $curr_variations[$product_variation->id] = $curr_variation;


                                $productsWithVariationsGroupByCompanyCurr = $curr_variation;

                                $productsWithVariationsGroupByCompanyCurr["name"] = $product->name;

                                $productsWithVariationsGroupByCompanyCurr["company_id"] = $product->company_id;

                                $productsWithVariationsGroupByCompanyCurr["companyInfo"] = $product->companyInfo;

                                $productsWithVariationsGroupByCompany[] = $productsWithVariationsGroupByCompanyCurr;
                            }
                        }

                        $curr_variations = $this->arrayMultisort($curr_variations, "factual_percentage");

                        $productsWithVariationsCurr["variations"] = $curr_variations;

                        $min_factual_percentage = min(array_column($curr_variations, 'factual_percentage'));

                        $productsWithVariationsCurr["min_factual_percentage"] = $min_factual_percentage;

                        $productsWithVariations[] = $productsWithVariationsCurr;

                    }
                }

                $productsWithVariationsGroupByCompany = $this->arrayMultisort($productsWithVariationsGroupByCompany, "factual_percentage");

                $productsWithVariationsGroupByCompany = collect($productsWithVariationsGroupByCompany)->groupBy('company_id');

                $productsWithVariationsGroupByCompany = $this->paginateCollection($productsWithVariationsGroupByCompany, $this->per_page, 'page_by_company');


                $productsWithVariations = collect($productsWithVariations)->sortBy('min_factual_percentage');

                $productsWithVariations = $this->paginateCollection($productsWithVariations, $this->per_page);

                $productsGroupByCompanyIds = array_unique($products->pluck('company_id')->toArray());

                $productsGroupByCompany = [];

                foreach ($productsGroupByCompanyIds as $productCompanyId) {
                    $productsGroupByCompany[] = $products->where('company_id', $productCompanyId);
                }

                $request_results_count = $products->sum('variations_count');//->count();

                $productsFiltersSingleCounts = $this->compareProductsGetSomeFilters($belonging_id, $products);

                $percentage_types = $productsFiltersSingleCounts["percentage_types"];

                $security_types = $productsFiltersSingleCounts["security_types"];

                $providing_types = $productsFiltersSingleCounts["providing_types"];

                $repayment_types = $productsFiltersSingleCounts["repayment_types"];

                $privileged_term_statuses = $productsFiltersSingleCounts["privileged_term_statuses"];

                $special_project_statuses = $productsFiltersSingleCounts["special_project_statuses"];
            }
        } else {
            $validator = Validator::make($request->all(), []);

            $products = NULL;

            $productsWithVariations = NULL;

            $productsWithVariationsGroupByCompany = NULL;

            $request_results_count = 0;

            $percentage_types = NULL;

            $providing_types = NULL;

            $security_types = NULL;

            $repayment_types = NULL;

            $privileged_term_statuses = null;

            $special_project_statuses = null;
        }

        $getCompareInfo = $this->removeIfDiffConfidentionCompareInfo($belonging_id, ["term" => $loan_term_search_in_days, "loan_amount" => $loan_amount]);

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

                "loan_term_search_in_days" => $loan_term_search_in_days,

                "loan_amount_min" => $loan_amount_min,

                "loan_amount_max" => $loan_amount_max,

                "errors" => $errors,

                "products" => $products,

                "productsWithVariationsGroupByCompany" => $productsWithVariationsGroupByCompany,

                "productsWithVariations" => $productsWithVariations,

                "request_results_count" => $request_results_count,

                "previousUrl" => $previousUrl,

                "percentage_types" => $percentage_types,

                "providing_types" => $providing_types,

                "security_types" => $security_types,

                "repayment_types" => $repayment_types,

                "special_project_statuses" => $special_project_statuses,

                "privileged_term_statuses" => $privileged_term_statuses,

                "getCompareInfo" => $getCompareInfo,
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

        $loan_amount_min = AgricLoan::min('loan_amount_from');

        $loan_amount_max = AgricLoan::max('loan_amount_to');

        $currency = $request->currency;

        if ($time_type == 1 || $time_type == "" || is_null($time_type)) {
            $time_type = 1;

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

                $loan_amount_converted = NULL;

                $productsWithVariations = NULL;

                $productsWithVariationsGroupByCompany = NULL;

                $request_results_count = 0;

                $purposeTypes = NULL;

                $percentage_types = NULL;

                $providing_types = NULL;

                $security_types = NULL;

                $repayment_types = NULL;

                $privileged_term_statuses = null;

                $special_project_statuses = null;
            } else {
                $products = AgricLoan::with('companyInfo')->whereIn('status', [1, 2, 5])
                    ->has('variations', '>', 0)
                    ->withCount('variations');

                if (!is_null($loan_amount)) {
                    $products->where(function ($query) use ($loan_amount) {
                        $query->where('loan_amount_from', '<=', (float)$loan_amount);
                    });
                    $products->where(function ($query) use ($loan_amount) {
                        $query->where('loan_amount_to', '>=', (float)$loan_amount)
                            ->orWhere(function ($query) use ($loan_amount) {
                                $query->where('loan_amount_to', '=', 0);
                            });
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
                        $query->where('currency', (int)$currency);
                    });
                }


                $products = $products->get();

                $productsWithVariations = [];

                $productsWithVariationsGroupByCompany = [];

                $loan_amount_converted = $this->getLoanAmountConverted($currency, $loan_amount);

                foreach ($products as $key => $product) {

                    if (intval($product->loan_pledge_ratio) == 0) {
                        $cost = 0;
                    } else {
                        $cost = 100 * $loan_amount_converted / $product->loan_pledge_ratio;
                    }

                    $privileged_term_err = 0;

                    $calculable_err = $this->checkProductParamsCalculableErr($belonging_id, $product, $loan_amount_converted, $cost);

                    if ($product->privileged_term == 1) {

                        if ($product->privileged_term_loan_time_type == 1) {

                            $privileged_term_search_in_days = $product->privileged_term_loan;
                        } else if ($product->privileged_term_loan_time_type == 2) {

                            $privileged_term_search_in_days = $product->privileged_term_loan * 30;
                        } else if ($product->privileged_term_loan_time_type == 3) {

                            $privileged_term_search_in_days = $product->privileged_term_loan * 365;
                        }
                        if ($privileged_term_search_in_days >= $loan_term_search_in_days) {
                            $privileged_term_err = 1;
                        }
                    }

                    if ($calculable_err == 1 || $privileged_term_err == 1) {

                        $products->forget($key);
                    }

                    if ($privileged_term_err == 0 && $calculable_err == 0) {

                        $productsWithVariationsCurr = [];

                        $productsWithVariationsCurr["id"] = $product->id;

                        $productsWithVariationsCurr["name"] = $product->name;

                        $productsWithVariationsCurr["company_id"] = $product->company_id;

                        $productsWithVariationsCurr["companyInfo"] = $product->companyInfo;

                        $curr_variations = [];

                        foreach ($product->variations as $product_variation) {

                            $curr_variation = [];

                            $getCalculation = $this->getCalculation($product, $product_variation, $cost, $loan_amount_converted, $loan_term, $loan_term_search_in_days, 0, $time_type);//calculate factual_percentage and other

                            if (is_numeric($getCalculation["xirr"])) {
                                $factual_percentage = 100 * $getCalculation["xirr"];

                                $require_payments = $getCalculation["require_payments"];

                                $sum_payments = $getCalculation["sum_payments"];

                                $curr_variation["id"] = $product_variation->id;

                                $curr_variation["product_id"] = $product->id;

                                $curr_variation["providing_type"] = $product_variation->providing_type;

                                $curr_variation["percentage_type"] = $product_variation->percentage_type;

                                $curr_variation["percentage"] = $product_variation->percentage;

                                $curr_variation["repayment_type"] = $product_variation->repayment_type;

                                $curr_variation["repayment_loan_interval_type_id"] = $product_variation->repayment_loan_interval_type_id;

                                $curr_variation["repayment_percent_interval_type_id"] = $product_variation->repayment_percent_interval_type_id;

                                $curr_variation["factual_percentage"] = $factual_percentage;

                                $curr_variation["require_payments"] = round($require_payments);

                                $curr_variation["sum_payments"] = number_format(round($sum_payments), 0, ",", " ");

                                $curr_variations[$product_variation->id] = $curr_variation;


                                $productsWithVariationsGroupByCompanyCurr = $curr_variation;

                                $productsWithVariationsGroupByCompanyCurr["name"] = $product->name;

                                $productsWithVariationsGroupByCompanyCurr["company_id"] = $product->company_id;

                                $productsWithVariationsGroupByCompanyCurr["companyInfo"] = $product->companyInfo;

                                $productsWithVariationsGroupByCompany[] = $productsWithVariationsGroupByCompanyCurr;
                            }
                        }

                        $curr_variations = $this->arrayMultisort($curr_variations, "factual_percentage");

                        $productsWithVariationsCurr["variations"] = $curr_variations;

                        $min_factual_percentage = min(array_column($curr_variations, 'factual_percentage'));

                        $productsWithVariationsCurr["min_factual_percentage"] = $min_factual_percentage;

                        $productsWithVariations[] = $productsWithVariationsCurr;
                    }
                }

                $productsWithVariationsGroupByCompany = $this->arrayMultisort($productsWithVariationsGroupByCompany, "factual_percentage");

                $productsWithVariationsGroupByCompany = collect($productsWithVariationsGroupByCompany)->groupBy('company_id');

                $productsWithVariationsGroupByCompany = $this->paginateCollection($productsWithVariationsGroupByCompany, $this->per_page, 'page_by_company');


                $productsWithVariations = collect($productsWithVariations)->sortBy('min_factual_percentage');

                $productsWithVariations = $this->paginateCollection($productsWithVariations, $this->per_page);

                $productsGroupByCompanyIds = array_unique($products->pluck('company_id')->toArray());

                $productsGroupByCompany = [];

                foreach ($productsGroupByCompanyIds as $productCompanyId) {
                    $productsGroupByCompany[] = $products->where('company_id', $productCompanyId);
                }

                $request_results_count = $products->sum('variations_count');//->count();

                $productsFiltersSingleCounts = $this->compareProductsGetSomeFilters($belonging_id, $products);

                $purposeTypes = $productsFiltersSingleCounts["purposeTypes"];

                $percentage_types = $productsFiltersSingleCounts["percentage_types"];

                $security_types = $productsFiltersSingleCounts["security_types"];

                $providing_types = $productsFiltersSingleCounts["providing_types"];

                $repayment_types = $productsFiltersSingleCounts["repayment_types"];

                $privileged_term_statuses = $productsFiltersSingleCounts["privileged_term_statuses"];

                $special_project_statuses = $productsFiltersSingleCounts["special_project_statuses"];
            }
        } else {
            $validator = Validator::make($request->all(), []);

            $products = NULL;

            $loan_amount_converted = NULL;

            $productsWithVariations = NULL;

            $productsWithVariationsGroupByCompany = NULL;

            $request_results_count = 0;

            $purposeTypes = NULL;

            $percentage_types = NULL;

            $providing_types = NULL;

            $security_types = NULL;

            $repayment_types = NULL;

            $privileged_term_statuses = null;

            $special_project_statuses = null;
        }

        $getCompareInfo = $this->removeIfDiffConfidentionCompareInfo($belonging_id, ["term" => $loan_term_search_in_days, "loan_amount" => $loan_amount, "currency" => $currency]);

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

                "time_types" => $time_types,

                "time_type" => $time_type,

                "loan_term" => $loan_term,

                "loan_amount" => $loan_amount,

                "loan_amount_min" => $loan_amount_min,

                "loan_amount_max" => $loan_amount_max,

                "loan_amount_converted" => $loan_amount_converted,

                "loan_term_search_in_days" => $loan_term_search_in_days,

                "currency" => $currency,

                "errors" => $errors,

                "products" => $products,

                "productsWithVariationsGroupByCompany" => $productsWithVariationsGroupByCompany,

                "productsWithVariations" => $productsWithVariations,

                "request_results_count" => $request_results_count,

                "previousUrl" => $previousUrl,

                "purposeTypes" => $purposeTypes,

                "percentage_types" => $percentage_types,

                "providing_types" => $providing_types,

                "security_types" => $security_types,

                "repayment_types" => $repayment_types,

                "special_project_statuses" => $special_project_statuses,

                "privileged_term_statuses" => $privileged_term_statuses,

                "getCompareInfo" => $getCompareInfo,
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

        $loan_amount_min = ConsumerCredit::min('loan_amount_from');

        $loan_amount_max = ConsumerCredit::max('loan_amount_to');

        if ($time_type == 1 || $time_type == "" || is_null($time_type)) {
            $time_type = 1;

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

                $productsWithVariationsGroupByCompany = NULL;

                $productsWithVariations = NULL;

                $request_results_count = 0;

                $percentage_types = NULL;

                $providing_types = NULL;

                $security_types = NULL;

                $repayment_types = NULL;

                $privileged_term_statuses = null;

                $special_project_statuses = null;
            } else {
                $products = ConsumerCredit::with('companyInfo')
                    ->where('status', 2)
//                    ->has('variations', '>', 0)
                    ->withCount('variations');

                if (!is_null($loan_amount)) {
                    $products->where(function ($query) use ($loan_amount) {
                        $query->where('loan_amount_from', '<=', (float)$loan_amount);
                    });
                    $products->where(function ($query) use ($loan_amount) {
                        $query->where('loan_amount_to', '>=', (float)$loan_amount)
                            ->orWhere(function ($query) use ($loan_amount) {
                                $query->where('loan_amount_to', '=', 0);
                            });
                    });
                }

                if (!is_null($loan_term_search_in_days)) {
                    $products->where(function ($query) use ($loan_term_search_in_days) {
                        $query->where('loan_term_from_in_days', '<=', (float)$loan_term_search_in_days);

                        $query->where('loan_term_to_in_days', '>=', (float)$loan_term_search_in_days);
                    });
                }

                $products = $products->get();

                $productsWithVariations = [];

                $productsWithVariationsGroupByCompany = [];

                foreach ($products as $key => $product) {

                    $privileged_term_err = 0;

//                    $calculable_err = $this->checkProductParamsCalculableErr($belonging_id, $product, $loan_amount, $loan_amount);

                    if ($product->privileged_term == 1) {

                        if ($product->privileged_term_loan_time_type == 1) {

                            $privileged_term_search_in_days = $product->privileged_term_loan;
                        } else if ($product->privileged_term_loan_time_type == 2) {

                            $privileged_term_search_in_days = $product->privileged_term_loan * 30;
                        } else if ($product->privileged_term_loan_time_type == 3) {

                            $privileged_term_search_in_days = $product->privileged_term_loan * 365;
                        }
                        if ($privileged_term_search_in_days >= $loan_term_search_in_days) {
                            $privileged_term_err = 1;
                        }
                    }

                    if ($privileged_term_err == 1) {

                        $products->forget($key);
                    }

                    if ($privileged_term_err == 0) {

                        $productsWithVariationsCurr = [];

                        $productsWithVariationsCurr["id"] = $product->id;

                        $productsWithVariationsCurr["name"] = $product->name;

                        $productsWithVariationsCurr["company_id"] = $product->company_id;

                        $productsWithVariationsCurr["companyInfo"] = $product->companyInfo;

                        $curr_variations = [];

                        if (intval($product->loan_pledge_ratio) == 0) {
                            $cost = 0;
                        } else {
                            $cost = 100 * $loan_amount / $product->loan_pledge_ratio;
                        }

                        foreach ($product->variations as $product_variation) {

                            $curr_variation = [];

                            $getCalculation = $this->getCalculation($product, $product_variation, $cost, $loan_amount, $loan_term, $loan_term_search_in_days, 0, $time_type);//calculate factual_percentage and other


                            if (is_numeric($getCalculation["xirr"])) {
                                $factual_percentage = 100 * $getCalculation["xirr"];

                                $require_payments = $getCalculation["require_payments"];

                                $sum_payments = $getCalculation["sum_payments"];

                                $curr_variation["id"] = $product_variation->id;

                                $curr_variation["product_id"] = $product->id;

                                $curr_variation["providing_type"] = $product_variation->providing_type;

                                $curr_variation["percentage_type"] = $product_variation->percentage_type;

                                $curr_variation["percentage"] = $product_variation->percentage;

                                $curr_variation["repayment_type"] = $product_variation->repayment_type;

                                $curr_variation["repayment_loan_interval_type_id"] = $product_variation->repayment_loan_interval_type_id;

                                $curr_variation["repayment_percent_interval_type_id"] = $product_variation->repayment_percent_interval_type_id;

                                $curr_variation["factual_percentage"] = $factual_percentage;

                                $curr_variation["require_payments"] = round($require_payments);

                                $curr_variation["sum_payments"] = number_format(round($sum_payments), 0, ",", " ");

                                $curr_variations[$product_variation->id] = $curr_variation;


                                $productsWithVariationsGroupByCompanyCurr = $curr_variation;

                                $productsWithVariationsGroupByCompanyCurr["name"] = $product->name;

                                $productsWithVariationsGroupByCompanyCurr["company_id"] = $product->company_id;

                                $productsWithVariationsGroupByCompanyCurr["companyInfo"] = $product->companyInfo;

                                $productsWithVariationsGroupByCompany[] = $productsWithVariationsGroupByCompanyCurr;
                            }
                        }

                        $curr_variations = $this->arrayMultisort($curr_variations, "factual_percentage");

                        $productsWithVariationsCurr["variations"] = $curr_variations;

                        if (is_array(array_column($curr_variations, 'factual_percentage')) && count(array_column($curr_variations, 'factual_percentage')) > 0) {
                            $min_factual_percentage = min(array_column($curr_variations, 'factual_percentage'));

                            $productsWithVariationsCurr["min_factual_percentage"] = $min_factual_percentage;

                            $productsWithVariations[] = $productsWithVariationsCurr;
                        }
                    }
                }

                $productsWithVariationsGroupByCompany = $this->arrayMultisort($productsWithVariationsGroupByCompany, "factual_percentage");

                $productsWithVariationsGroupByCompany = collect($productsWithVariationsGroupByCompany)->groupBy('company_id');

                $productsWithVariationsGroupByCompany = $this->paginateCollection($productsWithVariationsGroupByCompany, $this->per_page, 'page_by_company');


                $productsWithVariations = collect($productsWithVariations)->sortBy('min_factual_percentage');

                $productsWithVariations = $this->paginateCollection($productsWithVariations, $this->per_page);
//
//                $productsGroupByCompanyIds = array_unique($products->pluck('company_id')->toArray());
//
//                $productsGroupByCompany = [];
//
//                foreach ($productsGroupByCompanyIds as $productCompanyId) {
//                    $productsGroupByCompany[] = $products->where('company_id', $productCompanyId);
//                }

                $request_results_count = $products->sum('variations_count');//->count();

                $productsFiltersSingleCounts = $this->compareProductsGetSomeFilters($belonging_id, $products);

                $percentage_types = $productsFiltersSingleCounts["percentage_types"];

                $security_types = $productsFiltersSingleCounts["security_types"];

                $providing_types = $productsFiltersSingleCounts["providing_types"];

                $repayment_types = $productsFiltersSingleCounts["repayment_types"];

                $privileged_term_statuses = $productsFiltersSingleCounts["privileged_term_statuses"];

                $special_project_statuses = $productsFiltersSingleCounts["special_project_statuses"];
            }
        } else {
            $validator = Validator::make($request->all(), []);

            $products = NULL;

            $productsWithVariationsGroupByCompany = NULL;

            $productsWithVariations = NULL;

            $request_results_count = 0;

            $percentage_types = NULL;

            $providing_types = NULL;

            $security_types = NULL;

            $repayment_types = NULL;

            $privileged_term_statuses = null;

            $special_project_statuses = null;
        }

        $errors = $validator->errors();

        $previousUrl = $this->loansPreviousUrl($request);

        $getCompareInfo = $this->removeIfDiffConfidentionCompareInfo($belonging_id, ["term" => $loan_term_search_in_days, "loan_amount" => $loan_amount]);
//dd($_COOKIE);
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

                "loan_term_search_in_days" => $loan_term_search_in_days,

                "loan_amount_min" => $loan_amount_min,

                "loan_amount_max" => $loan_amount_max,

                "errors" => $errors,

                "products" => $products,

                "productsWithVariationsGroupByCompany" => $productsWithVariationsGroupByCompany,

                "productsWithVariations" => $productsWithVariations,

                "request_results_count" => $request_results_count,

                "previousUrl" => $previousUrl,

                "percentage_types" => $percentage_types,

                "providing_types" => $providing_types,

                "security_types" => $security_types,

                "repayment_types" => $repayment_types,

                "special_project_statuses" => $special_project_statuses,

                "privileged_term_statuses" => $privileged_term_statuses,

                "getCompareInfo" => $getCompareInfo,
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

        $loan_amount_min = OnlineLoan::min('loan_amount_from');

        $loan_amount_max = OnlineLoan::max('loan_amount_to');

        if ($time_type == 1 || $time_type == "" || is_null($time_type)) {
            $time_type = 1;

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

                $productsWithVariationsGroupByCompany = NULL;

                $productsWithVariations = NULL;

                $request_results_count = 0;

                $percentage_types = NULL;

                $providing_types = NULL;

                $security_types = NULL;

                $repayment_types = NULL;

                $privileged_term_statuses = null;

                $special_project_statuses = null;
            } else {
                $products = OnlineLoan::with('companyInfo')->whereIn('status', [1, 2, 5])
                    ->has('variations', '>', 0)
                    ->withCount('variations');

                if (!is_null($loan_amount)) {
                    $products->where(function ($query) use ($loan_amount) {
                        $query->where('loan_amount_from', '<=', (float)$loan_amount);
                    });
                    $products->where(function ($query) use ($loan_amount) {
                        $query->where('loan_amount_to', '>=', (float)$loan_amount)
                            ->orWhere(function ($query) use ($loan_amount) {
                                $query->where('loan_amount_to', '=', 0);
                            });
                    });
                }

                if (!is_null($loan_term_search_in_days)) {
                    $products->where(function ($query) use ($loan_term_search_in_days) {
                        $query->where('loan_term_from_in_days', '<=', (float)$loan_term_search_in_days);

                        $query->where('loan_term_to_in_days', '>=', (float)$loan_term_search_in_days);
                    });
                }

                $products = $products->get();

                $productsWithVariations = [];

                $productsWithVariationsGroupByCompany = [];

                foreach ($products as $key => $product) {

                    $privileged_term_err = 0;

                    $calculable_err = $this->checkProductParamsCalculableErr($belonging_id, $product, $loan_amount, $loan_amount);

                    if ($product->privileged_term == 1) {

                        if ($product->privileged_term_loan_time_type == 1) {

                            $privileged_term_search_in_days = $product->privileged_term_loan;
                        } else if ($product->privileged_term_loan_time_type == 2) {

                            $privileged_term_search_in_days = $product->privileged_term_loan * 30;
                        } else if ($product->privileged_term_loan_time_type == 3) {

                            $privileged_term_search_in_days = $product->privileged_term_loan * 365;
                        }
                        if ($privileged_term_search_in_days >= $loan_term_search_in_days) {
                            $privileged_term_err = 1;
                        }
                    }

                    if ($calculable_err == 1 || $privileged_term_err == 1) {

                        $products->forget($key);
                    }

                    if ($privileged_term_err == 0 && $calculable_err == 0) {

                        $productsWithVariationsCurr = [];

                        $productsWithVariationsCurr["id"] = $product->id;

                        $productsWithVariationsCurr["name"] = $product->name;

                        $productsWithVariationsCurr["company_id"] = $product->company_id;

                        $productsWithVariationsCurr["companyInfo"] = $product->companyInfo;

                        $curr_variations = [];

                        if (intval($product->loan_pledge_ratio) == 0) {
                            $cost = 0;
                        } else {
                            $cost = 100 * $loan_amount / $product->loan_pledge_ratio;
                        }

                        foreach ($product->variations as $product_variation) {

                            $curr_variation = [];

                            $getCalculation = $this->getCalculation($product, $product_variation, $cost, $loan_amount, $loan_term, $loan_term_search_in_days, 0, $time_type);//calculate factual_percentage and other

                            if (is_numeric($getCalculation["xirr"])) {

                                $factual_percentage = 100 * $getCalculation["xirr"];

                                $require_payments = $getCalculation["require_payments"];

                                $sum_payments = $getCalculation["sum_payments"];

                                $curr_variation["id"] = $product_variation->id;

                                $curr_variation["product_id"] = $product->id;

                                $curr_variation["providing_type"] = $product_variation->providing_type;

                                $curr_variation["percentage_type"] = $product_variation->percentage_type;

                                $curr_variation["percentage"] = $product_variation->percentage;

                                $curr_variation["repayment_type"] = $product_variation->repayment_type;

                                $curr_variation["repayment_loan_interval_type_id"] = $product_variation->repayment_loan_interval_type_id;

                                $curr_variation["repayment_percent_interval_type_id"] = $product_variation->repayment_percent_interval_type_id;

                                $curr_variation["factual_percentage"] = $factual_percentage;

                                $curr_variation["require_payments"] = round($require_payments);

                                $curr_variation["sum_payments"] = number_format(round($sum_payments), 0, ",", " ");

                                $curr_variations[$product_variation->id] = $curr_variation;


                                $productsWithVariationsGroupByCompanyCurr = $curr_variation;

                                $productsWithVariationsGroupByCompanyCurr["name"] = $product->name;

                                $productsWithVariationsGroupByCompanyCurr["company_id"] = $product->company_id;

                                $productsWithVariationsGroupByCompanyCurr["companyInfo"] = $product->companyInfo;

                                $productsWithVariationsGroupByCompany[] = $productsWithVariationsGroupByCompanyCurr;
                            }
                        }

                        $curr_variations = $this->arrayMultisort($curr_variations, "factual_percentage");

                        $productsWithVariationsCurr["variations"] = $curr_variations;

                        $min_factual_percentage = min(array_column($curr_variations, 'factual_percentage'));

                        $productsWithVariationsCurr["min_factual_percentage"] = $min_factual_percentage;

                        $productsWithVariations[] = $productsWithVariationsCurr;

                    }
                }

                $productsWithVariationsGroupByCompany = $this->arrayMultisort($productsWithVariationsGroupByCompany, "factual_percentage");

                $productsWithVariationsGroupByCompany = collect($productsWithVariationsGroupByCompany)->groupBy('company_id');

                $productsWithVariationsGroupByCompany = $this->paginateCollection($productsWithVariationsGroupByCompany, $this->per_page, 'page_by_company');


                $productsWithVariations = collect($productsWithVariations)->sortBy('min_factual_percentage');

                $productsWithVariations = $this->paginateCollection($productsWithVariations, $this->per_page);

                $productsGroupByCompanyIds = array_unique($products->pluck('company_id')->toArray());

                $productsGroupByCompany = [];

                foreach ($productsGroupByCompanyIds as $productCompanyId) {
                    $productsGroupByCompany[] = $products->where('company_id', $productCompanyId);
                }

                $request_results_count = $products->sum('variations_count');//->count();

                $productsFiltersSingleCounts = $this->compareProductsGetSomeFilters($belonging_id, $products);

                $percentage_types = $productsFiltersSingleCounts["percentage_types"];

                $security_types = $productsFiltersSingleCounts["security_types"];

                $providing_types = $productsFiltersSingleCounts["providing_types"];

                $repayment_types = $productsFiltersSingleCounts["repayment_types"];

                $privileged_term_statuses = $productsFiltersSingleCounts["privileged_term_statuses"];

                $special_project_statuses = $productsFiltersSingleCounts["special_project_statuses"];
            }
        } else {
            $validator = Validator::make($request->all(), []);

            $products = NULL;

            $productsWithVariationsGroupByCompany = NULL;

            $productsWithVariations = NULL;

            $request_results_count = 0;

            $percentage_types = NULL;

            $providing_types = NULL;

            $security_types = NULL;

            $repayment_types = NULL;

            $privileged_term_statuses = null;

            $special_project_statuses = null;
        }

        $errors = $validator->errors();

        $previousUrl = $this->loansPreviousUrl($request);

        $getCompareInfo = $this->removeIfDiffConfidentionCompareInfo($belonging_id, ["term" => $loan_term_search_in_days, "loan_amount" => $loan_amount]);

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

                "time_types" => $time_types,

                "time_type" => $time_type,

                "loan_term" => $loan_term,

                "loan_amount" => $loan_amount,

                "loan_term_search_in_days" => $loan_term_search_in_days,

                "loan_amount_min" => $loan_amount_min,

                "loan_amount_max" => $loan_amount_max,

                "errors" => $errors,

                "products" => $products,

                "productsWithVariationsGroupByCompany" => $productsWithVariationsGroupByCompany,

                "productsWithVariations" => $productsWithVariations,

                "request_results_count" => $request_results_count,

                "previousUrl" => $previousUrl,

                "percentage_types" => $percentage_types,

                "providing_types" => $providing_types,

                "security_types" => $security_types,

                "repayment_types" => $repayment_types,

                "special_project_statuses" => $special_project_statuses,

                "privileged_term_statuses" => $privileged_term_statuses,

                "getCompareInfo" => $getCompareInfo,
            ]);
    }

    /**
     * compare loan Refinancings.
     *
     * @return \Illuminate\Http\Response
     */
    function compareLoanRefinancings(Request $request)
    {
        $belonging_id = 11;

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

        $loan_amount_min = LoanRefinancing::min('loan_amount_from');

        $loan_amount_max = LoanRefinancing::max('loan_amount_to');

        if ($time_type == 1 || $time_type == "" || is_null($time_type)) {
            $time_type = 1;

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

                $productsWithVariationsGroupByCompany = NULL;

                $productsWithVariations = NULL;

                $request_results_count = 0;

                $percentage_types = NULL;

                $providing_types = NULL;

                $security_types = NULL;

                $repayment_types = NULL;

                $privileged_term_statuses = null;

                $special_project_statuses = null;
            } else {
                $products = LoanRefinancing::with('companyInfo')->whereIn('status', [1, 2, 5])
                    ->has('variations', '>', 0)
                    ->withCount('variations');

                if (!is_null($loan_amount)) {
                    $products->where(function ($query) use ($loan_amount) {
                        $query->where('loan_amount_from', '<=', (float)$loan_amount);
                    });
                    $products->where(function ($query) use ($loan_amount) {
                        $query->where('loan_amount_to', '>=', (float)$loan_amount)
                            ->orWhere(function ($query) use ($loan_amount) {
                                $query->where('loan_amount_to', '=', 0);
                            });
                    });
                }

                if (!is_null($loan_term_search_in_days)) {
                    $products->where(function ($query) use ($loan_term_search_in_days) {
                        $query->where('loan_term_from_in_days', '<=', (float)$loan_term_search_in_days);

                        $query->where('loan_term_to_in_days', '>=', (float)$loan_term_search_in_days);
                    });
                }

                $products = $products->get();

                $productsWithVariations = [];

                $productsWithVariationsGroupByCompany = [];

                foreach ($products as $key => $product) {

                    $privileged_term_err = 0;

                    $calculable_err = $this->checkProductParamsCalculableErr($belonging_id, $product, $loan_amount, $loan_amount);

                    if ($product->privileged_term == 1) {

                        if ($product->privileged_term_loan_time_type == 1) {

                            $privileged_term_search_in_days = $product->privileged_term_loan;
                        } else if ($product->privileged_term_loan_time_type == 2) {

                            $privileged_term_search_in_days = $product->privileged_term_loan * 30;
                        } else if ($product->privileged_term_loan_time_type == 3) {

                            $privileged_term_search_in_days = $product->privileged_term_loan * 365;
                        }
                        if ($privileged_term_search_in_days >= $loan_term_search_in_days) {
                            $privileged_term_err = 1;
                        }
                    }

                    if ($calculable_err == 1 || $privileged_term_err == 1) {

                        $products->forget($key);
                    }

                    if ($privileged_term_err == 0 && $calculable_err == 0) {

                        $productsWithVariationsCurr = [];

                        $productsWithVariationsCurr["id"] = $product->id;

                        $productsWithVariationsCurr["name"] = $product->name;

                        $productsWithVariationsCurr["company_id"] = $product->company_id;

                        $productsWithVariationsCurr["companyInfo"] = $product->companyInfo;

                        $curr_variations = [];

                        if (intval($product->loan_pledge_ratio) == 0) {
                            $cost = 0;
                        } else {
                            $cost = 100 * $loan_amount / $product->loan_pledge_ratio;
                        }

                        foreach ($product->variations as $product_variation) {

                            $curr_variation = [];

                            $getCalculation = $this->getCalculation($product, $product_variation, $cost, $loan_amount, $loan_term, $loan_term_search_in_days, 0, $time_type);//calculate factual_percentage and other

                            if (is_numeric($getCalculation["xirr"])) {

                                $factual_percentage = 100 * $getCalculation["xirr"];

                                $require_payments = $getCalculation["require_payments"];

                                $sum_payments = $getCalculation["sum_payments"];

                                $curr_variation["id"] = $product_variation->id;

                                $curr_variation["product_id"] = $product->id;

                                $curr_variation["providing_type"] = $product_variation->providing_type;

                                $curr_variation["percentage_type"] = $product_variation->percentage_type;

                                $curr_variation["percentage"] = $product_variation->percentage;

                                $curr_variation["repayment_type"] = $product_variation->repayment_type;

                                $curr_variation["repayment_loan_interval_type_id"] = $product_variation->repayment_loan_interval_type_id;

                                $curr_variation["repayment_percent_interval_type_id"] = $product_variation->repayment_percent_interval_type_id;

                                $curr_variation["factual_percentage"] = $factual_percentage;

                                $curr_variation["require_payments"] = round($require_payments);

                                $curr_variation["sum_payments"] = number_format(round($sum_payments), 0, ",", " ");

                                $curr_variations[$product_variation->id] = $curr_variation;


                                $productsWithVariationsGroupByCompanyCurr = $curr_variation;

                                $productsWithVariationsGroupByCompanyCurr["name"] = $product->name;

                                $productsWithVariationsGroupByCompanyCurr["company_id"] = $product->company_id;

                                $productsWithVariationsGroupByCompanyCurr["companyInfo"] = $product->companyInfo;

                                $productsWithVariationsGroupByCompany[] = $productsWithVariationsGroupByCompanyCurr;
                            }
                        }

                        $curr_variations = $this->arrayMultisort($curr_variations, "factual_percentage");

                        $productsWithVariationsCurr["variations"] = $curr_variations;

                        $min_factual_percentage = min(array_column($curr_variations, 'factual_percentage'));

                        $productsWithVariationsCurr["min_factual_percentage"] = $min_factual_percentage;

                        $productsWithVariations[] = $productsWithVariationsCurr;

                    }
                }

                $productsWithVariationsGroupByCompany = $this->arrayMultisort($productsWithVariationsGroupByCompany, "factual_percentage");

                $productsWithVariationsGroupByCompany = collect($productsWithVariationsGroupByCompany)->groupBy('company_id');

                $productsWithVariationsGroupByCompany = $this->paginateCollection($productsWithVariationsGroupByCompany, $this->per_page, 'page_by_company');


                $productsWithVariations = collect($productsWithVariations)->sortBy('min_factual_percentage');

                $productsWithVariations = $this->paginateCollection($productsWithVariations, $this->per_page);

                $productsGroupByCompanyIds = array_unique($products->pluck('company_id')->toArray());

                $productsGroupByCompany = [];

                foreach ($productsGroupByCompanyIds as $productCompanyId) {
                    $productsGroupByCompany[] = $products->where('company_id', $productCompanyId);
                }

                $request_results_count = $products->sum('variations_count');//->count();

                $productsFiltersSingleCounts = $this->compareProductsGetSomeFilters($belonging_id, $products);

                $percentage_types = $productsFiltersSingleCounts["percentage_types"];

                $security_types = $productsFiltersSingleCounts["security_types"];

                $providing_types = $productsFiltersSingleCounts["providing_types"];

                $repayment_types = $productsFiltersSingleCounts["repayment_types"];

                $privileged_term_statuses = $productsFiltersSingleCounts["privileged_term_statuses"];

                $special_project_statuses = $productsFiltersSingleCounts["special_project_statuses"];
            }
        } else {
            $validator = Validator::make($request->all(), []);

            $products = NULL;

            $productsWithVariationsGroupByCompany = NULL;

            $productsWithVariations = NULL;

            $request_results_count = 0;

            $percentage_types = NULL;

            $providing_types = NULL;

            $security_types = NULL;

            $repayment_types = NULL;

            $privileged_term_statuses = null;

            $special_project_statuses = null;
        }

        $errors = $validator->errors();

        $previousUrl = $this->loansPreviousUrl($request);

        $getCompareInfo = $this->removeIfDiffConfidentionCompareInfo($belonging_id, ["term" => $loan_term_search_in_days, "loan_amount" => $loan_amount]);

        return view('compare.compareLoanRefinancings',
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

                "loan_term_search_in_days" => $loan_term_search_in_days,

                "loan_amount_min" => $loan_amount_min,

                "loan_amount_max" => $loan_amount_max,

                "errors" => $errors,

                "products" => $products,

                "productsWithVariationsGroupByCompany" => $productsWithVariationsGroupByCompany,

                "productsWithVariations" => $productsWithVariations,

                "request_results_count" => $request_results_count,

                "previousUrl" => $previousUrl,

                "percentage_types" => $percentage_types,

                "providing_types" => $providing_types,

                "security_types" => $security_types,

                "repayment_types" => $repayment_types,

                "special_project_statuses" => $special_project_statuses,

                "privileged_term_statuses" => $privileged_term_statuses,

                "getCompareInfo" => $getCompareInfo,
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

        $yes_no_answers = YesNo::all();

        $deposit_types_list = DepositTypesList::all();

        $deposit_money_min = Deposit::min('deposit_money_from');

        $deposit_money_max = Deposit::max('deposit_money_to');

        $time_type = $request->time_type;

        $loan_term = $request->loan_term;

        $loan_amount = $request->loan_amount;

        $currency = $request->currency;

        $deposit_type = $request->deposit_type;

        if ($time_type == 1 || $time_type == "" || is_null($time_type)) {
            $time_type = 1;

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

                $deposit_interest_rates_payments = NULL;

                $deposit_capitalizations_list = NULL;

                $deposits_specials_list = NULL;

                $money_increasing = NULL;

                $money_decreasing = NULL;

                $currency_changing = NULL;

                $deposit_interruption = NULL;

                $minimum_money = NULL;
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

                $productsFiltersSingleCounts = $this->compareProductsGetSomeFilters($belonging_id, $products);

                $deposit_interest_rates_payments = $productsFiltersSingleCounts["deposit_interest_rates_payments"];

                $deposit_capitalizations_list = $productsFiltersSingleCounts["deposit_capitalizations_list"];

                $deposits_specials_list = $productsFiltersSingleCounts["deposits_specials_list"];

                $money_increasing = $productsFiltersSingleCounts["money_increasing"];

                $money_decreasing = $productsFiltersSingleCounts["money_decreasing"];

                $currency_changing = $productsFiltersSingleCounts["currency_changing"];

                $deposit_interruption = $productsFiltersSingleCounts["deposit_interruption"];

                $minimum_money = [];

                foreach ($yes_no_answers as $yes_no_answer) {
                    if ($yes_no_answer->id == 1) {
                        $minimum_money[$yes_no_answer->id] = $products->where('minimum_money', $yes_no_answer->id)->count();
                    } else {
                        $minimum_money[$yes_no_answer->id] = $products->where('minimum_money', '!=', 1)->count();
                    }
                }
            }
        } else {
            $validator = Validator::make($request->all(), []);

            $products = NULL;

            $productsGroupByCompany = NULL;

            $request_results_count = 0;

            $deposit_interest_rates_payments = NULL;

            $deposit_capitalizations_list = NULL;

            $deposits_specials_list = NULL;

            $money_increasing = NULL;

            $money_decreasing = NULL;

            $currency_changing = NULL;

            $deposit_interruption = NULL;

            $minimum_money = NULL;
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

                "yes_no_answers" => $yes_no_answers,

                "deposit_types_list" => $deposit_types_list,

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

                "deposit_interest_rates_payments" => $deposit_interest_rates_payments,

                "deposit_capitalizations_list" => $deposit_capitalizations_list,

                "deposits_specials_list" => $deposits_specials_list,

                "money_increasing" => $money_increasing,

                "money_decreasing" => $money_decreasing,

                "currency_changing" => $currency_changing,

                "deposit_interruption" => $deposit_interruption,

                "minimum_money" => $minimum_money,
            ]);
    }

    /**
     * compare Mortgages.
     *
     * @return \Illuminate\Http\Response
     */
    public
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

        $time_type = $request->time_type;

        $loan_term = $request->loan_term;

        $currency = $request->currency;

        $purpose_type = $request->purpose_type;

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
            $time_type = 1;

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

                'purpose_type' => 'required',

                'currency' => 'required',
            ]);

            $errors = $validator->errors();

            if ($errors->count() > 0) {
                $products = NULL;

                $loan_amount_converted = NULL;

                $productsWithVariations = NULL;

                $productsWithVariationsGroupByCompany = NULL;

                $request_results_count = 0;

                $purposeTypes = NULL;

                $percentage_types = NULL;

                $providing_types = NULL;

                $security_types = NULL;

                $repayment_types = NULL;

                $privileged_term_statuses = null;

                $special_project_statuses = null;
            } else {
                $products = Mortgage::with('companyInfo')->whereIn('status', [1, 2, 5])
                    ->has('variations', '>', 0)
                    ->withCount('variations');

                if (!is_null($loan_amount)) {
                    $products->where(function ($query) use ($loan_amount) {
                        $query->where('loan_amount_from', '<=', (float)$loan_amount);
                    });
                    $products->where(function ($query) use ($loan_amount) {
                        $query->where('loan_amount_to', '>=', (float)$loan_amount)
                            ->orWhere(function ($query) use ($loan_amount) {
                                $query->where('loan_amount_to', '=', 0);
                            });
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
                        $query->where('currency', (int)$currency);
                    });
                }


                $products = $products->get();

                $productsWithVariations = [];

                $productsWithVariationsGroupByCompany = [];

                $loan_amount_converted = $this->getLoanAmountConverted($currency, $loan_amount);

                $cost_converted = $this->getLoanAmountConverted($currency, $cost);

                foreach ($products as $key => $product) {

                    $privileged_term_err = 0;

                    $calculable_err = $this->checkProductParamsCalculableErr($belonging_id, $product, $loan_amount_converted, $cost);

                    if ($product->privileged_term == 1) {

                        if ($product->privileged_term_loan_time_type == 1) {

                            $privileged_term_search_in_days = $product->privileged_term_loan;
                        } else if ($product->privileged_term_loan_time_type == 2) {

                            $privileged_term_search_in_days = $product->privileged_term_loan * 30;
                        } else if ($product->privileged_term_loan_time_type == 3) {

                            $privileged_term_search_in_days = $product->privileged_term_loan * 365;
                        }
                        if ($privileged_term_search_in_days >= $loan_term_search_in_days) {
                            $privileged_term_err = 1;
                        }
                    }

                    if ($calculable_err == 1 || $privileged_term_err == 1) {

                        $products->forget($key);
                    }

                    if ($privileged_term_err == 0 && $calculable_err == 0) {

                        $productsWithVariationsCurr = [];

                        $productsWithVariationsCurr["id"] = $product->id;

                        $productsWithVariationsCurr["name"] = $product->name;

                        $productsWithVariationsCurr["company_id"] = $product->company_id;

                        $productsWithVariationsCurr["companyInfo"] = $product->companyInfo;

                        $curr_variations = [];

                        foreach ($product->variations as $product_variation) {

                            $curr_variation = [];

                            $getCalculation = $this->getCalculation($product, $product_variation, $cost_converted, $loan_amount_converted, $loan_term, $loan_term_search_in_days, 0, $time_type);//calculate factual_percentage and other

                            if (is_numeric($getCalculation["xirr"])) {

                                $factual_percentage = 100 * $getCalculation["xirr"];

                                $require_payments = $getCalculation["require_payments"];

                                $sum_payments = $getCalculation["sum_payments"];

                                $curr_variation["id"] = $product_variation->id;

                                $curr_variation["product_id"] = $product->id;

                                $curr_variation["providing_type"] = $product_variation->providing_type;

                                $curr_variation["percentage_type"] = $product_variation->percentage_type;

                                $curr_variation["percentage"] = $product_variation->percentage;

                                $curr_variation["repayment_type"] = $product_variation->repayment_type;

                                $curr_variation["repayment_loan_interval_type_id"] = $product_variation->repayment_loan_interval_type_id;

                                $curr_variation["repayment_percent_interval_type_id"] = $product_variation->repayment_percent_interval_type_id;

                                $curr_variation["factual_percentage"] = $factual_percentage;

                                $curr_variation["require_payments"] = round($require_payments);

                                $curr_variation["sum_payments"] = number_format(round($sum_payments), 0, ",", " ");

                                $curr_variations[$product_variation->id] = $curr_variation;


                                $productsWithVariationsGroupByCompanyCurr = $curr_variation;

                                $productsWithVariationsGroupByCompanyCurr["name"] = $product->name;

                                $productsWithVariationsGroupByCompanyCurr["company_id"] = $product->company_id;

                                $productsWithVariationsGroupByCompanyCurr["companyInfo"] = $product->companyInfo;

                                $productsWithVariationsGroupByCompany[] = $productsWithVariationsGroupByCompanyCurr;
                            }
                        }

                        $curr_variations = $this->arrayMultisort($curr_variations, "factual_percentage");

                        $productsWithVariationsCurr["variations"] = $curr_variations;


                        if (is_array(array_column($curr_variations, 'factual_percentage')) && count(array_column($curr_variations, 'factual_percentage')) > 0) {
                            $min_factual_percentage = min(array_column($curr_variations, 'factual_percentage'));

                            $productsWithVariationsCurr["min_factual_percentage"] = $min_factual_percentage;

                            $productsWithVariations[] = $productsWithVariationsCurr;
                        }

                    }
                }

                $productsWithVariationsGroupByCompany = $this->arrayMultisort($productsWithVariationsGroupByCompany, "factual_percentage");

                $productsWithVariationsGroupByCompany = collect($productsWithVariationsGroupByCompany)->groupBy('company_id');

                $productsWithVariationsGroupByCompany = $this->paginateCollection($productsWithVariationsGroupByCompany, $this->per_page, 'page_by_company');


                $productsWithVariations = collect($productsWithVariations)->sortBy('min_factual_percentage');

                $productsWithVariations = $this->paginateCollection($productsWithVariations, $this->per_page);

                $productsGroupByCompanyIds = array_unique($products->pluck('company_id')->toArray());

                $productsGroupByCompany = [];

                foreach ($productsGroupByCompanyIds as $productCompanyId) {
                    $productsGroupByCompany[] = $products->where('company_id', $productCompanyId);
                }

                $request_results_count = $products->sum('variations_count');//->count();

                $productsFiltersSingleCounts = $this->compareProductsGetSomeFilters($belonging_id, $products);

                $purposeTypes = $productsFiltersSingleCounts["purposeTypes"];

                $percentage_types = $productsFiltersSingleCounts["percentage_types"];

                $security_types = $productsFiltersSingleCounts["security_types"];

                $providing_types = $productsFiltersSingleCounts["providing_types"];

                $repayment_types = $productsFiltersSingleCounts["repayment_types"];

                $privileged_term_statuses = $productsFiltersSingleCounts["privileged_term_statuses"];

                $special_project_statuses = $productsFiltersSingleCounts["special_project_statuses"];
            }
        } else {
            $validator = Validator::make($request->all(), []);

            $products = NULL;

            $loan_amount_converted = NULL;

            $productsWithVariations = NULL;

            $productsWithVariationsGroupByCompany = NULL;

            $request_results_count = 0;

            $purposeTypes = NULL;

            $percentage_types = NULL;

            $providing_types = NULL;

            $security_types = NULL;

            $repayment_types = NULL;

            $privileged_term_statuses = null;

            $special_project_statuses = null;
        }

        $getCompareInfo = $this->getCompareInfo();

        $errors = $validator->errors();

        $previousUrl = $this->loansPreviousUrl($request);

        return view('compare.compareMortgages',
            [
                "belongings" => $belongings,

                "currProductByBelongingsView" => $currProductByBelongingsView,

                "currBelonging" => $currBelonging,

                "belonging_id" => $belonging_id,

                "mortgagePurposeTypes" => $mortgagePurposeTypes,

                "loanCurrenciesTypes" => $loanCurrenciesTypes,

                "purpose_type" => $purpose_type,

                "yes_no_all_answers" => $yes_no_all_answers,

                "yes_no_answers" => $yes_no_answers,

                "loan_amount_min" => $loan_amount_min,

                "loan_amount_max" => $loan_amount_max,

                "repayment_loan_interval_types" => $repayment_loan_interval_types,

                "repayment_percent_interval_types" => $repayment_percent_interval_types,

                "time_types" => $time_types,

                "time_type" => $time_type,

                "loan_term" => $loan_term,

                "cost" => $cost,

                "loan_amount" => $loan_amount,

                "prepayment" => $prepayment,

                "prepayment_final" => $prepayment_final,

                "loan_amount_converted" => $loan_amount_converted,

                "loan_term_search_in_days" => $loan_term_search_in_days,

                "currency" => $currency,

                "errors" => $errors,

                "products" => $products,

                "productsWithVariationsGroupByCompany" => $productsWithVariationsGroupByCompany,

                "productsWithVariations" => $productsWithVariations,

                "request_results_count" => $request_results_count,

                "previousUrl" => $previousUrl,

                "purposeTypes" => $purposeTypes,

                "percentage_types" => $percentage_types,

                "providing_types" => $providing_types,

                "security_types" => $security_types,

                "repayment_types" => $repayment_types,

                "special_project_statuses" => $special_project_statuses,

                "privileged_term_statuses" => $privileged_term_statuses,

                "getCompareInfo" => $getCompareInfo,
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

        $currency = $request->input('currency');

        if (count($request->all()) > 0) {
            $validator = Validator::make($request->all(), [
                'currency' => 'required',
            ]);

            $errors = $validator->errors();

            if ($errors->count() > 0) {
                $products = NULL;

                $productsGroupByCompany = NULL;

                $request_results_count = 0;

                $payment_card_types = NULL;

                $payment_card_product_types = NULL;

                $payment_card_regions = NULL;

                $payment_extra_cards = NULL;

                $payment_specials_cards = NULL;
            } else {
                $products = PaymentCard::with('companyInfo')
                    ->with('creditLineInfo')
                    ->with('productsPaymentCardsType')
                    ->with('productsPaymentCardsCurrencies')
                    ->with('productsPaymentCardsCardType')
                    ->with('productsPaymentCardsRegion')
                    ->with('productsSpecialsCardsType')
                    ->with('productsPaymentCardsExtraType')
                    ->with('attachmentCardInfo');

                if (!is_null($currency)) {
                    $products->whereHas('productsPaymentCardsCurrencies', function ($q) use ($currency) {
                        $q->where('currency_id', $currency);
                    });
                }


                $products = $products->get();

                $productsGroupByCompanyIds = array_unique($products->pluck('company_id')->toArray());

                $productsGroupByCompany = [];

                foreach ($productsGroupByCompanyIds as $productCompanyId) {
                    $productsGroupByCompany[] = $products->where('company_id', $productCompanyId);
                }

                $request_results_count = $products->count();

                $productsFiltersSingleCounts = $this->compareProductsGetSomeFilters($belonging_id, $products);

//                $payment_card_currencies_types = $productsFiltersSingleCounts["payment_card_currencies_types"];

                $payment_card_types = $productsFiltersSingleCounts["payment_card_types"];

                $payment_card_product_types = $productsFiltersSingleCounts["payment_card_product_types"];

                $payment_extra_cards = $productsFiltersSingleCounts["payment_extra_cards"];

                $payment_card_regions = $productsFiltersSingleCounts["payment_card_regions"];

                $payment_specials_cards = $productsFiltersSingleCounts["payment_specials_cards"];
            }
        } else {
            $validator = Validator::make($request->all(), []);

            $products = NULL;

            $productsGroupByCompany = NULL;

            $request_results_count = 0;

            $payment_card_types = NULL;

            $payment_card_product_types = NULL;

            $payment_card_regions = NULL;

            $payment_extra_cards = NULL;

            $payment_specials_cards = NULL;
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

                "currency" => $currency,

                "errors" => $errors,

                "products" => $products,

                "productsGroupByCompany" => $productsGroupByCompany,

                "previousUrl" => $previousUrl,

                "request_results_count" => $request_results_count,

                "payment_card_types" => $payment_card_types,

                "payment_card_product_types" => $payment_card_product_types,

                "payment_card_regions" => $payment_card_regions,

                "payment_extra_cards" => $payment_extra_cards,

                "payment_specials_cards" => $payment_specials_cards,
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

        $transfer_types = TransferType::all();

        $money_transfer_currencies_all_types = MoneyTransferCurrenciesAllType::all();

        $transfer_amount = $request->input('transfer_amount');

//        $money_transfer_amount_min = MoneyTransfer::min('money_transfer_amount_from');
//
//        $money_transfer_amount_max = MoneyTransfer::max('money_transfer_amount_to');

//        if (is_null($money_transfer_amount_min)) {
//            $money_transfer_amount_min = 0;
//        }

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

                $transfer_systems = NULL;

                $transfer_banks = NULL;
            } else {
                $products = MoneyTransfer::with('companyInfo')
                    ->with('transferType')
                    ->with('countriesInfo');

//                if (!is_null($transfer_amount)) {
//                    $products->whereHas('moneyTransferAmountsTermsCommissionFee', function ($q) use ($transfer_amount) {
//                        $q->where('money_transfer_amount_from', '<=', (float)$transfer_amount);
//
//                        $q->where('money_transfer_amount_to', '>=', (float)$transfer_amount);
//                    });
//                }

//                if (!is_null($country)) {
//                    $products->whereHas('countriesInfo', function ($q) use ($country) {
//                        $q->where('country_id', $country);
//                    });
//                }
//                if (!is_null($currency)) {
//                    $products->whereHas('currenciesInfo', function ($q) use ($currency) {
//                        $q->where('currency_id', $currency);
//                    });
//                }

                $products = $products->get();

                $productsGroupByCompanyIds = array_unique($products->pluck('company_id')->toArray());

                $productsGroupByCompany = [];

                foreach ($productsGroupByCompanyIds as $productCompanyId) {
                    $productsGroupByCompany[] = $products->where('company_id', $productCompanyId);
                }

                $request_results_count = $products->count();

                $productsFiltersSingleCounts = $this->compareProductsGetSomeFilters($belonging_id, $products);

                $transfer_systems = $productsFiltersSingleCounts["transfer_systems"];

                $transfer_banks = $productsFiltersSingleCounts["transfer_banks"];
            }
        } else {
            $validator = Validator::make($request->all(), []);

            $products = NULL;

            $productsGroupByCompany = NULL;

            $request_results_count = 0;

            $transfer_systems = NULL;

            $transfer_banks = NULL;
        }

        $getCompareInfo =   $this->getCompareInfo();
//        $getCompareInfo = $this->removeIfDiffConfidentionCompareInfo($belonging_id, ["term" => $loan_term, "age" => $age, "country" => $country]);

        $errors = $validator->errors();

        $previousUrl = $this->loansPreviousUrl($request);

        return view('compare.compareMoneyTransfers',
            [
                "belongings" => $belongings,

                "currProductByBelongingsView" => $currProductByBelongingsView,

                "currBelonging" => $currBelonging,

                "belonging_id" => $belonging_id,

                "yes_no_answers" => $yes_no_answers,

                "countries" => $countries,

                "money_transfer_currencies_all_types" => $money_transfer_currencies_all_types,

                "transfer_types" => $transfer_types,
//
//                "money_transfer_amount_min" => $money_transfer_amount_min,
//
//                "money_transfer_amount_max" => $money_transfer_amount_max,

                "currency" => $currency,

                "country" => $country,

                "transfer_amount" => $transfer_amount,

                "errors" => $errors,

                "products" => $products,

                "productsGroupByCompany" => $productsGroupByCompany,

                "request_results_count" => $request_results_count,

                "transfer_systems" => $transfer_systems,

                "transfer_banks" => $transfer_banks,

                "getCompareInfo" => $getCompareInfo,

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

        $countries_whole_world_id = 246;

        $countries_schengen_concret_id = 247;

        $countries_schengen_ids_arr = Country::where('is_schengen', 1)->pluck('id')->toArray();

        $currProductByBelongingsView = ProductByBelongingsView::where("belonging_id", $belonging_id)->first();

        $belongings = Belonging::with('productsByBelongingInfo')->get();

        $currBelonging = Belonging::where("id", $belonging_id)->first();

        $countries = Country::all();

        $time_types = TimeType::all();

        $yes_no_answers = YesNo::all();

        $time_type = $request->time_type;

        $loan_term = $request->travel_term;

        $age = $request->age;

        $country = $request->travel_country;

        $min_age = TravelInsurancesVariation::min('travel_age_from');

        $max_age = TravelInsurancesVariation::max('travel_age_to');

        $min_age = intval($min_age);

        if (intval($max_age) == 0) {
            $max_age = 100;
        }

        if ($time_type == 1 || $time_type == "" || is_null($time_type)) {
            $time_type = 1;

            $loan_term_search_in_days = $loan_term;
        } else if ($time_type == 2) {
            $loan_term_search_in_days = $loan_term * 30;
        } else if ($time_type == 3) {
            $loan_term_search_in_days = $loan_term * 365;
        }
//        $executionStartTime = microtime(true);
        if (count($request->all()) > 0) {
            $validator = Validator::make($request->all(), [
                'travel_country' => 'required',

                'travel_term' => 'required|numeric',

                'age' => 'required|numeric',
            ]);

            $errors = $validator->errors();

            if ($errors->count() > 0) {

                $products = NULL;

                $productsWithVariations = NULL;

                $productsWithVariationsGroupByCompany = NULL;

                $request_results_count = 0;

                $non_recoverable_expenses_answers = NULL;

                $term_inputs_quantities = NULL;
            } else {
                $products = TravelInsurance::with('companyInfo')
                    ->with(['variations' => function ($q) use ($age, $loan_term_search_in_days) {
                        $q->where('travel_age_from', '<=', (int)$age);
                        $q->where('travel_age_to', '>=', (int)$age);

                        $q->where('travel_insurance_term_from', '<=', (int)$loan_term_search_in_days);
                        $q->where('travel_insurance_term_to', '>=', (int)$loan_term_search_in_days);
                    }])
//                    ->with(['countriesInfo' => function ($q) use ($country) {
//                        $q->where('country_id', $country);
//                    }])
                    ->withCount('variations')
                    ->where('status', 2);
//                    ->has('variations', '>', 0)


                if (!is_null($country)) {
                    if (!in_array($country, $countries_schengen_ids_arr)) {
                        $products->whereHas('countriesInfo', function ($q) use ($country, $countries_whole_world_id) {
                            $q->where('country_id', $country)
                                ->orWhere(function ($query) use ($countries_whole_world_id) {
                                    $query->where('country_id', $countries_whole_world_id);
                                });
                        });
                    } else {
                        $products->whereHas('countriesInfo', function ($q) use ($country, $countries_whole_world_id, $countries_schengen_concret_id) {
                            $q->where('country_id', $country)
                                ->orWhere(function ($query) use ($countries_whole_world_id) {
                                    $query->where('country_id', $countries_whole_world_id);
                                })->orWhere(function ($query) use ($countries_schengen_concret_id) {
                                    $query->where('country_id', $countries_schengen_concret_id);
                                });
                        });
                    }
                }

                $products = $products->get();

                $productsWithVariations = [];

                $productsWithVariationsGroupByCompany = [];

                $request_results_count = 0;


                foreach ($products as $product) {

                    if ($product->variations->count() > 0) {

                        $request_results_count = $request_results_count + $product->variations->count();

                        $productsWithVariationsCurr = [];

                        $productsWithVariationsCurr["id"] = $product->id;

                        $productsWithVariationsCurr["countryInfo"] = $product->countriesInfo;

                        $productsWithVariationsCurr["name"] = $product->name;

                        $productsWithVariationsCurr["company_id"] = $product->company_id;

                        $productsWithVariationsCurr["companyInfo"] = $product->companyInfo;

                        $curr_variations = [];

                        foreach ($product->variations as $product_variation) {

                            $curr_variation = [];

                            $curr_variation["id"] = $product_variation->id;

                            $curr_variation["product_id"] = $product->id;

                            $curr_variation["travel_insurance_term_from"] = $product_variation->travel_insurance_term_from;

                            $curr_variation["travel_insurance_term_to"] = $product_variation->travel_insurance_term_to;

                            $curr_variation["term_inputs_quantity"] = $product_variation->term_inputs_quantity;

                            $curr_variation["term_days_quantity"] = $product_variation->term_days_quantity;

                            $curr_variation["term_coefficient"] = $product_variation->term_coefficient;


                            $curr_variation["travel_age_from"] = $product_variation->travel_age_from;

                            $curr_variation["travel_age_to"] = $product_variation->travel_age_to;

                            $curr_variation["travel_age_coefficient"] = $product_variation->travel_age_coefficient;


                            $curr_variation["travel_insurance_amount"] = $product_variation->travel_insurance_amount;

                            $curr_variation["currency"] = $product_variation->currency;

                            $curr_variation["travel_insurance_tariff_amount"] = $product_variation->travel_insurance_tariff_amount;

                            $curr_variation["travel_insurance_percent"] = $product_variation->travel_insurance_percent;

                            $calcTravelInsuranceFee = $this->calcTravelInsuranceFee($product_variation->travel_insurance_amount, $product_variation->currency,
                                $product_variation->travel_insurance_tariff_amount, $product_variation->travel_insurance_percent, $product_variation->term_coefficient, $product_variation->travel_age_coefficient);

                            $curr_variation["insurance_fee"] = round($calcTravelInsuranceFee);
//                            $curr_variation["insurance_fee"] = number_format(round($calcTravelInsuranceFee),0,","," ");

                            $curr_variations[$product_variation->id] = $curr_variation;


                            $productsWithVariationsGroupByCompanyCurr = $curr_variation;

                            $productsWithVariationsGroupByCompanyCurr["name"] = $product->name;

                            $productsWithVariationsGroupByCompanyCurr["company_id"] = $product->company_id;

                            $productsWithVariationsGroupByCompanyCurr["companyInfo"] = $product->companyInfo;

                            $productsWithVariationsGroupByCompany[] = $productsWithVariationsGroupByCompanyCurr;
                        }

                        $curr_variations = $this->arrayMultisort($curr_variations, "insurance_fee");

                        $productsWithVariationsCurr["variations"] = $curr_variations;

                        $min_insurance_fee = min(array_column($curr_variations, 'insurance_fee'));

                        $productsWithVariationsCurr["min_insurance_fee"] = $min_insurance_fee;

                        $productsWithVariations[] = $productsWithVariationsCurr;
                    }
                }


                $productsWithVariationsGroupByCompany = $this->arrayMultisort($productsWithVariationsGroupByCompany, "insurance_fee");

                $productsWithVariationsGroupByCompany = collect($productsWithVariationsGroupByCompany)->groupBy('company_id');

                $productsWithVariationsGroupByCompany = $this->paginateCollection($productsWithVariationsGroupByCompany, $this->per_page, 'page_by_company');


                $productsWithVariations = collect($productsWithVariations)->sortBy('min_insurance_fee');

                $productsWithVariations = $this->paginateCollection($productsWithVariations, $this->per_page);

                $productsFiltersSingleCounts = $this->compareProductsGetSomeFilters($belonging_id, $products);

                $non_recoverable_expenses_answers = $productsFiltersSingleCounts["non_recoverable_expenses_answers"];

                $term_inputs_quantities = $productsFiltersSingleCounts["term_inputs_quantities"];
            }
        } else {
            $validator = Validator::make($request->all(), []);

            $products = NULL;

            $productsWithVariations = NULL;

            $productsWithVariationsGroupByCompany = NULL;

            $request_results_count = 0;

            $non_recoverable_expenses_answers = NULL;

            $term_inputs_quantities = NULL;
        }

        $errors = $validator->errors();

        $getCompareInfo = $this->removeIfDiffConfidentionCompareInfo($belonging_id, ["term" => $loan_term, "age" => $age, "country" => $country]);

        $previousUrl = $this->loansPreviousUrl($request);
//        $executionEndTime = microtime(true);
//        $seconds = $executionEndTime - $executionStartTime;
//        echo $seconds;echo "</br>";
        return view('compare.compareTravelInsurances',
            [
                "belongings" => $belongings,

                "currProductByBelongingsView" => $currProductByBelongingsView,

                "currBelonging" => $currBelonging,

                "belonging_id" => $belonging_id,

                "yes_no_answers" => $yes_no_answers,

                "countries" => $countries,

                "time_types" => $time_types,

                "time_type" => $time_type,

                "loan_term" => $loan_term,

                "loan_term_search_in_days" => $loan_term_search_in_days,

                "age" => $age,

                "country" => $country,

                "min_age" => $min_age,

                "max_age" => $max_age,

                "errors" => $errors,

                "products" => $products,

                "productsWithVariations" => $productsWithVariations,

                "productsWithVariationsGroupByCompany" => $productsWithVariationsGroupByCompany,

                "request_results_count" => $request_results_count,

                "non_recoverable_expenses_answers" => $non_recoverable_expenses_answers,

                "term_inputs_quantities" => $term_inputs_quantities,

                "getCompareInfo" => $getCompareInfo,

                "previousUrl" => $previousUrl,
            ]);
    }

}