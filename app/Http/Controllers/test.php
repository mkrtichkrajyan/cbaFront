<?php

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

            foreach ($products as $product) {

                $productsWithVariationsCurr = [];

                $productsWithVariationsCurr["id"] = $product->id;

                $productsWithVariationsCurr["name"] = $product->name;

                $productsWithVariationsCurr["company_id"] = $product->company_id;

                $productsWithVariationsCurr["companyInfo"] = $product->companyInfo;

                $curr_variations = [];

                foreach ($product->variations as $product_variation) {

                    $curr_variation = [];

                    $getCalculation = $this->getCalculation($product, $product_variation, $car_cost, $loan_amount, $loan_term_search_in_days, $prepayment_percent);//calculate factual_percentage and other

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