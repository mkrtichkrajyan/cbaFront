<?php

namespace App\Traits;

use App\Models\AbsoluteAmountORPercentOnlyPayType;
use App\Models\AbsoluteAmountOrPercentPayType;
use App\Models\AgricLoan;
use App\Models\Belonging;
use App\Models\CarLoan;
use App\Models\ConsumerCredit;
use App\Models\Country;
use App\Models\CreditLoan;
use App\Models\Deposit;
use App\Models\DocumentList;
use App\Models\GoldAssayType;
use App\Models\GoldLoan;
use App\Models\LoanRefinancing;
use App\Models\LoanServicePayTypes;
use App\Models\MoneyTransfer;
use App\Models\Mortgage;
use App\Models\NonRecoverableExpensesAnswer;
use App\Models\OnlineLoan;
use App\Models\PaymentCard;
use App\Models\PercentageType;
use App\Models\PeriodicityType;
use App\Models\ProductByBelongingsView;
use App\Models\ProductsVariation;
use App\Models\ProvidingType;
use App\Models\RepaymentLoanIntervalType;
use App\Models\RepaymentPercentIntervalType;
use App\Models\RepaymentType;
use App\Models\SecurityType;
use App\Models\SpecialProject;
use App\Models\StudentLoan;
use App\Models\TimeType;
use App\Models\TravelInsurance;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use Illuminate\Database\Eloquent\Model;

trait ProductsFiltersTrait
{
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
            $time_type = 1;

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
            ->whereIn('status', [1, 2, 5]);


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

                $getCalculation = $this->getCalculation($product, $product_variation, $car_cost, $loan_amount, $loan_term_search, $loan_term_search_in_days, $prepayment_percent, $time_type_search);//calculate factual_percentage and other

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
            }

            $curr_variations = $this->arrayMultisort($curr_variations, "factual_percentage");

            $productsWithVariationsCurr["variations"] = $curr_variations;

            $min_factual_percentage = count($curr_variations) > 0 ? min(array_column($curr_variations, 'factual_percentage')) : null;

            $productsWithVariationsCurr["min_factual_percentage"] = $min_factual_percentage;

            $productsWithVariations[] = $productsWithVariationsCurr;
        }

        $productsWithVariations = collect($productsWithVariations)->sortBy('min_factual_percentage');

        $productsWithVariations = $this->paginateCollection($productsWithVariations, $this->per_page);

        $productsWithVariationsGroupByCompany = $this->arrayMultisort($productsWithVariationsGroupByCompany, "factual_percentage");

        $productsWithVariationsGroupByCompany = collect($productsWithVariationsGroupByCompany)->groupBy('company_id');

        $productsWithVariationsGroupByCompany = $this->paginateCollection($productsWithVariationsGroupByCompany, $this->per_page, 'page_by_company');


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
     * compare Credit Loans Filters.
     *
     * @return \Illuminate\Http\Response
     */
    public
    function creditLoanFilters(Request $request)
    {
        $belonging_id = 3;

        $purpose_types = $request->purpose_types;

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

        if ($time_type_search == 1 || $time_type_search == "" || is_null($time_type_search)) {
            $time_type = 1;

            $loan_term_search_in_days = $loan_term_search;
        } else if ($time_type_search == 2) {

            $loan_term_search_in_days = $loan_term_search * 30;
        } else if ($time_type_search == 3) {

            $loan_term_search_in_days = $loan_term_search * 365;
        }

        if ($cost > 0) {
            $prepayment_percent = 100 * $prepayment_final / $cost;
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

        $products = CreditLoan::with('companyInfo')->with(['variations' => function ($q) use ($providing_types, $percentage_types, $repayment_types) {
            $q->whereIn('providing_type', $providing_types);
            $q->whereIn('percentage_type', $percentage_types);
            $q->whereIn('repayment_type', $repayment_types);
        }])
            ->with('securityTypes')->withCount('variations')->has('variations', '>', 0)
            ->whereIn('status', [1, 2, 5]);


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

        if (is_array($purpose_types) && count($purpose_types) > 0) {
            $products->whereHas('ProductPurposes', function ($q) use ($purpose_types) {
                $q->whereIn('purpose_type', $purpose_types);
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

                $getCalculation = $this->getCalculation($product, $product_variation, $cost, $loan_amount, $loan_term_search, $loan_term_search_in_days, $prepayment_percent, $time_type_search);//calculate factual_percentage and other

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
            }

            $curr_variations = $this->arrayMultisort($curr_variations, "factual_percentage");

            $productsWithVariationsCurr["variations"] = $curr_variations;

            $min_factual_percentage = count($curr_variations) > 0 ? min(array_column($curr_variations, 'factual_percentage')) : null;

            $productsWithVariationsCurr["min_factual_percentage"] = $min_factual_percentage;

            $productsWithVariations[] = $productsWithVariationsCurr;
        }

        $productsWithVariations = collect($productsWithVariations)->sortBy('min_factual_percentage');

        $productsWithVariations = $this->paginateCollection($productsWithVariations, $this->per_page);

        $productsWithVariationsGroupByCompany = $this->arrayMultisort($productsWithVariationsGroupByCompany, "factual_percentage");

        $productsWithVariationsGroupByCompany = collect($productsWithVariationsGroupByCompany)->groupBy('company_id');

        $productsWithVariationsGroupByCompany = $this->paginateCollection($productsWithVariationsGroupByCompany, $this->per_page, 'page_by_company');


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
     * compare Mortgage Filters.
     *
     * @return \Illuminate\Http\Response
     */
    public
    function mortgageLoanFilters(Request $request)
    {
        $belonging_id = 8;

        $purpose_types = $request->purpose_types;

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

        $cost = $request->cost;

        $prepayment = $request->prepayment;

        $currency = $request->currency;

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

        $loan_amount_converted = $this->getLoanAmountConverted($currency, $loan_amount);

        if ($time_type_search == 1 || $time_type_search == "" || is_null($time_type_search)) {
            $time_type = 1;

            $loan_term_search_in_days = $loan_term_search;
        } else if ($time_type_search == 2) {

            $loan_term_search_in_days = $loan_term_search * 30;
        } else if ($time_type_search == 3) {

            $loan_term_search_in_days = $loan_term_search * 365;
        }

        if ($cost > 0) {
            $prepayment_percent = 100 * $prepayment_final / $cost;
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

        $products = Mortgage::with('companyInfo')->with(['variations' => function ($q) use ($providing_types, $percentage_types, $repayment_types) {
            $q->whereIn('providing_type', $providing_types);
            $q->whereIn('percentage_type', $percentage_types);
            $q->whereIn('repayment_type', $repayment_types);
        }])
            ->with('securityTypes')->withCount('variations')->has('variations', '>', 0)
            ->whereIn('status', [1, 2, 5]);


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
//
//        if (is_array($purpose_types) && count($purpose_types) > 0) {
//            $products->whereHas('ProductPurposes', function ($q) use ($purpose_types) {
//                $q->whereIn('purpose_type', $purpose_types);
//            });
//        }

        if (!is_null($currency)) {
            $products->where(function ($query) use ($currency) {
                $query->where('currency', (int)$currency);
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

                $getCalculation = $this->getCalculation($product, $product_variation, $cost, $loan_amount_converted, $loan_term_search, $loan_term_search_in_days, $prepayment_percent, $time_type_search);//calculate factual_percentage and other

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
            }

            $curr_variations = $this->arrayMultisort($curr_variations, "factual_percentage");

            $productsWithVariationsCurr["variations"] = $curr_variations;

            $min_factual_percentage = count($curr_variations) > 0 ? min(array_column($curr_variations, 'factual_percentage')) : null;

            $productsWithVariationsCurr["min_factual_percentage"] = $min_factual_percentage;

            $productsWithVariations[] = $productsWithVariationsCurr;
        }

        $productsWithVariations = collect($productsWithVariations)->sortBy('min_factual_percentage');

        $productsWithVariations = $this->paginateCollection($productsWithVariations, $this->per_page);

        $productsWithVariationsGroupByCompany = $this->arrayMultisort($productsWithVariationsGroupByCompany, "factual_percentage");

        $productsWithVariationsGroupByCompany = collect($productsWithVariationsGroupByCompany)->groupBy('company_id');

        $productsWithVariationsGroupByCompany = $this->paginateCollection($productsWithVariationsGroupByCompany, $this->per_page, 'page_by_company');


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
     * compare Gold Loans Filters.
     *
     * @return \Illuminate\Http\Response
     */
    public
    function goldLoansFilters(Request $request)
    {
        $belonging_id = 2;

        $gold_assay_types = $request->gold_assay_types;

        $percentage_types = $request->percentage_types;

        $providing_types = $request->providing_types;

        $special_project_answers = $request->special_project_answers;

        $privileged_term_answers = $request->privileged_term_answers;

        $repayment_types = $request->repayment_types;

        $repayment_loan_interval_type = $request->repayment_loan_interval_type;

        $repayment_percent_interval_type = $request->repayment_percent_interval_type;

        $loan_term_search = $request->loan_term_search;

        $time_type_search = $request->time_type_search;

        $loan_amount = intval($request->loan_amount);

        if ($time_type_search == 1 || $time_type_search == "" || is_null($time_type_search)) {
            $time_type = 1;

            $loan_term_search_in_days = $loan_term_search;
        } else if ($time_type_search == 2) {

            $loan_term_search_in_days = $loan_term_search * 30;
        } else if ($time_type_search == 3) {

            $loan_term_search_in_days = $loan_term_search * 365;
        }

        $prepayment_percent = 0;

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

        $products = GoldLoan::with('companyInfo')->with('goldAssayTypes')
            ->withCount('variations')->has('variations', '>', 0)
            ->whereIn('status', [1, 2, 5]);

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

        $product_variations_ids_without_any_filter = $products->get()->pluck('variations')->flatten()->pluck('id')->toArray();

        $products_ids_without_any_filter = $products->pluck('id');

        $products->with(['variations' => function ($q) use ($providing_types, $percentage_types, $repayment_types) {
            $q->whereIn('providing_type', $providing_types);
            $q->whereIn('percentage_type', $percentage_types);
            $q->whereIn('repayment_type', $repayment_types);
        }]);

        if (is_array($gold_assay_types) && count($gold_assay_types) > 0) {
            $products->whereHas('goldAssayTypes', function ($q) use ($gold_assay_types) {
                $q->whereIn('gold_assay_type_id', $gold_assay_types);
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

            if (intval($product->loan_pledge_ratio) == 0) {
                $cost = 0;
            } else {
                $cost = 100 * $loan_amount / $product->loan_pledge_ratio;
            }

            foreach ($product->variations as $product_variation) {

                $curr_variation = [];

                $getCalculation = $this->getCalculation($product, $product_variation, $cost, $loan_amount, $loan_term_search, $loan_term_search_in_days, $prepayment_percent, $time_type_search);//calculate factual_percentage and other

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
            }

            $curr_variations = $this->arrayMultisort($curr_variations, "factual_percentage");

            $productsWithVariationsCurr["variations"] = $curr_variations;

//            $min_factual_percentage = count($curr_variations) > 0 ? min(array_column($curr_variations, 'factual_percentage')) : null;
            if (is_array(array_column($curr_variations, 'factual_percentage')) && count(array_column($curr_variations, 'factual_percentage')) > 0) {
                $min_factual_percentage = min(array_column($curr_variations, 'factual_percentage'));

                $productsWithVariationsCurr["min_factual_percentage"] = $min_factual_percentage;

                $productsWithVariations[] = $productsWithVariationsCurr;
            }
        }

        $productsWithVariations = collect($productsWithVariations)->sortBy('min_factual_percentage');

//        $products_variations_ids = array_column($productsWithVariationsGroupByCompany, 'id');

        /*Filtered Filters Single Counts*/
        $filter_transfer_data = ["gold_assay_types" => $gold_assay_types, "providing_types" => $providing_types, "percentage_types" => $percentage_types, "repayment_types" => $repayment_types,
            "special_project_answers" => $special_project_answers, "privileged_term_answers" => $privileged_term_answers];

        $productsFilteredFiltersSingleCounts = $this->compareProductsVariationsGetSomeFilters($belonging_id, $products_ids_without_any_filter, $product_variations_ids_without_any_filter, $filter_transfer_data);
        /*Filtered Filters Single Counts*/

        $productsWithVariations = $this->paginateCollection($productsWithVariations, $this->per_page);

        $productsWithVariationsGroupByCompany = $this->arrayMultisort($productsWithVariationsGroupByCompany, "factual_percentage");

        $productsWithVariationsGroupByCompany = collect($productsWithVariationsGroupByCompany)->groupBy('company_id');

        $productsWithVariationsGroupByCompany = $this->paginateCollection($productsWithVariationsGroupByCompany, $this->per_page, 'page_by_company');


        $links = (String)$productsWithVariations->appends([])->links('pagination::bootstrap-4');

        $links_grouped_by_company = (String)$productsWithVariationsGroupByCompany->appends([])->links('pagination::bootstrap-4');

        $getCompareInfo = $this->getCompareInfo();

        $checked_variations = $getCompareInfo[$belonging_id]["checked_variations"];
//dd($productsWithVariationsGroupByCompany);
        return response()->json(
            [
                "test" => [6 => "a", 1 => "b", 9 => "c", 7 => "e"],
                "belonging_id" => $belonging_id,
                "request_results_count" => $request_results_count,
                "productsWithVariations" => $productsWithVariations,
                "productsWithVariationsGroupByCompany" => $productsWithVariationsGroupByCompany,
                "productsFilteredFiltersSingleCounts" => $productsFilteredFiltersSingleCounts,
                'links' => $links,
                'links_grouped_by_company' => $links_grouped_by_company,
                'checked_variations' => $checked_variations
            ]
        );
    }

    /**
     * compare Student Loans Filters.
     *
     * @return \Illuminate\Http\Response
     */
    public
    function studentLoansFilters(Request $request)
    {
        $belonging_id = 4;

        $percentage_types = $request->percentage_types;

        $providing_types = $request->providing_types;

        $special_project_answers = $request->special_project_answers;

        $privileged_term_answers = $request->privileged_term_answers;

        $repayment_types = $request->repayment_types;

        $repayment_loan_interval_type = $request->repayment_loan_interval_type;

        $repayment_percent_interval_type = $request->repayment_percent_interval_type;

        $loan_term_search = $request->loan_term_search;

        $time_type_search = $request->time_type_search;

        $loan_amount = intval($request->loan_amount);

        if ($time_type_search == 1 || $time_type_search == "" || is_null($time_type_search)) {
            $time_type = 1;

            $loan_term_search_in_days = $loan_term_search;
        } else if ($time_type_search == 2) {

            $loan_term_search_in_days = $loan_term_search * 30;
        } else if ($time_type_search == 3) {

            $loan_term_search_in_days = $loan_term_search * 365;
        }

        $prepayment_percent = 0;

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

        $security_types = $request->security_types;

        $products = StudentLoan::with('companyInfo')->with(['variations' => function ($q) use ($providing_types, $percentage_types, $repayment_types) {
            $q->whereIn('providing_type', $providing_types);
            $q->whereIn('percentage_type', $percentage_types);
            $q->whereIn('repayment_type', $repayment_types);
        }])
            ->withCount('variations')->has('variations', '>', 0)
            ->whereIn('status', [1, 2, 5]);

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

                    $getCalculation = $this->getCalculation($product, $product_variation, $cost, $loan_amount, $loan_term_search, $loan_term_search_in_days, 0, $time_type_search);//calculate factual_percentage and other

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

                        /*curr variation unique_options*/
                        $unique_options = "bel_" . $belonging_id . "_prod_" . $product->id . "_prov_" . $product_variation->providing_type . "_perc_" .
                            $product_variation->percentage_type . "_rep_" . $product_variation->repayment_type . "_rep_loan_" .
                            intval($product_variation->repayment_loan_interval_type_id) . "_rep_perc_" . intval($product_variation->repayment_percent_interval_type_id);

                        $unique_options = md5($unique_options);

                        $curr_variation["unique_options"] = $unique_options;

                        $curr_variations[$product_variation->id] = $curr_variation;
                        /*curr variation unique_options*/

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

        $request_results_count = $this->arrayMultiCountColumn($productsWithVariations, 'variations'); //  $products->sum('variations_count');

        $productsWithVariations = collect($productsWithVariations)->sortBy('min_factual_percentage');

        $productsWithVariations = $this->paginateCollection($productsWithVariations, $this->per_page);

        $productsWithVariationsGroupByCompany = $this->arrayMultisort($productsWithVariationsGroupByCompany, "factual_percentage");

        $productsWithVariationsGroupByCompany = collect($productsWithVariationsGroupByCompany)->groupBy('company_id');

        $productsWithVariationsGroupByCompany = $this->paginateCollection($productsWithVariationsGroupByCompany, $this->per_page, 'page_by_company');


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
     * compare Agric Loans Filters.
     *
     * @return \Illuminate\Http\Response
     */
    public
    function agricLoansFilters(Request $request)
    {
        $belonging_id = 5;

        $purpose_types = $request->purpose_types;

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

        $loan_amount = intval($request->loan_amount);

        $currency = $request->currency;

        $loan_amount_converted = $this->getLoanAmountConverted($currency, $loan_amount);

        if ($time_type_search == 1 || $time_type_search == "" || is_null($time_type_search)) {
            $time_type = 1;

            $loan_term_search_in_days = $loan_term_search;
        } else if ($time_type_search == 2) {

            $loan_term_search_in_days = $loan_term_search * 30;
        } else if ($time_type_search == 3) {

            $loan_term_search_in_days = $loan_term_search * 365;
        }

        $prepayment_percent = 0;

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

        $products = AgricLoan::with('companyInfo')->with(['variations' => function ($q) use ($providing_types, $percentage_types, $repayment_types) {
            $q->whereIn('providing_type', $providing_types);
            $q->whereIn('percentage_type', $percentage_types);
            $q->whereIn('repayment_type', $repayment_types);
        }])
            ->withCount('variations')->has('variations', '>', 0)
            ->whereIn('status', [1, 2, 5]);

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

        if (is_array($purpose_types) && count($purpose_types) > 0) {
            $products->whereHas('purposesInfo', function ($q) use ($purpose_types) {
                $q->whereIn('purpose_type', $purpose_types);
            });
        }

        if (!is_null($currency)) {
            $products->where(function ($query) use ($currency) {
                $query->where('currency', (int)$currency);
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

                    $getCalculation = $this->getCalculation($product, $product_variation, $cost, $loan_amount_converted, $loan_term_search, $loan_term_search_in_days, $prepayment_percent, $time_type_search);//calculate factual_percentage and other

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
                }

                $curr_variations = $this->arrayMultisort($curr_variations, "factual_percentage");

                $productsWithVariationsCurr["variations"] = $curr_variations;

                $min_factual_percentage = count($curr_variations) > 0 ? min(array_column($curr_variations, 'factual_percentage')) : null;

                $productsWithVariationsCurr["min_factual_percentage"] = $min_factual_percentage;

                $productsWithVariations[] = $productsWithVariationsCurr;
            }
        }

        $request_results_count = $this->arrayMultiCountColumn($productsWithVariations, 'variations'); //  $products->sum('variations_count');

        $productsWithVariations = collect($productsWithVariations)->sortBy('min_factual_percentage');

        $productsWithVariations = $this->paginateCollection($productsWithVariations, $this->per_page);

        $productsWithVariationsGroupByCompany = $this->arrayMultisort($productsWithVariationsGroupByCompany, "factual_percentage");

        $productsWithVariationsGroupByCompany = collect($productsWithVariationsGroupByCompany)->groupBy('company_id');

        $productsWithVariationsGroupByCompany = $this->paginateCollection($productsWithVariationsGroupByCompany, $this->per_page, 'page_by_company');


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
     * compare Online Loans Filters.
     *
     * @return \Illuminate\Http\Response
     */
    public
    function onlineLoansFilters(Request $request)
    {
        $belonging_id = 13;

        $percentage_types = $request->percentage_types;

        $providing_types = $request->providing_types;

        $special_project_answers = $request->special_project_answers;

        $privileged_term_answers = $request->privileged_term_answers;

        $repayment_types = $request->repayment_types;

        $repayment_loan_interval_type = $request->repayment_loan_interval_type;

        $repayment_percent_interval_type = $request->repayment_percent_interval_type;

        $loan_term_search = $request->loan_term_search;

        $time_type_search = $request->time_type_search;

        $loan_amount = intval($request->loan_amount);

        if ($time_type_search == 1 || $time_type_search == "" || is_null($time_type_search)) {
            $time_type = 1;

            $loan_term_search_in_days = $loan_term_search;
        } else if ($time_type_search == 2) {

            $loan_term_search_in_days = $loan_term_search * 30;
        } else if ($time_type_search == 3) {

            $loan_term_search_in_days = $loan_term_search * 365;
        }

        $prepayment_percent = 0;

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

        $security_types = $request->security_types;

        $products = OnlineLoan::with('companyInfo')->with(['variations' => function ($q) use ($providing_types, $percentage_types, $repayment_types) {
            $q->whereIn('providing_type', $providing_types);
            $q->whereIn('percentage_type', $percentage_types);
            $q->whereIn('repayment_type', $repayment_types);
        }])
            ->withCount('variations')->has('variations', '>', 0)
            ->whereIn('status', [1, 2, 5]);

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

            if (intval($product->loan_pledge_ratio) == 0) {
                $cost = 0;
            } else {
                $cost = 100 * $loan_amount / $product->loan_pledge_ratio;
            }

            foreach ($product->variations as $product_variation) {

                $curr_variation = [];

                $getCalculation = $this->getCalculation($product, $product_variation, $cost, $loan_amount, $loan_term_search, $loan_term_search_in_days, $prepayment_percent, $time_type_search);//calculate factual_percentage and other

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
            }

            $curr_variations = $this->arrayMultisort($curr_variations, "factual_percentage");

            $productsWithVariationsCurr["variations"] = $curr_variations;

            $min_factual_percentage = count($curr_variations) > 0 ? min(array_column($curr_variations, 'factual_percentage')) : null;

            $productsWithVariationsCurr["min_factual_percentage"] = $min_factual_percentage;

            $productsWithVariations[] = $productsWithVariationsCurr;
        }

        $productsWithVariations = collect($productsWithVariations)->sortBy('min_factual_percentage');

        $productsWithVariations = $this->paginateCollection($productsWithVariations, $this->per_page);

        $productsWithVariationsGroupByCompany = $this->arrayMultisort($productsWithVariationsGroupByCompany, "factual_percentage");

        $productsWithVariationsGroupByCompany = collect($productsWithVariationsGroupByCompany)->groupBy('company_id');

        $productsWithVariationsGroupByCompany = $this->paginateCollection($productsWithVariationsGroupByCompany, $this->per_page, 'page_by_company');


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
     * compare Travel Insurances Filters.
     *
     * @return \Illuminate\Http\Response
     */
    public
    function travelInsurancesFilters(Request $request)
    {
        $belonging_id = 12;

        $countries_whole_world_id = 246;

        $countries_schengen_concret_id = 247;

        $countries_schengen_ids_arr = Country::where('is_schengen', 1)->pluck('id')->toArray();

        $non_recoverable_expenses = $request->non_recoverable_expenses;

        $term_inputs_quantities = $request->term_inputs_quantities;

        $term_search = $request->term_search;

        $age_search = $request->age_search;

        $age_search = $request->age_search;

        $country_search = intval($request->country_search);

        if (is_null($non_recoverable_expenses)) {
            $non_recoverable_expenses = NonRecoverableExpensesAnswer::pluck('id')->toArray();
        } else {
            $non_recoverable_expenses = array_merge($non_recoverable_expenses, array(1));
        }

        if (is_null($term_inputs_quantities)) {
            $term_inputs_quantities = [1, 2];
        }

        $products = TravelInsurance::with('companyInfo')
            ->with(['countriesInfo' => function ($q) use ($country_search) {
                $q->where('country_id', $country_search);
            }])
            ->withCount('variations')
            ->where('status', 2);
//                    ->has('variations', '>', 0)


        if (!is_null($country_search)) {
            if (!in_array($country_search, $countries_schengen_ids_arr)) {
                $products->whereHas('countriesInfo', function ($q) use ($country_search, $countries_whole_world_id) {
                    $q->where('country_id', $country_search)
                        ->orWhere(function ($query) use ($countries_whole_world_id) {
                            $query->where('country_id', $countries_whole_world_id);
                        });
                });
            } else {
                $products->whereHas('countriesInfo', function ($q) use ($country_search, $countries_whole_world_id, $countries_schengen_concret_id) {
                    $q->where('country_id', $country_search)
                        ->orWhere(function ($query) use ($countries_whole_world_id) {
                            $query->where('country_id', $countries_whole_world_id);
                        })->orWhere(function ($query) use ($countries_schengen_concret_id) {
                            $query->where('country_id', $countries_schengen_concret_id);
                        });
                });
            }
        }

        $products->with(['variations' => function ($q) use ($age_search, $term_search) {
            $q->where('travel_age_from', '<=', (int)$age_search);
            $q->where('travel_age_to', '>=', (int)$age_search);

            $q->where('travel_insurance_term_from', '<=', (int)$term_search);
            $q->where('travel_insurance_term_to', '>=', (int)$term_search);
        }]);


        $product_variations_ids_without_any_filter = $products->get()->pluck('variations')->flatten()->pluck('id')->toArray();

        $products_ids_without_any_filter = $products->pluck('id');

        if (is_array($non_recoverable_expenses) && count($non_recoverable_expenses) > 0) {
            $products = $products->whereIn('non_recoverable_amount', $non_recoverable_expenses);
        }

        if (is_array($term_inputs_quantities) && count($term_inputs_quantities) == 1) {
            if ($term_inputs_quantities[0] == 1) {
                $products->whereHas('variations', function ($q) use ($term_inputs_quantities) {
                    $q->where('term_inputs_quantity', $term_inputs_quantities[0]);
                });
            } else {
                $products->whereHas('variations', function ($q) use ($term_inputs_quantities) {
                    $q->where('term_inputs_quantity', '>', 1);
                });
            }
        }

//
//        if (is_array($term_inputs_quantities) && count($term_inputs_quantities) > 0) {
//            $products = $products->whereIn('non_recoverable_amount', $non_recoverable_expenses);
//        }

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

                    $curr_variations[$product_variation->id] = $curr_variation;


                    $productsWithVariationsGroupByCompanyCurr = $curr_variation;

                    $productsWithVariationsGroupByCompanyCurr["name"] = $product->name;

                    $productsWithVariationsGroupByCompanyCurr["company_id"] = $product->company_id;

                    $productsWithVariationsGroupByCompanyCurr["companyInfo"] = $product->companyInfo;

                    $productsWithVariationsGroupByCompany[] = $productsWithVariationsGroupByCompanyCurr;
                }

                $curr_variations = $this->arrayMultisort($curr_variations, "insurance_fee");

                $productsWithVariationsCurr["variations"] = $curr_variations;

                if (is_array(array_column($curr_variations, 'insurance_fee')) && count(array_column($curr_variations, 'insurance_fee')) > 0) {
                    $min_insurance_fee = min(array_column($curr_variations, 'insurance_fee'));

                    $productsWithVariationsCurr["min_insurance_fee"] = $min_insurance_fee;

                    $productsWithVariations[] = $productsWithVariationsCurr;
                }
            }
        }

        $productsWithVariations = collect($productsWithVariations)->sortBy('min_insurance_fee');

        $productsWithVariations = $this->paginateCollection($productsWithVariations, $this->per_page);


        $productsWithVariationsGroupByCompany = $this->arrayMultisort($productsWithVariationsGroupByCompany, "insurance_fee");

        $productsWithVariationsGroupByCompany = collect($productsWithVariationsGroupByCompany)->groupBy('company_id');

        $productsWithVariationsGroupByCompany = $this->paginateCollection($productsWithVariationsGroupByCompany, $this->per_page, 'page_by_company');


        /*Filtered Filters Single Counts*/
        $filter_transfer_data = ["non_recoverable_expenses" => $non_recoverable_expenses, "term_inputs_quantities" => $term_inputs_quantities];

        $productsFilteredFiltersSingleCounts = $this->compareProductsVariationsGetSomeFilters($belonging_id, $products_ids_without_any_filter, $product_variations_ids_without_any_filter, $filter_transfer_data);
        /*Filtered Filters Single Counts*/


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
                "productsFilteredFiltersSingleCounts" => $productsFilteredFiltersSingleCounts,
                'links' => $links,
                'links_grouped_by_company' => $links_grouped_by_company,
                'checked_variations' => $checked_variations
            ]
        );
    }

    /**
     * compare Consumer Loans Filters.
     *
     * @return \Illuminate\Http\Response
     */
    public
    function consumerLoanFilters(Request $request)
    {
        $belonging_id = 6;

        $percentage_types = $request->percentage_types;

        $providing_types = $request->providing_types;

        $special_project_answers = $request->special_project_answers;

        $privileged_term_answers = $request->privileged_term_answers;

        $repayment_types = $request->repayment_types;

        $repayment_loan_interval_type = $request->repayment_loan_interval_type;

        $repayment_percent_interval_type = $request->repayment_percent_interval_type;

        $loan_term_search = $request->loan_term_search;

        $time_type_search = $request->time_type_search;

        $loan_amount = intval($request->loan_amount);

        if ($time_type_search == 1 || $time_type_search == "" || is_null($time_type_search)) {
            $time_type = 1;

            $loan_term_search_in_days = $loan_term_search;
        } else if ($time_type_search == 2) {

            $loan_term_search_in_days = $loan_term_search * 30;
        } else if ($time_type_search == 3) {

            $loan_term_search_in_days = $loan_term_search * 365;
        }

        $prepayment_percent = 0;

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

        $security_types = $request->security_types;

        $products = ConsumerCredit::with('companyInfo')->with(['variations' => function ($q) use ($providing_types, $percentage_types, $repayment_types) {
            $q->whereIn('providing_type', $providing_types);
            $q->whereIn('percentage_type', $percentage_types);
            $q->whereIn('repayment_type', $repayment_types);
        }])
            ->withCount('variations')->has('variations', '>', 0)
            ->whereIn('status', [1, 2, 5]);

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

                    $getCalculation = $this->getCalculation($product, $product_variation, $cost, $loan_amount, $loan_term_search, $loan_term_search_in_days, 0, $time_type_search);//calculate factual_percentage and other

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

                        /*curr variation unique_options*/
                        $unique_options = "bel_" . $belonging_id . "_prod_" . $product->id . "_prov_" . $product_variation->providing_type . "_perc_" .
                            $product_variation->percentage_type . "_rep_" . $product_variation->repayment_type . "_rep_loan_" .
                            intval($product_variation->repayment_loan_interval_type_id) . "_rep_perc_" . intval($product_variation->repayment_percent_interval_type_id);

                        $unique_options = md5($unique_options);

                        $curr_variation["unique_options"] = $unique_options;

                        $curr_variations[$product_variation->id] = $curr_variation;
                        /*curr variation unique_options*/

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

                $min_factual_percentage = count($curr_variations) > 0 ? min(array_column($curr_variations, 'factual_percentage')) : null;

                $productsWithVariationsCurr["min_factual_percentage"] = $min_factual_percentage;

                $productsWithVariations[] = $productsWithVariationsCurr;

            }
        }

        $request_results_count = $this->arrayMultiCountColumn($productsWithVariations, 'variations'); //  $products->sum('variations_count');

        $productsWithVariations = collect($productsWithVariations)->sortBy('min_factual_percentage');

        $productsWithVariations = $this->paginateCollection($productsWithVariations, $this->per_page);

        $productsWithVariationsGroupByCompany = $this->arrayMultisort($productsWithVariationsGroupByCompany, "factual_percentage");

        $productsWithVariationsGroupByCompany = collect($productsWithVariationsGroupByCompany)->groupBy('company_id');

        $productsWithVariationsGroupByCompany = $this->paginateCollection($productsWithVariationsGroupByCompany, $this->per_page, 'page_by_company');


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
     * compare Loan Refinancings Filters.
     *
     * @return \Illuminate\Http\Response
     */
    public
    function loanRefinancingFilters(Request $request)
    {
        $belonging_id = 11;

        $percentage_types = $request->percentage_types;

        $providing_types = $request->providing_types;

        $special_project_answers = $request->special_project_answers;

        $privileged_term_answers = $request->privileged_term_answers;

        $repayment_types = $request->repayment_types;

        $repayment_loan_interval_type = $request->repayment_loan_interval_type;

        $repayment_percent_interval_type = $request->repayment_percent_interval_type;

        $loan_term_search = $request->loan_term_search;

        $time_type_search = $request->time_type_search;

        $loan_amount = intval($request->loan_amount);

        if ($time_type_search == 1 || $time_type_search == "" || is_null($time_type_search)) {
            $time_type = 1;

            $loan_term_search_in_days = $loan_term_search;
        } else if ($time_type_search == 2) {

            $loan_term_search_in_days = $loan_term_search * 30;
        } else if ($time_type_search == 3) {

            $loan_term_search_in_days = $loan_term_search * 365;
        }

        $prepayment_percent = 0;

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

        $security_types = $request->security_types;

        $products = LoanRefinancing::with('companyInfo')->with(['variations' => function ($q) use ($providing_types, $percentage_types, $repayment_types) {
            $q->whereIn('providing_type', $providing_types);
            $q->whereIn('percentage_type', $percentage_types);
            $q->whereIn('repayment_type', $repayment_types);
        }])
            ->withCount('variations')->has('variations', '>', 0)
            ->whereIn('status', [1, 2, 5]);

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

            if (intval($product->loan_pledge_ratio) == 0) {
                $cost = 0;
            } else {
                $cost = 100 * $loan_amount / $product->loan_pledge_ratio;
            }

            foreach ($product->variations as $product_variation) {

                $curr_variation = [];

                $getCalculation = $this->getCalculation($product, $product_variation, $cost, $loan_amount, $loan_term_search, $loan_term_search_in_days, $prepayment_percent, $time_type_search);//calculate factual_percentage and other

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
            }

            $curr_variations = $this->arrayMultisort($curr_variations, "factual_percentage");

            $productsWithVariationsCurr["variations"] = $curr_variations;

            $min_factual_percentage = count($curr_variations) > 0 ? min(array_column($curr_variations, 'factual_percentage')) : null;

            $productsWithVariationsCurr["min_factual_percentage"] = $min_factual_percentage;

            $productsWithVariations[] = $productsWithVariationsCurr;
        }

        $productsWithVariations = collect($productsWithVariations)->sortBy('min_factual_percentage');

        $productsWithVariations = $this->paginateCollection($productsWithVariations, $this->per_page);

        $productsWithVariationsGroupByCompany = $this->arrayMultisort($productsWithVariationsGroupByCompany, "factual_percentage");

        $productsWithVariationsGroupByCompany = collect($productsWithVariationsGroupByCompany)->groupBy('company_id');

        $productsWithVariationsGroupByCompany = $this->paginateCollection($productsWithVariationsGroupByCompany, $this->per_page, 'page_by_company');


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

}