<?php

namespace App\Traits;

use App\Models\AgricLoan;
use App\Models\Belonging;
use App\Models\CarLoan;
use App\Models\ConsumerCredit;
use App\Models\CreditLoan;
use App\Models\Deposit;
use App\Models\DocumentList;
use App\Models\GoldLoan;
use App\Models\LoanRefinancing;
use App\Models\MoneyTransfer;
use App\Models\Mortgage;
use App\Models\OnlineLoan;
use App\Models\PaymentCard;
use App\Models\ProductsVariation;
use App\Models\RepaymentLoanIntervalType;
use App\Models\RepaymentPercentIntervalType;
use App\Models\StudentLoan;
use App\Models\TravelInsurance;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use Illuminate\Database\Eloquent\Model;

trait ProductTrait
{

    /**
     * Get product searched results filters single counts
     *
     * @return \Illuminate\Http\Response
     */
    public
    function compareProductsVariationsGetSomeFilters($belonging_id, $products_ids_without_any_filter, $product_variations_ids_without_any_filter, $filter_transfer_data)
    {
        $products_variations = ProductsVariation::whereIn('id', $product_variations_ids_without_any_filter)->get();

        $data = [];

        $yes_no_answers = YesNo::all();

        $loan_belonging_ids_arr = [1, 2, 3, 4, 5, 6, 8, 11, 13];

        $percentage_types = PercentageType::all();

        $percentage_types_arr = [];

        $providing_types = ProvidingType::all();

        $providing_types_arr = [];

        $repayment_types = RepaymentType::all();

        $repayment_types_arr = [];

        $privileged_term_answers_arr = [];

        $providing_types_filter = $filter_transfer_data["providing_types"];

        $percentage_types_filter = $filter_transfer_data["percentage_types"];

        $repayment_types_filter = $filter_transfer_data["repayment_types"];

        $special_project_answers_filter = $filter_transfer_data["special_project_answers"];

        $privileged_term_answers_filter = $filter_transfer_data["privileged_term_answers"];


//        if (is_null($special_project_answers_filter)) {
//            $special_project_answers_filter = $yes_no_answers->pluck('id')->toArray();
//        }

        switch ($belonging_id) {
            case 1:

                break;
            case 2:
                $gold_assay_types = GoldAssayType::all();

                $gold_assay_types_arr = [];

                $gold_assay_types_filter = $filter_transfer_data["gold_assay_types"];

                if (is_null($gold_assay_types_filter)) {
                    $gold_assay_types_filter = $gold_assay_types->pluck('id')->toArray();
                }

                $products_without_filter_by_privileged_term_answers_filter_ids = GoldLoan::whereIn('id', $products_ids_without_any_filter)->whereHas('goldAssayTypes', function ($q) use ($gold_assay_types_filter) {
                    $q->whereIn('gold_assay_type_id', $gold_assay_types_filter);
                })->pluck('id')->toArray();

                if (!is_null($privileged_term_answers_filter) && count($privileged_term_answers_filter) == 1) {
                    if ($privileged_term_answers_filter[0] == 1) {

                        $products_without_filter_by_gold_assay_type_filter_ids = GoldLoan::whereIn('id', $products_ids_without_any_filter)->where('privileged_term', 1)->pluck('id')->toArray();
                    } else {
                        $products_without_filter_by_gold_assay_type_filter_ids = GoldLoan::whereIn('id', $products_ids_without_any_filter)->where('privileged_term', '!=', 1)->pluck('id')->toArray();
                    }
                } else {
                    $products_without_filter_by_gold_assay_type_filter_ids = $products_ids_without_any_filter;
                }

                $products_with_filter_by_gold_assay_type_and_privileged_term_answer_ids = GoldLoan::whereIn('id', $products_without_filter_by_gold_assay_type_filter_ids)
                    ->whereIn('id', $products_without_filter_by_privileged_term_answers_filter_ids)->pluck('id')->toArray();

                $products_variations_without_privileged_term_answer = ProductsVariation::whereIn('id', $product_variations_ids_without_any_filter)->whereIn('product_id', $products_without_filter_by_privileged_term_answers_filter_ids)
                    ->whereIn('providing_type', $providing_types_filter)->whereIn('repayment_type', $repayment_types_filter)->whereIn('percentage_type', $percentage_types_filter)->get();

                $products_variations_without_gold_assay_type = ProductsVariation::whereIn('id', $product_variations_ids_without_any_filter)->whereIn('product_id', $products_without_filter_by_gold_assay_type_filter_ids)
                    ->whereIn('providing_type', $providing_types_filter)->whereIn('repayment_type', $repayment_types_filter)->whereIn('percentage_type', $percentage_types_filter)->get();

                $products_variations_without_percentage_type = ProductsVariation::whereIn('id', $product_variations_ids_without_any_filter)->whereIn('product_id', $products_with_filter_by_gold_assay_type_and_privileged_term_answer_ids)
                    ->whereIn('providing_type', $providing_types_filter)->whereIn('repayment_type', $repayment_types_filter)->get();

                $products_variations_without_providing_type = ProductsVariation::whereIn('id', $product_variations_ids_without_any_filter)->whereIn('product_id', $products_with_filter_by_gold_assay_type_and_privileged_term_answer_ids)
                    ->whereIn('percentage_type', $percentage_types_filter)->whereIn('repayment_type', $repayment_types_filter)->get();

                $products_variations_without_repayment_type = ProductsVariation::whereIn('id', $product_variations_ids_without_any_filter)->whereIn('product_id', $products_with_filter_by_gold_assay_type_and_privileged_term_answer_ids)
                    ->whereIn('providing_type', $providing_types_filter)->whereIn('percentage_type', $percentage_types_filter)->get();


                foreach ($percentage_types as $percentage_type) {
                    if ($percentage_type->id != 1) {
                        $products_variations_without_percentage_type_query = $products_variations_without_percentage_type;

                        $count_percentage_type = $products_variations_without_percentage_type_query->whereIn('percentage_type', array($percentage_type->id))->count();

                        $percentage_types_arr[] = array(
                            "id" => $percentage_type->id,
                            "count" => $count_percentage_type
                        );
                    }
                }

                foreach ($providing_types as $providing_type) {
                    if ($providing_type->id != 1) {

                        $count_providing_type = $products_variations_without_providing_type->whereIn('providing_type', array($providing_type->id, 1))->count();

                        $providing_types_arr[] = array(
                            "id" => $providing_type->id,
                            "count" => $count_providing_type
                        );
                    }
                }

                foreach ($repayment_types as $repayment_type) {
                    if ($repayment_type->id != 1) {

                        $count_repayment_type = $products_variations_without_repayment_type->whereIn('repayment_type', array($repayment_type->id, 1))->count();

                        $repayment_types_arr[] = array(
                            "id" => $repayment_type->id,
                            "count" => $count_repayment_type
                        );
                    }
                }

                foreach ($gold_assay_types as $gold_assay_type) {

                    $filtered_by_gold_assay_type_product_ids = ProductsGoldAssayType::where('gold_assay_type_id', $gold_assay_type->id)
                        ->where('belonging_id', $belonging_id)
                        ->whereIn('product_id', $products_variations_without_gold_assay_type->pluck('product_id')->toArray())->pluck('product_id')->toArray();

                    $count_gold_assay_type = $products_variations_without_gold_assay_type->whereIn('product_id', $filtered_by_gold_assay_type_product_ids)->count();

                    $gold_assay_types_arr[] = array(
                        "id" => $gold_assay_type->id,
                        "count" => $count_gold_assay_type
                    );
                }

                foreach ($yes_no_answers as $yes_no_answer) {

                    if ($yes_no_answer->id == 1) {
                        $filtered_by_privileged_term_answer_product_ids = GoldLoan::select('id')->whereIn('id', $products_without_filter_by_privileged_term_answers_filter_ids)->where('privileged_term', 1)->pluck('id')->toArray();

                        $count_privileged_term_answer = $products_variations_without_privileged_term_answer->whereIn('product_id', $filtered_by_privileged_term_answer_product_ids)->count();
                    } else {
                        $filtered_by_privileged_term_answer_product_ids = GoldLoan::whereIn('id', $products_without_filter_by_privileged_term_answers_filter_ids)->whereNull('privileged_term')->pluck('id')->toArray();

                        $count_privileged_term_answer = $products_variations_without_privileged_term_answer->whereIn('product_id', $filtered_by_privileged_term_answer_product_ids)->count();
                    }


                    $privileged_term_answers_arr[] = array(
                        "id" => $yes_no_answer->id,
                        "count" => $count_privileged_term_answer
                    );
                }

                $data["privileged_term_answers"] = $privileged_term_answers_arr;

                $data["gold_assay_types"] = $gold_assay_types_arr;

                $data["repayment_types"] = $repayment_types_arr;

                $data["providing_types"] = $providing_types_arr;

                $data["percentage_types"] = $percentage_types_arr;

                break;
            case 5:
                break;
            case 6:
                break;
            case 3:
                break;
            case 4:
                break;
            case 10:
                break;
            case 11:
                break;
            case 7:
                break;
            case 8:
                break;
            case 9:
                break;
            case 12:
                break;
            case 13:

                break;
            default:
        }

//        $data["special_project_statuses"] = $special_project_statuses_arr;
//
//        $data["privileged_term_statuses"] = $privileged_term_statuses_arr;

        return $data;
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
     * compare Agric Loans.
     *
     * @return \Illuminate\Http\Response
     */
    public
    function compareAgricLoans(Request $request)
    {
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

                    $curr_variation["require_payments"] = $require_payments;

                    $curr_variation["sum_payments"] = $sum_payments;

                    /*curr variation unique_options*/
                    $unique_options = "bel_" . $belonging_id . "_prod_" . $product->id . "_prov_" . $product_variation->providing_type . "_perc_" .
                        $product_variation->percentage_type . "_rep_" . $product_variation->repayment_type . "_rep_loan_" .
                        intval($product_variation->repayment_loan_interval_type_id) . "_rep_perc_" . intval($product_variation->repayment_percent_interval_type_id);

                    $unique_options = md5($unique_options);

                    $curr_variation["unique_options"] = $unique_options;

                    $curr_variations[$product_variation->id] = $curr_variation;
                    /*curr variation unique_options*/


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


//                $productsWithVariations->transform(function ($item, $key) {
//
//                    $item->transform(function ($item_inner, $key) {
//                        $item_inner->insurance_fee = number_format($item_inner->insurance_fee, 0, ",", " ");
//
//                        return $item_inner;
//                    });
//
//                    return $item;
//                });


}