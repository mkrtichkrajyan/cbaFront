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
use App\Models\GoldAssayType;
use App\Models\GoldLoan;
use App\Models\GoldPledgeType;
use App\Models\LoanCurrenciesType;
use App\Models\LoanRefinancing;
use App\Models\LoanRefinancingPurposeType;
use App\Models\LoanServicePayTypes;
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
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
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
     * compare compare Car Loans Inner page.
     *
     * @return \Illuminate\Http\Response
     */
    public
    function compareCarLoansInner(Request $request)
    {
        $belongings = Belonging::with('productsByBelongingInfo')->get();

        $belonging_id = 1;

        $currProductByBelongingsView = ProductByBelongingsView::where("belonging_id", $belonging_id)->first();

        $productPercentageTypesArr = ["1" => "+", "2" => "-", "3" => "±"];

        $currBelonging = Belonging::where("id", $belonging_id)->first();

        $time_types = TimeType::all();

        $security_types = SecurityType::all();

        $repayment_types = RepaymentType::all();

        $repayment_loan_interval_types = RepaymentLoanIntervalType::all();

        $repayment_percent_interval_types = RepaymentPercentIntervalType::all();

        $providing_types = ProvidingType::all();

        $percentage_types = PercentageType::all();

        $loan_service_pay_types = LoanServicePayTypes::all();

        $absolute_amount_or_percent_only_pay_types = AbsoluteAmountORPercentOnlyPayType::all();

        $absolute_amount_or_percent_pay_types = AbsoluteAmountOrPercentPayType::all();

        $periodicity_types = PeriodicityType::all();

        $special_projects = SpecialProject::all();

        $previous_url = $currBelonging->productsByBelongingInfo->first()->compare_url;

        $getCompareInfo = $this->getCompareInfo();

        $compareVariations = $getCompareInfo[$belonging_id]["checked_variations_full_info"];

        $compareVariationsData = [];

        foreach ($compareVariations as $key => $compareVariation) {

            $curr_data = [];

            $curr_data["product_info"] = CarLoan::with('companyInfo')->with('carInfo')->with('otherPayments')
                ->with('carSalons')->find($compareVariation["product_id"]);

            $curr_data["variation_info"] = ProductsVariation::where(DB::raw("md5(unique_options)"), $compareVariation["curr_variation_options"])->first();

            $curr_data["cost"] = $compareVariation["cost"];

            $curr_data["prepayment"] = $compareVariation["prepayment"];

            $curr_data["term"] = $compareVariation["term"];


            $curr_data["loan_amount"] = intval($compareVariation["cost"]) - intval($curr_data["prepayment"]);

            $compareVariationsData[] = $curr_data;
        }

        return view('compare.inner.compareCarLoansInner',
            [
                "belongings" => $belongings,

                "belonging_id" => $belonging_id,

                "currProductByBelongingsView" => $currProductByBelongingsView,

                "currBelonging" => $currBelonging,

                "productPercentageTypesArr" => $productPercentageTypesArr,

                "time_types" => $time_types,

                "security_types" => $security_types,

                "repayment_types" => $repayment_types,

                "repayment_loan_interval_types" => $repayment_loan_interval_types,

                "repayment_percent_interval_types" => $repayment_percent_interval_types,

                "providing_types" => $providing_types,

                "percentage_types" => $percentage_types,

                "loan_service_pay_types" => $loan_service_pay_types,

                "periodicity_types" => $periodicity_types,

                "special_projects" => $special_projects,

                "absolute_amount_or_percent_pay_types" => $absolute_amount_or_percent_pay_types,

                "absolute_amount_or_percent_only_pay_types" => $absolute_amount_or_percent_only_pay_types,

                "previous_url" => $previous_url,

                "getCompareInfo" => $getCompareInfo,

                "compareVariationsData" => $compareVariationsData,
            ]);
    }

    /**
     * compare Car Loans Filters.
     *
     * @return \Illuminate\Http\Response
     */
    public
    function carLoansFilters(Request $request)
    {
        $belonging_id = 1;

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

        if ($time_type_search == 1 || $time_type_search == "" || is_null($time_type_search)) {

            $loan_term_search_in_days = $loan_term_search;
        } else if ($time_type_search == 2) {

            $loan_term_search_in_days = $loan_term_search * 30;
        } else if ($time_type_search == 3) {

            $loan_term_search_in_days = $loan_term_search * 365;
        }

        if ($car_cost > 0) {
            $prepayment_percent = 100 * $prepayment_final / $car_cost;
        } else {
            $prepayment_percent = null;
        }


        if (is_null($providing_types)) {
            $providing_types = ProvidingType::pluck('id')->toArray();
        } else {
            $providing_types = array_merge($providing_types, array(1));
        }

        if (is_null($percentage_types)) {
            $percentage_types = PercentageType::pluck('id')->toArray();
        } else {
            $percentage_types = array_merge($percentage_types, array(1));
        }

        if (is_null($repayment_types)) {
            $repayment_types = RepaymentType::pluck('id')->toArray();
        } else {
            $repayment_types = array_merge($repayment_types, array(1));
        }

        $products = CarLoan::with('companyInfo')->with(['variations' => function ($q) use ($providing_types, $percentage_types, $repayment_types) {
            $q->whereIn('providing_type', $providing_types);
            $q->whereIn('percentage_type', $percentage_types);
            $q->whereIn('repayment_type', $repayment_types);
        }])
            ->with('securityTypes')->withCount('variations')->has('variations', '>', 0)
            ->where('status', 2);

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

        if (is_array($car_types) && count($car_types) > 0) {
            $products->where(function ($query) use ($car_types) {
                $query->whereIn('car_type', array_merge($car_types, array(1)));
            });
        }

        if (is_array($repayment_types) && count($repayment_types) > 0) {
            $products->where(function ($query) use ($repayment_types) {
                $query->whereIn('repayment_type', $repayment_types);
            });
        }

        if (is_array($percentage_types) && count($percentage_types) > 0) {
            $products->where(function ($query) use ($percentage_types) {
                $query->whereIn('percentage_type', $percentage_types);
            });
        }

        if (is_array($providing_types) && count($providing_types) > 0) {
            $products->whereHas('variations', function ($q) use ($providing_types) {
                $q->whereIn('providing_type', $providing_types);
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
                    $query->whereIn('privileged_term', $privileged_term_answers);
                    $query->orWhereNull('privileged_term');
                });
            } else {
                $products->where(function ($query) use ($privileged_term_answers) {
                    $query->whereIn('privileged_term', $privileged_term_answers);
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

        $productsWithVariations = [];

        $productsWithVariationsGroupByCompany = [];

        $request_results_count = 0;

        foreach ($products as $product) {

            $request_results_count = $request_results_count + $product->variations->count();

            $productsWithVariationsCurr = [];

            $productsWithVariationsCurr["id"] = $product->id;

            $productsWithVariationsCurr["name"] = $product->name;

            $productsWithVariationsCurr["company_id"] = $product->company_id;

            $productsWithVariationsCurr["companyInfo"] = $product->companyInfo;

            $curr_variations = [];

            foreach ($product->variations as $product_variation) {

                $curr_variation = [];

                $getCalculation = $this->getCalculation($product, $product_variation, $car_cost, $loan_amount, $loan_term_search_in_days, $prepayment_percent, $time_type_search);//calculate factual_percentage and other

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

                $curr_variation["require_payments"] = $require_payments;

                $curr_variation["sum_payments"] = $sum_payments;

                $unique_options = "bel_" . $belonging_id . "_prod_" . $product->id . "_prov_" . $product_variation->providing_type . "_perc_" .
                    $product_variation->percentage_type . "_rep_" . $product_variation->repayment_type . "_rep_loan_" .
                    intval($product_variation->repayment_loan_interval_type_id) . "_rep_perc_" . intval($product_variation->repayment_percent_interval_type_id);

                $unique_options = md5($unique_options);

                $curr_variation["unique_options"] = $unique_options;

                $curr_variations[$product_variation->id] = $curr_variation;


                $productsWithVariationsGroupByCompanyCurr = $curr_variation;

                $productsWithVariationsGroupByCompanyCurr["name"] = $product->name;

                $productsWithVariationsGroupByCompanyCurr["company_id"] = $product->company_id;

                $productsWithVariationsGroupByCompanyCurr["companyInfo"] = $product->companyInfo;

                $productsWithVariationsGroupByCompany[] = $productsWithVariationsGroupByCompanyCurr;
            }

            $curr_variations = $this->arrayMultisort($curr_variations, "factual_percentage");

            $productsWithVariationsCurr["variations"] = $curr_variations;

            $min_factual_percentage = count($curr_variations) > 0 ? min(array_column($curr_variations, 'factual_percentage')) : null;

            $productsWithVariationsCurr["min_factual_percentage"] = $min_factual_percentage;

            $productsWithVariations[] = $productsWithVariationsCurr;
        }

        $productsWithVariations = collect($productsWithVariations)->sortBy('min_factual_percentage');

        $productsWithVariations = $this->paginateCollection($productsWithVariations, 1);

        $productsWithVariationsGroupByCompany = $this->arrayMultisort($productsWithVariationsGroupByCompany, "factual_percentage");

        $productsWithVariationsGroupByCompany = collect($productsWithVariationsGroupByCompany)->groupBy('company_id');

        $productsWithVariationsGroupByCompany = $this->paginateCollection($productsWithVariationsGroupByCompany, 1, 'page_by_company');


        $links = (String)$productsWithVariations->appends([])->links('pagination::bootstrap-4');

        $links_grouped_by_company = (String)$productsWithVariationsGroupByCompany->appends([])->links('pagination::bootstrap-4');

        $getCompareInfo = $this->getCompareInfo();

        $checked_variations = $getCompareInfo[$belonging_id]["checked_variations"];

        return response()->json(
            [
                "belonging_id" => $belonging_id,
                "request_results_count" => $request_results_count,
                "productsWithVariations" => $productsWithVariations,
                "productsWithVariationsGroupByCompany" => $productsWithVariationsGroupByCompany,
                'links' => $links,
                'links_grouped_by_company' => $links_grouped_by_company,
                'checked_variations' => $checked_variations
            ]
        );
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
            }
            else {
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

                            $getCalculation = $this->getCalculation($product, $product_variation, $car_cost, $loan_amount, $loan_term_search_in_days, $prepayment_percent, $time_type);//calculate factual_percentage and other

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

                            $curr_variation["require_payments"] = $require_payments;

                            $curr_variation["sum_payments"] = $sum_payments;

                            $curr_variations[$product_variation->id] = $curr_variation;


                            $productsWithVariationsGroupByCompanyCurr = $curr_variation;

                            $productsWithVariationsGroupByCompanyCurr["name"] = $product->name;

                            $productsWithVariationsGroupByCompanyCurr["company_id"] = $product->company_id;

                            $productsWithVariationsGroupByCompanyCurr["companyInfo"] = $product->companyInfo;

                            $productsWithVariationsGroupByCompany[] = $productsWithVariationsGroupByCompanyCurr;
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

                $productsWithVariationsGroupByCompany = $this->paginateCollection($productsWithVariationsGroupByCompany, 1, 'page_by_company');


                $productsWithVariations = collect($productsWithVariations)->sortBy('min_factual_percentage');

                $productsWithVariations = $this->paginateCollection($productsWithVariations, 1);

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
        }
        else {
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

        $getCompareInfo = $this->getCompareInfo();

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

        $productPercentageTypesArr = ["1" => "+", "2" => "-", "3" => "±"];

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

                $productsWithVariations = NULL;

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
                    ->where('status', 2)
                    ->has('variations', '>', 0)
                    ->withCount('variations')
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

                $productsWithVariations = [];

                foreach ($products as $product) {

                    $productsWithVariationsCurr = [];

                    $productsWithVariationsCurr["id"] = $product->id;

                    $productsWithVariationsCurr["name"] = $product->name;

                    $productsWithVariationsCurr["company_id"] = $product->company_id;

                    $productsWithVariationsCurr["companyInfo"] = $product->companyInfo;

                    $curr_variations = [];

                    foreach ($product->variations as $product_variation) {

                        $curr_variation = [];

                        $factual_percentage = 100 / $product_variation->id; //really getting from calculate factual_percentage function

                        $curr_variation["id"] = $product_variation->id;

                        $curr_variation["providing_type"] = $product_variation->providing_type;

                        $curr_variation["percentage_type"] = $product_variation->percentage_type;

                        $curr_variation["percentage"] = $product_variation->percentage;

                        $curr_variation["repayment_type"] = $product_variation->repayment_type;

                        $curr_variation["repayment_loan_interval_type_id"] = $product_variation->repayment_loan_interval_type_id;

                        $curr_variation["repayment_percent_interval_type_id"] = $product_variation->repayment_percent_interval_type_id;

                        $curr_variation["factual_percentage"] = $factual_percentage;

                        $curr_variations[$product_variation->id] = $curr_variation;
                    }

                    $curr_variations = $this->arrayMultisort($curr_variations, "factual_percentage");

                    $productsWithVariationsCurr["variations"] = $curr_variations;

                    $min_factual_percentage = min(array_column($curr_variations, 'factual_percentage'));

                    $productsWithVariationsCurr["min_factual_percentage"] = $min_factual_percentage;

                    $productsWithVariations[] = $productsWithVariationsCurr;
                }

                $productsWithVariations = collect($productsWithVariations)->sortBy('min_factual_percentage');

                $productsWithVariations = $this->paginateCollection($productsWithVariations, 1);

                $productsGroupByCompanyIds = array_unique($products->pluck('company_id')->toArray());

                $productsGroupByCompany = [];

                foreach ($productsGroupByCompanyIds as $productCompanyId) {
                    $productsGroupByCompany[] = $products->where('company_id', $productCompanyId);
                }

                $request_results_count = $products->count();

                $productsFiltersSingleCounts = $this->compareProductsGetSomeFilters($belonging_id, $products);

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

            $productsGroupByCompany = NULL;

            $productsWithVariations = NULL;

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

                "productsWithVariations" => $productsWithVariations,

                "request_results_count" => $request_results_count,

                "previousUrl" => $previousUrl,

                "percentage_types" => $percentage_types,

                "providing_types" => $providing_types,

                "repayment_types" => $repayment_types,

                "gold_assay_types" => $gold_assay_types,

                "special_project_statuses" => $special_project_statuses,

                "privileged_term_statuses" => $privileged_term_statuses,
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
                $products = CreditLoan::with('companyInfo')->where('status', 2)
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

                foreach ($products as $product) {

                    $productsWithVariationsCurr = [];

                    $productsWithVariationsCurr["id"] = $product->id;

                    $productsWithVariationsCurr["name"] = $product->name;

                    $productsWithVariationsCurr["company_id"] = $product->company_id;

                    $productsWithVariationsCurr["companyInfo"] = $product->companyInfo;

                    $curr_variations = [];

                    foreach ($product->variations as $product_variation) {

                        $curr_variation = [];

                        $factual_percentage = 100 / $product_variation->id; //really getting from calculate factual_percentage function

                        $curr_variation["id"] = $product_variation->id;

                        $curr_variation["providing_type"] = $product_variation->providing_type;

                        $curr_variation["percentage_type"] = $product_variation->percentage_type;

                        $curr_variation["percentage"] = $product_variation->percentage;

                        $curr_variation["repayment_type"] = $product_variation->repayment_type;

                        $curr_variation["repayment_loan_interval_type_id"] = $product_variation->repayment_loan_interval_type_id;

                        $curr_variation["repayment_percent_interval_type_id"] = $product_variation->repayment_percent_interval_type_id;

                        $curr_variation["factual_percentage"] = $factual_percentage;

                        $curr_variations[$product_variation->id] = $curr_variation;
                    }

                    $curr_variations = $this->arrayMultisort($curr_variations, "factual_percentage");

                    $productsWithVariationsCurr["variations"] = $curr_variations;

                    $min_factual_percentage = min(array_column($curr_variations, 'factual_percentage'));

                    $productsWithVariationsCurr["min_factual_percentage"] = $min_factual_percentage;

                    $productsWithVariations[] = $productsWithVariationsCurr;
                }

                $productsWithVariations = collect($productsWithVariations)->sortBy('min_factual_percentage');

                $productsWithVariations = $this->paginateCollection($productsWithVariations, 1);

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

                $creditPurposeTypes = $productsFiltersSingleCounts["creditPurposeTypes"];
            }
        } else {
            $validator = Validator::make($request->all(), []);

            $products = NULL;

            $productsGroupByCompany = NULL;

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

                "productsWithVariations" => $productsWithVariations,

                "request_results_count" => $request_results_count,

                "previousUrl" => $previousUrl,

                "creditPurposeTypes" => $creditPurposeTypes,

                "percentage_types" => $percentage_types,

                "providing_types" => $providing_types,

                "security_types" => $security_types,

                "repayment_types" => $repayment_types,

                "special_project_statuses" => $special_project_statuses,

                "privileged_term_statuses" => $privileged_term_statuses,
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

                $productsWithVariations = NULL;

                $request_results_count = 0;

                $percentage_types = NULL;

                $providing_types = NULL;

                $security_types = NULL;

                $repayment_types = NULL;

                $privileged_term_statuses = null;

                $special_project_statuses = null;
            } else {
                $products = StudentLoan::with('companyInfo')->where('status', 2)
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

                foreach ($products as $product) {

                    $productsWithVariationsCurr = [];

                    $productsWithVariationsCurr["id"] = $product->id;

                    $productsWithVariationsCurr["name"] = $product->name;

                    $productsWithVariationsCurr["company_id"] = $product->company_id;

                    $productsWithVariationsCurr["companyInfo"] = $product->companyInfo;

                    $curr_variations = [];

                    foreach ($product->variations as $product_variation) {

                        $curr_variation = [];

                        $factual_percentage = 100 / $product_variation->id; //really getting from calculate factual_percentage function

                        $curr_variation["id"] = $product_variation->id;

                        $curr_variation["providing_type"] = $product_variation->providing_type;

                        $curr_variation["percentage_type"] = $product_variation->percentage_type;

                        $curr_variation["percentage"] = $product_variation->percentage;

                        $curr_variation["repayment_type"] = $product_variation->repayment_type;

                        $curr_variation["repayment_loan_interval_type_id"] = $product_variation->repayment_loan_interval_type_id;

                        $curr_variation["repayment_percent_interval_type_id"] = $product_variation->repayment_percent_interval_type_id;

                        $curr_variation["factual_percentage"] = $factual_percentage;

                        $curr_variations[$product_variation->id] = $curr_variation;
                    }

                    $curr_variations = $this->arrayMultisort($curr_variations, "factual_percentage");

                    $productsWithVariationsCurr["variations"] = $curr_variations;

                    $min_factual_percentage = min(array_column($curr_variations, 'factual_percentage'));

                    $productsWithVariationsCurr["min_factual_percentage"] = $min_factual_percentage;

                    $productsWithVariations[] = $productsWithVariationsCurr;
                }

                $productsWithVariations = collect($productsWithVariations)->sortBy('min_factual_percentage');

                $productsWithVariations = $this->paginateCollection($productsWithVariations, 1);

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

            $productsGroupByCompany = NULL;

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

                "productsWithVariations" => $productsWithVariations,

                "request_results_count" => $request_results_count,

                "previousUrl" => $previousUrl,

                "percentage_types" => $percentage_types,

                "providing_types" => $providing_types,

                "security_types" => $security_types,

                "repayment_types" => $repayment_types,

                "special_project_statuses" => $special_project_statuses,

                "privileged_term_statuses" => $privileged_term_statuses,
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

                $productsWithVariations = NULL;

                $request_results_count = 0;

                $purposeTypes = NULL;

                $percentage_types = NULL;

                $providing_types = NULL;

                $security_types = NULL;

                $repayment_types = NULL;

                $privileged_term_statuses = null;

                $special_project_statuses = null;
            } else {
                $products = AgricLoan::with('companyInfo')->where('status', 2)
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

                foreach ($products as $product) {

                    $productsWithVariationsCurr = [];

                    $productsWithVariationsCurr["id"] = $product->id;

                    $productsWithVariationsCurr["name"] = $product->name;

                    $productsWithVariationsCurr["company_id"] = $product->company_id;

                    $productsWithVariationsCurr["companyInfo"] = $product->companyInfo;

                    $curr_variations = [];

                    foreach ($product->variations as $product_variation) {

                        $curr_variation = [];

                        $factual_percentage = 100 / $product_variation->id; //really getting from calculate factual_percentage function

                        $curr_variation["id"] = $product_variation->id;

                        $curr_variation["providing_type"] = $product_variation->providing_type;

                        $curr_variation["percentage_type"] = $product_variation->percentage_type;

                        $curr_variation["percentage"] = $product_variation->percentage;

                        $curr_variation["repayment_type"] = $product_variation->repayment_type;

                        $curr_variation["repayment_loan_interval_type_id"] = $product_variation->repayment_loan_interval_type_id;

                        $curr_variation["repayment_percent_interval_type_id"] = $product_variation->repayment_percent_interval_type_id;

                        $curr_variation["factual_percentage"] = $factual_percentage;

                        $curr_variations[$product_variation->id] = $curr_variation;
                    }

                    $curr_variations = $this->arrayMultisort($curr_variations, "factual_percentage");

                    $productsWithVariationsCurr["variations"] = $curr_variations;

                    $min_factual_percentage = min(array_column($curr_variations, 'factual_percentage'));

                    $productsWithVariationsCurr["min_factual_percentage"] = $min_factual_percentage;

                    $productsWithVariations[] = $productsWithVariationsCurr;
                }

                $productsWithVariations = collect($productsWithVariations)->sortBy('min_factual_percentage');

                $productsWithVariations = $this->paginateCollection($productsWithVariations, 1);

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

            $productsGroupByCompany = NULL;

            $productsWithVariations = NULL;

            $request_results_count = 0;

            $purposeTypes = NULL;

            $percentage_types = NULL;

            $providing_types = NULL;

            $security_types = NULL;

            $repayment_types = NULL;

            $privileged_term_statuses = null;

            $special_project_statuses = null;
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

                "time_types" => $time_types,

                "time_type" => $time_type,

                "loan_term" => $loan_term,

                "loan_amount" => $loan_amount,

                "currency" => $currency,

                "errors" => $errors,

                "products" => $products,

                "productsGroupByCompany" => $productsGroupByCompany,

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

                $productsWithVariations = NULL;

                $request_results_count = 0;

                $percentage_types = NULL;

                $providing_types = NULL;

                $security_types = NULL;

                $repayment_types = NULL;

                $privileged_term_statuses = null;

                $special_project_statuses = null;
            } else {
                $products = ConsumerCredit::with('companyInfo')->where('status', 2)
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

                foreach ($products as $product) {

                    $productsWithVariationsCurr = [];

                    $productsWithVariationsCurr["id"] = $product->id;

                    $productsWithVariationsCurr["name"] = $product->name;

                    $productsWithVariationsCurr["company_id"] = $product->company_id;

                    $productsWithVariationsCurr["companyInfo"] = $product->companyInfo;

                    $curr_variations = [];

                    foreach ($product->variations as $product_variation) {

                        $curr_variation = [];

                        $factual_percentage = 100 / $product_variation->id; //really getting from calculate factual_percentage function

                        $curr_variation["id"] = $product_variation->id;

                        $curr_variation["providing_type"] = $product_variation->providing_type;

                        $curr_variation["percentage_type"] = $product_variation->percentage_type;

                        $curr_variation["percentage"] = $product_variation->percentage;

                        $curr_variation["repayment_type"] = $product_variation->repayment_type;

                        $curr_variation["repayment_loan_interval_type_id"] = $product_variation->repayment_loan_interval_type_id;

                        $curr_variation["repayment_percent_interval_type_id"] = $product_variation->repayment_percent_interval_type_id;

                        $curr_variation["factual_percentage"] = $factual_percentage;

                        $curr_variations[$product_variation->id] = $curr_variation;
                    }

                    $curr_variations = $this->arrayMultisort($curr_variations, "factual_percentage");

                    $productsWithVariationsCurr["variations"] = $curr_variations;

                    $min_factual_percentage = min(array_column($curr_variations, 'factual_percentage'));

                    $productsWithVariationsCurr["min_factual_percentage"] = $min_factual_percentage;

                    $productsWithVariations[] = $productsWithVariationsCurr;
                }

                $productsWithVariations = collect($productsWithVariations)->sortBy('min_factual_percentage');

                $productsWithVariations = $this->paginateCollection($productsWithVariations, 1);

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

            $productsGroupByCompany = NULL;

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

                "productsWithVariations" => $productsWithVariations,

                "request_results_count" => $request_results_count,

                "previousUrl" => $previousUrl,

                "percentage_types" => $percentage_types,

                "providing_types" => $providing_types,

                "security_types" => $security_types,

                "repayment_types" => $repayment_types,

                "special_project_statuses" => $special_project_statuses,

                "privileged_term_statuses" => $privileged_term_statuses,
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

                $productsWithVariations = NULL;

                $request_results_count = 0;

                $percentage_types = NULL;

                $providing_types = NULL;

                $security_types = NULL;

                $repayment_types = NULL;

                $privileged_term_statuses = null;

                $special_project_statuses = null;
            } else {
                $products = OnlineLoan::with('companyInfo')->where('status', 2)
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

                foreach ($products as $product) {

                    $productsWithVariationsCurr = [];

                    $productsWithVariationsCurr["id"] = $product->id;

                    $productsWithVariationsCurr["name"] = $product->name;

                    $productsWithVariationsCurr["company_id"] = $product->company_id;

                    $productsWithVariationsCurr["companyInfo"] = $product->companyInfo;

                    $curr_variations = [];

                    foreach ($product->variations as $product_variation) {

                        $curr_variation = [];

                        $factual_percentage = 100 / $product_variation->id; //really getting from calculate factual_percentage function

                        $curr_variation["id"] = $product_variation->id;

                        $curr_variation["providing_type"] = $product_variation->providing_type;

                        $curr_variation["percentage_type"] = $product_variation->percentage_type;

                        $curr_variation["percentage"] = $product_variation->percentage;

                        $curr_variation["repayment_type"] = $product_variation->repayment_type;

                        $curr_variation["repayment_loan_interval_type_id"] = $product_variation->repayment_loan_interval_type_id;

                        $curr_variation["repayment_percent_interval_type_id"] = $product_variation->repayment_percent_interval_type_id;

                        $curr_variation["factual_percentage"] = $factual_percentage;

                        $curr_variations[$product_variation->id] = $curr_variation;
                    }

                    $curr_variations = $this->arrayMultisort($curr_variations, "factual_percentage");

                    $productsWithVariationsCurr["variations"] = $curr_variations;

                    $min_factual_percentage = min(array_column($curr_variations, 'factual_percentage'));

                    $productsWithVariationsCurr["min_factual_percentage"] = $min_factual_percentage;

                    $productsWithVariations[] = $productsWithVariationsCurr;
                }

                $productsWithVariations = collect($productsWithVariations)->sortBy('min_factual_percentage');

                $productsWithVariations = $this->paginateCollection($productsWithVariations, 1);

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

            $productsGroupByCompany = NULL;

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

                "errors" => $errors,

                "products" => $products,

                "productsGroupByCompany" => $productsGroupByCompany,

                "productsWithVariations" => $productsWithVariations,

                "request_results_count" => $request_results_count,

                "previousUrl" => $previousUrl,

                "percentage_types" => $percentage_types,

                "providing_types" => $providing_types,

                "security_types" => $security_types,

                "repayment_types" => $repayment_types,

                "special_project_statuses" => $special_project_statuses,

                "privileged_term_statuses" => $privileged_term_statuses,
            ]);
    }

    /**
     * compare loan Refinancings.
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

                $productsWithVariations = NULL;

                $request_results_count = 0;

                $loanRefinancingPurposeTypes = NULL;

                $percentage_types = NULL;

                $providing_types = NULL;

                $security_types = NULL;

                $repayment_types = NULL;

                $privileged_term_statuses = null;

                $special_project_statuses = null;
            } else {
                $products = LoanRefinancing::with('companyInfo')->where('status', 2)
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

                foreach ($products as $product) {

                    $productsWithVariationsCurr = [];

                    $productsWithVariationsCurr["id"] = $product->id;

                    $productsWithVariationsCurr["name"] = $product->name;

                    $productsWithVariationsCurr["company_id"] = $product->company_id;

                    $productsWithVariationsCurr["companyInfo"] = $product->companyInfo;

                    $curr_variations = [];

                    foreach ($product->variations as $product_variation) {

                        $curr_variation = [];

                        $factual_percentage = 100 / $product_variation->id; //really getting from calculate factual_percentage function

                        $curr_variation["id"] = $product_variation->id;

                        $curr_variation["providing_type"] = $product_variation->providing_type;

                        $curr_variation["percentage_type"] = $product_variation->percentage_type;

                        $curr_variation["percentage"] = $product_variation->percentage;

                        $curr_variation["repayment_type"] = $product_variation->repayment_type;

                        $curr_variation["repayment_loan_interval_type_id"] = $product_variation->repayment_loan_interval_type_id;

                        $curr_variation["repayment_percent_interval_type_id"] = $product_variation->repayment_percent_interval_type_id;

                        $curr_variation["factual_percentage"] = $factual_percentage;

                        $curr_variations[$product_variation->id] = $curr_variation;
                    }

                    $curr_variations = $this->arrayMultisort($curr_variations, "factual_percentage");

                    $productsWithVariationsCurr["variations"] = $curr_variations;

                    $min_factual_percentage = min(array_column($curr_variations, 'factual_percentage'));

                    $productsWithVariationsCurr["min_factual_percentage"] = $min_factual_percentage;

                    $productsWithVariations[] = $productsWithVariationsCurr;
                }

                $productsWithVariations = collect($productsWithVariations)->sortBy('min_factual_percentage');

                $productsWithVariations = $this->paginateCollection($productsWithVariations, 1);

                $productsGroupByCompanyIds = array_unique($products->pluck('company_id')->toArray());

                $productsGroupByCompany = [];

                foreach ($productsGroupByCompanyIds as $productCompanyId) {
                    $productsGroupByCompany[] = $products->where('company_id', $productCompanyId);
                }

                $request_results_count = $products->sum('variations_count');//->count();

                $productsFiltersSingleCounts = $this->compareProductsGetSomeFilters($belonging_id, $products);

                $loanRefinancingPurposeTypes = $productsFiltersSingleCounts["loanRefinancingPurposeTypes"];

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

            $productsGroupByCompany = NULL;

            $productsWithVariations = NULL;

            $request_results_count = 0;

            $loanRefinancingPurposeTypes = NULL;

            $percentage_types = NULL;

            $providing_types = NULL;

            $security_types = NULL;

            $repayment_types = NULL;

            $privileged_term_statuses = null;

            $special_project_statuses = null;
        }

        $errors = $validator->errors();

        $previousUrl = $this->loansPreviousUrl($request);

        return view('compare.compareLoanRefinancings',
            [
                "belongings" => $belongings,

                "currProductByBelongingsView" => $currProductByBelongingsView,

                "currBelonging" => $currBelonging,

                "belonging_id" => $belonging_id,

                "loanCurrenciesTypes" => $loanCurrenciesTypes,

                "yes_no_answers" => $yes_no_answers,

                "repayment_loan_interval_types" => $repayment_loan_interval_types,

                "repayment_percent_interval_types" => $repayment_percent_interval_types,

                "time_types" => $time_types,

                "time_type" => $time_type,

                "loan_term" => $loan_term,

                "loan_amount" => $loan_amount,

                "currency" => $currency,

                "errors" => $errors,

                "products" => $products,

                "productsGroupByCompany" => $productsGroupByCompany,

                "productsWithVariations" => $productsWithVariations,

                "request_results_count" => $request_results_count,

                "previousUrl" => $previousUrl,

                "loanRefinancingPurposeTypes" => $loanRefinancingPurposeTypes,

                "percentage_types" => $percentage_types,

                "providing_types" => $providing_types,

                "security_types" => $security_types,

                "repayment_types" => $repayment_types,

                "special_project_statuses" => $special_project_statuses,

                "privileged_term_statuses" => $privileged_term_statuses,
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

                $percentage_types = NULL;

                $providing_types = NULL;

                $security_types = NULL;

                $repayment_types = NULL;
            } else {
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

                $productsFiltersSingleCounts = $this->compareProductsGetSomeFilters($belonging_id, $products);

                $percentage_types = $productsFiltersSingleCounts["percentage_types"];

                $providing_types = $productsFiltersSingleCounts["providing_types"];

                $security_types = $productsFiltersSingleCounts["security_types"];

                $repayment_types = $productsFiltersSingleCounts["repayment_types"];
            }
        } else {
            $validator = Validator::make($request->all(), []);

            $products = NULL;

            $productsGroupByCompany = NULL;

            $request_results_count = 0;

            $percentage_types = NULL;

            $providing_types = NULL;

            $security_types = NULL;

            $repayment_types = NULL;
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

                "percentage_types" => $percentage_types,

                "providing_types" => $providing_types,

                "security_types" => $security_types,

                "repayment_types" => $repayment_types,
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

                if (!is_null($transfer_amount)) {
                    $products->whereHas('moneyTransferAmountsTermsCommissionFee', function ($q) use ($transfer_amount) {
                        $q->where('money_transfer_amount_from', '<=', (float)$transfer_amount);

                        $q->where('money_transfer_amount_to', '>=', (float)$transfer_amount);
                    });
                }

                if (!is_null($country)) {
                    $products->whereHas('countriesInfo', function ($q) use ($country) {
                        $q->where('country_id', $country);
                    });
                }
                if (!is_null($currency)) {
                    $products->whereHas('currenciesInfo', function ($q) use ($currency) {
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

        $yes_no_answers = YesNo::all();

        $time_type = $request->time_type;

        $loan_term = $request->loan_term;

        $age = $request->age;

        $country = $request->country;

        $min_age = TravelInsurancesVariation::min('travel_age_from');

        $max_age = TravelInsurancesVariation::where('id', '>', '222')->max('travel_age_to');

        $min_age = intval($min_age);

        if (intval($max_age) == 0) {
            $max_age = 100;
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
                    //->with('variations')
                    ->with(['variations' => function ($q) use ($age, $loan_term_search_in_days) {
                        $q->where('travel_age_from', '<=', (int)$age);
                        $q->where('travel_age_to', '>=', (int)$age);

                        $q->where('travel_insurance_term_from', '<=', (int)$loan_term_search_in_days);
                        $q->where('travel_insurance_term_to', '>=', (int)$loan_term_search_in_days);
                    }])
                    ->with(['countriesInfo' => function ($q) use ($country) {
                        $q->where('country_id', $country);
                    }])
                    ->withCount('variations')
                    ->where('status', 2)
                    ->has('variations', '>', 0);

                if (!is_null($country)) {
                    $products->whereHas('countriesInfo', function ($q) use ($country) {
                        $q->where('country_id', $country);
                    });
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

                            $curr_variation["insurance_fee"] = $calcTravelInsuranceFee;

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

                $productsWithVariationsGroupByCompany = $this->paginateCollection($productsWithVariationsGroupByCompany, 1, 'page_by_company');


                $productsWithVariations = collect($productsWithVariations)->sortBy('insurance_fee');

                $productsWithVariations = $this->paginateCollection($productsWithVariations, 1);


                $productsFiltersSingleCounts = $this->compareProductsGetSomeFilters($belonging_id, $products);

                $non_recoverable_expenses_answers = $productsFiltersSingleCounts["non_recoverable_expenses_answers"];

                $term_inputs_quantities = $productsFiltersSingleCounts["term_inputs_quantities"];
            }
        }
        else {
            $validator = Validator::make($request->all(), []);

            $products = NULL;

            $productsWithVariations = NULL;

            $productsWithVariationsGroupByCompany = NULL;

            $request_results_count = 0;

            $non_recoverable_expenses_answers = NULL;

            $term_inputs_quantities = NULL;
        }

        $errors = $validator->errors();

        $getCompareInfo = $this->getCompareInfo();

        $previousUrl = $this->loansPreviousUrl($request);

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


    /**
     * car loan Product page
     *
     * @return \Illuminate\Http\Response
     */
    function carLoanProduct($unique_options, $car_cost, $prepayment, $time_type, $term, Request $request)
    {
        $belonging_id = 1;

        $belongings = Belonging::with('productsByBelongingInfo')->get();

        $product_variation = ProductsVariation::where(DB::raw("md5(unique_options)"), $unique_options)
            ->with('providingTypeInfo')->with('repaymentTypeInfo')->first();

        $product_id = $product_variation->product_id;

        $product = CarLoan::where('id', $product_id)->with('companyInfo')
            ->with('carInfo')
            ->with('securityTypes')
            ->with('carSalons')->first();

        $repayment_loan_interval_types = RepaymentLoanIntervalType::all();

        $repayment_percent_interval_types = RepaymentPercentIntervalType::all();

        $productPercentageTypesArr = ["1" => "+", "2" => "-", "3" => "±"];

        $loan_amount = $car_cost - $prepayment;

        if ($time_type == 1 || $time_type == "" || is_null($time_type)) {

            $loan_term_search_in_days = $term;
        } else if ($time_type == 2) {

            $loan_term_search_in_days = $term * 30;
        } else if ($time_type == 3) {

            $loan_term_search_in_days = $term * 365;
        }

        if ($car_cost > 0) {
            $prepayment_percent = 100 * $prepayment / $car_cost;
        } else {
            $prepayment_percent = null;
        }

        $getCalculation = $this->getCalculation($product, $product_variation, $car_cost, $loan_amount, $loan_term_search_in_days, $prepayment_percent, $time_type);//calculate factual_percentage and other

        $require_payments_schedule_annually_and_summary = [];

        $loan_application_fee = $getCalculation["other_fee"]["loan_application_fee"];

        $collateral_assessment_fee = $getCalculation["other_fee"]["collateral_assessment_fee"];

        $cash_service_fee = $getCalculation["other_fee"]["cash_service_fee"];

        $notary_validation_fee = $getCalculation["other_fee"]["notary_validation_fee"];

        $require_payments_schedule_annually_and_summary[] =
            ["name" => "Վարկային հայտի ուսումնասիրության վճար (միանվագ)", "anually" => $loan_application_fee, "summary" => $loan_application_fee];

        $require_payments_schedule_annually_and_summary[] =
            ["name" => "Գրավի գնահատման վճար (միանվագ)", "anually" => $collateral_assessment_fee, "summary" => $collateral_assessment_fee];

        $require_payments_schedule_annually_and_summary[] =
            ["name" => "Կանխիկացման վճար", "anually" => $cash_service_fee, "summary" => $cash_service_fee];

        $require_payments_schedule_annually_and_summary[] =
            ["name" => "Նոտարական վավերացման վճար (միանվագ)", "anually" => $notary_validation_fee, "summary" => $notary_validation_fee];

        $factual_percentage = 100 * $getCalculation["xirr"];

        $require_payments = $getCalculation["require_payments"];

        $sum_payments = $getCalculation["sum_payments"];

        $more_payment_amount = $sum_payments - $loan_amount;

        $getCompareInfo = $this->getCompareInfo();

        return view('product.carloan', [
            "belonging_id" => $belonging_id,

            "belongings" => $belongings,

            "car_cost" => $car_cost,

            "prepayment" => $prepayment,

            "time_type" => $time_type,

            "term" => $term,

            "loan_term_search_in_days" => $loan_term_search_in_days,

            "loan_amount" => $loan_amount,

            "product" => $product,

            "product_variation" => $product_variation,

            "getCalculation" => $getCalculation,

            "require_payments_schedule_annually_and_summary" => $require_payments_schedule_annually_and_summary,

            "factual_percentage" => $factual_percentage,

            "require_payments" => $require_payments,

            "sum_payments" => $sum_payments,

            "more_payment_amount" => $more_payment_amount,

            "repayment_loan_interval_types" => $repayment_loan_interval_types,

            "repayment_percent_interval_types" => $repayment_percent_interval_types,

            "productPercentageTypesArr" => $productPercentageTypesArr,

            "getCompareInfo" => $getCompareInfo,
        ]);
    }

}