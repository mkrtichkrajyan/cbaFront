<?php

namespace App\Traits;

use App\Models\AgricLoan;
use App\Models\Belonging;
use App\Models\CarLoan;
use App\Models\ConsumerCredit;
use App\Models\Country;
use App\Models\CreditLoan;
use App\Models\Deposit;
use App\Models\DocumentList;
use App\Models\GoldLoan;
use App\Models\LoanCurrenciesType;
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
use App\Models\TravelInsurancesVariation;
use App\Models\YesNo;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use Illuminate\Database\Eloquent\Model;

trait ProductTrait
{
    /**
     * car loan Product page
     *
     * @return \Illuminate\Http\Response
     */
    function carLoanProduct($unique_options, $car_cost, $prepayment, $time_type, $term, Request $request)
    {
        $belonging_id = 1;

        $belongings = Belonging::with('productsByBelongingInfo')->get();

        $documents_list = DocumentList::all();

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
            $time_type = 1;

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

        $getCalculation = $this->getCalculation($product, $product_variation, $car_cost, $loan_amount, $term, $loan_term_search_in_days, $prepayment_percent, $time_type);//calculate factual_percentage and other

        $require_payments_schedule_annually_and_summary = [];


        $loan_application_fee = $getCalculation["other_fee"]["loan_application_fee"];

        /*calculate $loan_service_fee*/
        $loan_service_fee = 0;

        $loan_service_fee_yearly = 0;

        foreach ($getCalculation["schedule"] as $schedule_item) {
            if (array_key_exists('loan_service_fee', $schedule_item)) {

                $loan_service_fee_yearly = $schedule_item['loan_service_fee'];

                $loan_service_fee += $schedule_item['loan_service_fee'];
            }
        }
        /*calculate $loan_service_fee*/

        $collateral_assessment_fee = $getCalculation["other_fee"]["collateral_assessment_fee"];

        $cash_service_fee = $getCalculation["other_fee"]["cash_service_fee"];

        $notary_validation_fee = $getCalculation["other_fee"]["notary_validation_fee"];

        $require_payments_schedule_annually_and_summary[] =
            ["name" => "Վարկային հայտի ուսումնասիրության վճար (միանվագ)", "anually" => $loan_application_fee, "summary" => $loan_application_fee];

        $require_payments_schedule_annually_and_summary[] =
            ["name" => "Վարկի սպասարկման վճար (տարեկան)", "anually" => $loan_service_fee_yearly, "summary" => $loan_service_fee];

        $require_payments_schedule_annually_and_summary[] =
            ["name" => "Գրավի գնահատման վճար (միանվագ)", "anually" => $collateral_assessment_fee, "summary" => $collateral_assessment_fee];

        $require_payments_schedule_annually_and_summary[] =
            ["name" => "Կանխիկացման վճար", "anually" => $cash_service_fee, "summary" => $cash_service_fee];

        $require_payments_schedule_annually_and_summary[] =
            ["name" => "Նոտարական վավերացման վճար (միանվագ)", "anually" => $notary_validation_fee, "summary" => $notary_validation_fee];

        $factual_percentage = 100 * $getCalculation["xirr"];

        $factual_percentage = round($factual_percentage, 2); // rounding float 2 chars precision

        $require_payments = $getCalculation["require_payments"];

        $sum_payments = $getCalculation["sum_payments"];

        $more_payment_amount = $sum_payments - $loan_amount;

        $getCompareInfo = $this->getCompareInfo();

        $more_payment_amount_piece = round($more_payment_amount / $sum_payments, 2);

        $loan_amount_piece = round($loan_amount / $sum_payments, 2);

        return view('product.carloan', [
            "belonging_id" => $belonging_id,

            "belongings" => $belongings,

            "documents_list" => $documents_list,

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

            "more_payment_amount_piece" => $more_payment_amount_piece,

            "loan_amount_piece" => $loan_amount_piece,

            "repayment_loan_interval_types" => $repayment_loan_interval_types,

            "repayment_percent_interval_types" => $repayment_percent_interval_types,

            "productPercentageTypesArr" => $productPercentageTypesArr,

            "getCompareInfo" => $getCompareInfo,
        ]);
    }

    /**
     * car loan Product page
     *
     * @return \Illuminate\Http\Response
     */
    function creditLoanProduct($unique_options, $cost, $prepayment, $time_type, $term, Request $request)
    {
        $belonging_id = 3;

        $belongings = Belonging::with('productsByBelongingInfo')->get();

        $documents_list = DocumentList::all();

        $product_variation = ProductsVariation::where(DB::raw("md5(unique_options)"), $unique_options)
            ->with('providingTypeInfo')->with('repaymentTypeInfo')->first();

        $product_id = $product_variation->product_id;

        $product = CreditLoan::where('id', $product_id)->with('companyInfo')
//            ->with('carInfo')
            ->with('securityTypes')
            ->with('sellSalons')->first();

        $repayment_loan_interval_types = RepaymentLoanIntervalType::all();

        $repayment_percent_interval_types = RepaymentPercentIntervalType::all();

        $productPercentageTypesArr = ["1" => "+", "2" => "-", "3" => "±"];

        $loan_amount = $cost - $prepayment;

        if ($time_type == 1 || $time_type == "" || is_null($time_type)) {
            $time_type = 1;

            $loan_term_search_in_days = $term;
        } else if ($time_type == 2) {

            $loan_term_search_in_days = $term * 30;
        } else if ($time_type == 3) {

            $loan_term_search_in_days = $term * 365;
        }

        if ($cost > 0) {
            $prepayment_percent = 100 * $prepayment / $cost;
        } else {
            $prepayment_percent = null;
        }

        $getCalculation = $this->getCalculation($product, $product_variation, $cost, $loan_amount, $term, $loan_term_search_in_days, $prepayment_percent, $time_type);//calculate factual_percentage and other

        $require_payments_schedule_annually_and_summary = [];


        $loan_application_fee = $getCalculation["other_fee"]["loan_application_fee"];

        /*calculate $loan_service_fee*/
        $loan_service_fee = 0;

        $loan_service_fee_yearly = 0;

        foreach ($getCalculation["schedule"] as $schedule_item) {
            if (array_key_exists('loan_service_fee', $schedule_item)) {

                $loan_service_fee_yearly = $schedule_item['loan_service_fee'];

                $loan_service_fee += $schedule_item['loan_service_fee'];
            }
        }
        /*calculate $loan_service_fee*/

        $collateral_assessment_fee = $getCalculation["other_fee"]["collateral_assessment_fee"];

        $cash_service_fee = $getCalculation["other_fee"]["cash_service_fee"];

        /*calculate $collateral_insurance_fee*/
        $collateral_insurance_fee = 0;

        $collateral_insurance_fee_yearly = 0;

        foreach ($getCalculation["schedule"] as $schedule_item) {
            if (array_key_exists('loan_service_fee', $schedule_item)) {

                $collateral_insurance_fee_yearly = $schedule_item['collateral_insurance_fee'];

                $collateral_insurance_fee += $schedule_item['collateral_insurance_fee'];
            }
        }
        /*calculate $collateral_insurance_fee*/

        $require_payments_schedule_annually_and_summary[] =
            ["name" => "Վարկային հայտի ուսումնասիրության վճար (միանվագ)", "anually" => $loan_application_fee, "summary" => $loan_application_fee];

        $require_payments_schedule_annually_and_summary[] =
            ["name" => "Վարկի սպասարկման վճար (տարեկան)", "anually" => $loan_service_fee_yearly, "summary" => $loan_service_fee];

        $require_payments_schedule_annually_and_summary[] =
            ["name" => "Գրավի գնահատման վճար (միանվագ)", "anually" => $collateral_assessment_fee, "summary" => $collateral_assessment_fee];

        $require_payments_schedule_annually_and_summary[] =
            ["name" => "Կանխիկացման վճար", "anually" => $cash_service_fee, "summary" => $cash_service_fee];

        $require_payments_schedule_annually_and_summary[] =
            ["name" => "Գրավի ապահովագրության վճար (տարեկան)", "anually" => $collateral_insurance_fee_yearly, "summary" => $collateral_insurance_fee];

        $require_payments_anually_sum = array_sum([$loan_application_fee, $loan_service_fee_yearly, $collateral_assessment_fee, $cash_service_fee, $collateral_insurance_fee_yearly]);

        $require_payments_full_sum = array_sum([$loan_application_fee, $loan_service_fee, $collateral_assessment_fee, $cash_service_fee, $collateral_insurance_fee]);

        if ($product->other_payments == 1) {
            foreach ($product->otherPayments as $key => $productCurrOtherPayment) {
                $other_payments_name = $productCurrOtherPayment->other_payments_name;

                $other_payments_sum_amount = 0;

                $other_payments_yearly_amount = 0;

                if ($productCurrOtherPayment->other_payments_periodicity == 1) {

                    $other_payments_sum_amount = @$getCalculation["other_fee"]["loan_other_onetime_fee_" . $key];

                    $other_payments_yearly_amount = @$getCalculation["other_fee"]["loan_other_onetime_fee_" . $key];
                } else if ($productCurrOtherPayment->other_payments_periodicity == 3) {

                    foreach ($getCalculation["schedule"] as $schedule_item) {

                        if (array_key_exists('loan_other_year_fee_' . $key, $schedule_item)) {

                            $other_payments_yearly_amount = $schedule_item['loan_other_year_fee_' . $key];

                            $other_payments_sum_amount += $schedule_item['loan_other_year_fee_' . $key];
                        }
                    }
                } else if ($productCurrOtherPayment->other_payments_periodicity == 2) {

                    foreach ($getCalculation["schedule"] as $schedule_item) {

                        if (array_key_exists('loan_other_mount_fee_' . $key, $schedule_item)) {

                            $other_payments_monthly_amount = $schedule_item['loan_other_mount_fee_' . $key];

                            $other_payments_sum_amount += $schedule_item['loan_other_mount_fee_' . $key];
                        }
                    }

                    $other_payments_yearly_amount = 12 * $other_payments_monthly_amount;
                }

                $require_payments_schedule_annually_and_summary[] =
                    ["name" => $other_payments_name, "anually" => $other_payments_yearly_amount, "summary" => $other_payments_sum_amount];

                $require_payments_anually_sum += $other_payments_yearly_amount;

                $require_payments_full_sum += $other_payments_sum_amount;
            }
        }

        $factual_percentage = 100 * $getCalculation["xirr"];

        $factual_percentage = round($factual_percentage, 2); // rounding float 2 chars precision

        $require_payments = $getCalculation["require_payments"];

        $sum_payments = $getCalculation["sum_payments"];

        $more_payment_amount = $sum_payments - $loan_amount;

        $getCompareInfo = $this->getCompareInfo();

        $more_payment_amount_piece = round($more_payment_amount / $sum_payments, 2);

        $loan_amount_piece = round($loan_amount / $sum_payments, 2);

        return view('product.creditloan', [
            "belonging_id" => $belonging_id,

            "belongings" => $belongings,

            "documents_list" => $documents_list,

            "cost" => $cost,

            "prepayment" => $prepayment,

            "time_type" => $time_type,

            "term" => $term,

            "loan_term_search_in_days" => $loan_term_search_in_days,

            "loan_amount" => $loan_amount,

            "product" => $product,

            "product_variation" => $product_variation,

            "getCalculation" => $getCalculation,

            "require_payments_schedule_annually_and_summary" => $require_payments_schedule_annually_and_summary,

            "require_payments_anually_sum" => $require_payments_anually_sum,

            "require_payments_full_sum" => $require_payments_full_sum,

            "factual_percentage" => $factual_percentage,

            "require_payments" => $require_payments,

            "sum_payments" => $sum_payments,

            "more_payment_amount" => $more_payment_amount,

            "more_payment_amount_piece" => $more_payment_amount_piece,

            "loan_amount_piece" => $loan_amount_piece,

            "repayment_loan_interval_types" => $repayment_loan_interval_types,

            "repayment_percent_interval_types" => $repayment_percent_interval_types,

            "productPercentageTypesArr" => $productPercentageTypesArr,

            "getCompareInfo" => $getCompareInfo,
        ]);
    }

    /**
     * gold loan Product page
     *
     * @return \Illuminate\Http\Response
     */
    function goldLoanProduct($unique_options, $loan_amount, $time_type, $term, Request $request)
    {
//        dd($request->all());
        $belonging_id = 2;

        $belongings = Belonging::with('productsByBelongingInfo')->get();

        $documents_list = DocumentList::all();

        $product_variation = ProductsVariation::where(DB::raw("md5(unique_options)"), $unique_options)
            ->with('providingTypeInfo')->with('repaymentTypeInfo')->first();

        $product_id = $product_variation->product_id;

        $product = GoldLoan::where('id', $product_id)->with('companyInfo')
            ->with('goldPledgeTypeInfo')->with('goldAssayTypes')->with('goldLoanAssessmentAssaysValues')->with('otherPayments')->first();

        $repayment_loan_interval_types = RepaymentLoanIntervalType::all();

        $repayment_percent_interval_types = RepaymentPercentIntervalType::all();

        $productPercentageTypesArr = ["1" => "+", "2" => "-", "3" => "±"];

        if ($time_type == 1 || $time_type == "" || is_null($time_type)) {
            $time_type = 1;

            $loan_term_search_in_days = $term;
        } else if ($time_type == 2) {

            $loan_term_search_in_days = $term * 30;
        } else if ($time_type == 3) {

            $loan_term_search_in_days = $term * 365;
        }


        if (intval($product->loan_pledge_ratio) == 0) {
            $cost = 0;
        } else {
            $cost = 100 * $loan_amount / $product->loan_pledge_ratio;
        }

        $getCalculation = $this->getCalculation($product, $product_variation, $cost, $loan_amount, $term, $loan_term_search_in_days, 0, $time_type);//calculate factual_percentage and other

        $require_payments_schedule_annually_and_summary = [];

        $factual_percentage = 100 * $getCalculation["xirr"];

        $factual_percentage = round($factual_percentage, 2); // rounding float 2 chars precision

        $require_payments = $getCalculation["require_payments"];

        $sum_payments = $getCalculation["sum_payments"];

        $more_payment_amount = $sum_payments - $loan_amount;


        $loan_application_fee = $getCalculation["other_fee"]["loan_application_fee"];

        /*calculate $loan_service_fee*/
        $loan_service_fee = 0;

        $loan_service_fee_yearly = 0;

        foreach ($getCalculation["schedule"] as $schedule_item) {
            if (array_key_exists('loan_service_fee', $schedule_item)) {

                $loan_service_fee_yearly = $schedule_item['loan_service_fee'];

                $loan_service_fee += $schedule_item['loan_service_fee'];
            }
        }
        /*calculate $loan_service_fee*/

        $collateral_assessment_fee = $getCalculation["other_fee"]["collateral_assessment_fee"];

        $collateral_maintenance_fee = $getCalculation["other_fee"]["collateral_maintenance_fee"];

        $cash_service_fee = $getCalculation["other_fee"]["cash_service_fee"];

        $require_payments_schedule_annually_and_summary[] =
            ["name" => "Վարկային հայտի ուսումնասիրության վճար (միանվագ)", "anually" => $loan_application_fee, "summary" => $loan_application_fee];

        $require_payments_schedule_annually_and_summary[] =
            ["name" => "Վարկի սպասարկման վճար (տարեկան)", "anually" => $loan_service_fee_yearly, "summary" => $loan_service_fee];

        $require_payments_schedule_annually_and_summary[] =
            ["name" => "Կանխիկացման վճար", "anually" => $cash_service_fee, "summary" => $cash_service_fee];

        $require_payments_schedule_annually_and_summary[] =
            ["name" => "Գրավի գնահատման վճար (միանվագ)", "anually" => $collateral_assessment_fee, "summary" => $collateral_assessment_fee];

        $require_payments_schedule_annually_and_summary[] =
            ["name" => "Գրավի պահպանման վճար (միանվագ)", "anually" => $collateral_maintenance_fee, "summary" => $collateral_maintenance_fee];

        $require_payments_anually_sum = array_sum([$loan_application_fee, $loan_service_fee_yearly, $cash_service_fee, $collateral_assessment_fee, $collateral_maintenance_fee]);

        $require_payments_full_sum = array_sum([$loan_application_fee, $loan_service_fee, $cash_service_fee, $collateral_assessment_fee, $collateral_maintenance_fee]);

        if ($product->other_payments == 1) {
            foreach ($product->otherPayments as $key => $productCurrOtherPayment) {
                $other_payments_name = $productCurrOtherPayment->other_payments_name;

                if ($productCurrOtherPayment->other_payments_periodicity == 1) {

                    $other_payments_sum_amount = @$getCalculation["other_fee"]["loan_other_onetime_fee_" . $key];

                    $other_payments_yearly_amount = @$getCalculation["other_fee"]["loan_other_onetime_fee_" . $key];
                } else if ($productCurrOtherPayment->other_payments_periodicity == 3) {

                    $other_payments_sum_amount = 0;

                    foreach ($getCalculation["schedule"] as $schedule_item) {

                        if (array_key_exists('loan_service_fee', $schedule_item)) {

                            $other_payments_yearly_amount = $schedule_item['loan_other_year_fee_' . $key];

                            $other_payments_sum_amount += $schedule_item['loan_other_year_fee_' . $key];
                        }
                    }
                } else if ($productCurrOtherPayment->other_payments_periodicity == 2) {

                }

                $require_payments_schedule_annually_and_summary[] =
                    ["name" => $other_payments_name, "anually" => $other_payments_yearly_amount, "summary" => $other_payments_sum_amount];

                $require_payments_anually_sum += $other_payments_yearly_amount;

                $require_payments_full_sum += $other_payments_sum_amount;
            }
        }

        $getCompareInfo = $this->getCompareInfo();

        $more_payment_amount_piece = round($more_payment_amount / $sum_payments, 2);

        $loan_amount_piece = round($loan_amount / $sum_payments, 2);

        return view('product.goldloan', [
            "belonging_id" => $belonging_id,

            "belongings" => $belongings,

            "documents_list" => $documents_list,

            "cost" => $cost,

            "time_type" => $time_type,

            "term" => $term,

            "loan_term_search_in_days" => $loan_term_search_in_days,

            "loan_amount" => $loan_amount,

            "product" => $product,

            "product_variation" => $product_variation,

            "getCalculation" => $getCalculation,

            "require_payments_schedule_annually_and_summary" => $require_payments_schedule_annually_and_summary,

            "require_payments_anually_sum" => $require_payments_anually_sum,

            "require_payments_full_sum" => $require_payments_full_sum,

            "factual_percentage" => $factual_percentage,

            "require_payments" => $require_payments,

            "sum_payments" => $sum_payments,

            "more_payment_amount" => $more_payment_amount,

            "more_payment_amount_piece" => $more_payment_amount_piece,

            "loan_amount_piece" => $loan_amount_piece,

            "repayment_loan_interval_types" => $repayment_loan_interval_types,

            "repayment_percent_interval_types" => $repayment_percent_interval_types,

            "productPercentageTypesArr" => $productPercentageTypesArr,

            "getCompareInfo" => $getCompareInfo,
        ]);
    }

    /**
     * student loan Product page
     *
     * @return \Illuminate\Http\Response
     */
    function studentLoanProduct($unique_options, $loan_amount, $time_type, $term, Request $request)
    {
        $belonging_id = 4;

        $belongings = Belonging::with('productsByBelongingInfo')->get();

        $documents_list = DocumentList::all();

        $product_variation = ProductsVariation::where(DB::raw("md5(unique_options)"), $unique_options)
            ->with('providingTypeInfo')->with('repaymentTypeInfo')->first();

        $product_id = $product_variation->product_id;

        $product = StudentLoan::where('id', $product_id)->with('companyInfo')->with('otherPayments')->first();

        $repayment_loan_interval_types = RepaymentLoanIntervalType::all();

        $repayment_percent_interval_types = RepaymentPercentIntervalType::all();

        $productPercentageTypesArr = ["1" => "+", "2" => "-", "3" => "±"];

        if ($time_type == 1 || $time_type == "" || is_null($time_type)) {
            $time_type = 1;

            $loan_term_search_in_days = $term;
        } else if ($time_type == 2) {

            $loan_term_search_in_days = $term * 30;
        } else if ($time_type == 3) {

            $loan_term_search_in_days = $term * 365;
        }

        if (intval($product->loan_pledge_ratio) == 0) {
            $cost = 0;
        } else {
            $cost = 100 * $loan_amount / $product->loan_pledge_ratio;
        }

        $getCalculation = $this->getCalculation($product, $product_variation, $cost, $loan_amount, $term, $loan_term_search_in_days, 0, $time_type);//calculate factual_percentage and other

        $require_payments_schedule_annually_and_summary = [];

        $factual_percentage = 100 * $getCalculation["xirr"];

        $factual_percentage = round($factual_percentage, 2); // rounding float 2 chars precision

        $require_payments = $getCalculation["require_payments"];

        $sum_payments = $getCalculation["sum_payments"];

        $more_payment_amount = $sum_payments - $loan_amount;


        $loan_application_fee = $getCalculation["other_fee"]["loan_application_fee"];

        /*calculate $loan_service_fee*/
        $loan_service_fee = 0;

        $loan_service_fee_yearly = 0;

        foreach ($getCalculation["schedule"] as $schedule_item) {
            if (array_key_exists('loan_service_fee', $schedule_item)) {

                $loan_service_fee_yearly = $schedule_item['loan_service_fee'];

                $loan_service_fee += $schedule_item['loan_service_fee'];
            }
        }
        /*calculate $loan_service_fee*/

        /*calculate $collateral_insurance_fee*/
        $collateral_insurance_fee = 0;

        $collateral_insurance_fee_yearly = 0;

        foreach ($getCalculation["schedule"] as $schedule_item) {
            if (array_key_exists('loan_service_fee', $schedule_item)) {

                $collateral_insurance_fee_yearly = $schedule_item['collateral_insurance_fee'];

                $collateral_insurance_fee += $schedule_item['collateral_insurance_fee'];
            }
        }
        /*calculate $collateral_insurance_fee*/

        $collateral_assessment_fee = $getCalculation["other_fee"]["collateral_assessment_fee"];

        $notary_validation_fee = $getCalculation["other_fee"]["notary_validation_fee"];

        $require_payments_schedule_annually_and_summary[] =
            ["name" => "Վարկային հայտի ուսումնասիրության վճար (միանվագ)", "anually" => $loan_application_fee, "summary" => $loan_application_fee];

        $require_payments_schedule_annually_and_summary[] =
            ["name" => "Վարկի սպասարկման վճար (տարեկան)", "anually" => $loan_service_fee_yearly, "summary" => $loan_service_fee];

        $require_payments_schedule_annually_and_summary[] =
            ["name" => "Գրավի գնահատման վճար (միանվագ)", "anually" => $collateral_assessment_fee, "summary" => $collateral_assessment_fee];

        $require_payments_schedule_annually_and_summary[] =
            ["name" => "Գրավի ապահովագրության վճար (տարեկան)", "anually" => $collateral_insurance_fee_yearly, "summary" => $collateral_insurance_fee];

        $require_payments_schedule_annually_and_summary[] =
            ["name" => "Նոտարական վավերացման վճար (միանվագ)", "anually" => $notary_validation_fee, "summary" => $notary_validation_fee];

        $require_payments_anually_sum = array_sum([$loan_application_fee, $loan_service_fee_yearly, $collateral_insurance_fee_yearly, $collateral_assessment_fee, $notary_validation_fee]);

        $require_payments_full_sum = array_sum([$loan_application_fee, $loan_service_fee, $collateral_assessment_fee, $collateral_insurance_fee, $notary_validation_fee]);

        if ($product->other_payments == 1) {
            foreach ($product->otherPayments as $key => $productCurrOtherPayment) {
                $other_payments_name = $productCurrOtherPayment->other_payments_name;

                $other_payments_sum_amount = 0;

                $other_payments_yearly_amount = 0;

                if ($productCurrOtherPayment->other_payments_periodicity == 1) {

                    $other_payments_sum_amount = @$getCalculation["other_fee"]["loan_other_onetime_fee_" . $key];

                    $other_payments_yearly_amount = @$getCalculation["other_fee"]["loan_other_onetime_fee_" . $key];
                } else if ($productCurrOtherPayment->other_payments_periodicity == 3) {

                    foreach ($getCalculation["schedule"] as $schedule_item) {

                        if (array_key_exists('loan_other_year_fee_' . $key, $schedule_item)) {

                            $other_payments_yearly_amount = $schedule_item['loan_other_year_fee_' . $key];

                            $other_payments_sum_amount += $schedule_item['loan_other_year_fee_' . $key];
                        }
                    }
                } else if ($productCurrOtherPayment->other_payments_periodicity == 2) {

                    foreach ($getCalculation["schedule"] as $schedule_item) {

                        if (array_key_exists('loan_other_mount_fee_' . $key, $schedule_item)) {

                            $other_payments_monthly_amount = $schedule_item['loan_other_mount_fee_' . $key];

                            $other_payments_sum_amount += $schedule_item['loan_other_mount_fee_' . $key];
                        }
                    }

                    $other_payments_yearly_amount = 12 * $other_payments_monthly_amount;
                }

                $require_payments_schedule_annually_and_summary[] =
                    ["name" => $other_payments_name, "anually" => $other_payments_yearly_amount, "summary" => $other_payments_sum_amount];

                $require_payments_anually_sum += $other_payments_yearly_amount;

                $require_payments_full_sum += $other_payments_sum_amount;
            }
        }

        $getCompareInfo = $this->getCompareInfo();

        $more_payment_amount_piece = round($more_payment_amount / $sum_payments, 2);

        $loan_amount_piece = round($loan_amount / $sum_payments, 2);

        return view('product.studentloan', [
            "belonging_id" => $belonging_id,

            "belongings" => $belongings,

            "documents_list" => $documents_list,

            "cost" => $cost,

            "time_type" => $time_type,

            "term" => $term,

            "loan_term_search_in_days" => $loan_term_search_in_days,

            "loan_amount" => $loan_amount,

            "product" => $product,

            "product_variation" => $product_variation,

            "getCalculation" => $getCalculation,

            "require_payments_schedule_annually_and_summary" => $require_payments_schedule_annually_and_summary,

            "require_payments_anually_sum" => $require_payments_anually_sum,

            "require_payments_full_sum" => $require_payments_full_sum,

            "factual_percentage" => $factual_percentage,

            "require_payments" => $require_payments,

            "sum_payments" => $sum_payments,

            "more_payment_amount" => $more_payment_amount,

            "more_payment_amount_piece" => $more_payment_amount_piece,

            "loan_amount_piece" => $loan_amount_piece,

            "repayment_loan_interval_types" => $repayment_loan_interval_types,

            "repayment_percent_interval_types" => $repayment_percent_interval_types,

            "productPercentageTypesArr" => $productPercentageTypesArr,

            "getCompareInfo" => $getCompareInfo,
        ]);
    }

    /**
     * agric loan Product page
     *
     * @return \Illuminate\Http\Response
     */
    function agricLoanProduct($unique_options, $loan_amount, $time_type, $term, Request $request)
    {
        $belonging_id = 5;

        $belongings = Belonging::with('productsByBelongingInfo')->get();

        $documents_list = DocumentList::all();

        $product_variation = ProductsVariation::where(DB::raw("md5(unique_options)"), $unique_options)
            ->with('providingTypeInfo')->with('repaymentTypeInfo')->first();

        $product_id = $product_variation->product_id;

        $product = AgricLoan::where('id', $product_id)->with('companyInfo')->with('otherPayments')->first();

        $repayment_loan_interval_types = RepaymentLoanIntervalType::all();

        $repayment_percent_interval_types = RepaymentPercentIntervalType::all();

        $productPercentageTypesArr = ["1" => "+", "2" => "-", "3" => "±"];

        if ($time_type == 1 || $time_type == "" || is_null($time_type)) {
            $time_type = 1;

            $loan_term_search_in_days = $term;
        } else if ($time_type == 2) {

            $loan_term_search_in_days = $term * 30;
        } else if ($time_type == 3) {

            $loan_term_search_in_days = $term * 365;
        }

        $currency = $product->currency;

        $loan_amount_converted = $this->getLoanAmountConverted($currency, $loan_amount);

        if (intval($product->loan_pledge_ratio) == 0) {
            $cost = 0;
        } else {
            $cost = 100 * $loan_amount_converted / $product->loan_pledge_ratio;
        }

        $getCalculation = $this->getCalculation($product, $product_variation, $cost, $loan_amount_converted, $term, $loan_term_search_in_days, 0, $time_type);//calculate factual_percentage and other

        $require_payments_schedule_annually_and_summary = [];

        $factual_percentage = 100 * $getCalculation["xirr"];

        $factual_percentage = round($factual_percentage, 2); // rounding float 2 chars precision

        $require_payments = $getCalculation["require_payments"];

        $sum_payments = $getCalculation["sum_payments"];

        $more_payment_amount = $sum_payments - $loan_amount_converted;


        $loan_application_fee = $getCalculation["other_fee"]["loan_application_fee"];

        /*calculate $loan_service_fee*/
        $loan_service_fee = 0;

        $loan_service_fee_yearly = 0;

        foreach ($getCalculation["schedule"] as $schedule_item) {
            if (array_key_exists('loan_service_fee', $schedule_item)) {

                $loan_service_fee_yearly = $schedule_item['loan_service_fee'];

                $loan_service_fee += $schedule_item['loan_service_fee'];
            }
        }
        /*calculate $loan_service_fee*/

        /*calculate $collateral_insurance_fee*/
        $collateral_insurance_fee = 0;

        $collateral_insurance_fee_yearly = 0;

        foreach ($getCalculation["schedule"] as $schedule_item) {
            if (array_key_exists('loan_service_fee', $schedule_item)) {

                $collateral_insurance_fee_yearly = $schedule_item['collateral_insurance_fee'];

                $collateral_insurance_fee += $schedule_item['collateral_insurance_fee'];
            }
        }
        /*calculate $collateral_insurance_fee*/

        $cash_service_fee = $getCalculation["other_fee"]["cash_service_fee"];

        $collateral_assessment_fee = $getCalculation["other_fee"]["collateral_assessment_fee"];

        $collateral_maintenance_fee = $getCalculation["other_fee"]["collateral_maintenance_fee"];

        $notary_validation_fee = $getCalculation["other_fee"]["notary_validation_fee"];

        $pledge_state_fee = $getCalculation["other_fee"]["pledge_state_fee"];

//        $borrower_insurance_fee = $getCalculation["other_fee"]["borrower_insurance_fee"];

        $require_payments_schedule_annually_and_summary[] =
            ["name" => "Վարկային հայտի ուսումնասիրության վճար (միանվագ)", "anually" => $loan_application_fee, "summary" => $loan_application_fee];

        $require_payments_schedule_annually_and_summary[] =
            ["name" => "Վարկի սպասարկման վճար (տարեկան)", "anually" => $loan_service_fee_yearly, "summary" => $loan_service_fee];

        $require_payments_schedule_annually_and_summary[] =
            ["name" => "Գրավի գնահատման վճար (միանվագ)", "anually" => $collateral_assessment_fee, "summary" => $collateral_assessment_fee];

        $require_payments_schedule_annually_and_summary[] =
            ["name" => "Կանխիկացման վճար", "anually" => $cash_service_fee, "summary" => $cash_service_fee];

        $require_payments_schedule_annually_and_summary[] =
            ["name" => "Գրավի ապահովագրության վճար (տարեկան)", "anually" => $collateral_insurance_fee_yearly, "summary" => $collateral_insurance_fee];

        $require_payments_schedule_annually_and_summary[] =
            ["name" => "Նոտարական վավերացման վճար (միանվագ)", "anually" => $notary_validation_fee, "summary" => $notary_validation_fee];

        $require_payments_schedule_annually_and_summary[] =
            ["name" => "Գրավի պետական գրանցման հետ կապված վճար (միանվագ)", "anually" => $pledge_state_fee, "summary" => $pledge_state_fee];

        $require_payments_schedule_annually_and_summary[] =
            ["name" => "Գրավի պահպանման վճար (միանվագ)", "anually" => $collateral_maintenance_fee, "summary" => $collateral_maintenance_fee];

        $require_payments_anually_sum = array_sum([$loan_application_fee, $loan_service_fee_yearly, $collateral_insurance_fee_yearly, $collateral_assessment_fee, $notary_validation_fee]);

        $require_payments_full_sum = array_sum([$loan_application_fee, $loan_service_fee, $collateral_assessment_fee, $collateral_insurance_fee, $notary_validation_fee]);

        if ($product->other_payments == 1) {
            foreach ($product->otherPayments as $key => $productCurrOtherPayment) {
                $other_payments_name = $productCurrOtherPayment->other_payments_name;

                $other_payments_sum_amount = 0;

                $other_payments_yearly_amount = 0;

                if ($productCurrOtherPayment->other_payments_periodicity == 1) {

                    $other_payments_sum_amount = @$getCalculation["other_fee"]["loan_other_onetime_fee_" . $key];

                    $other_payments_yearly_amount = @$getCalculation["other_fee"]["loan_other_onetime_fee_" . $key];
                } else if ($productCurrOtherPayment->other_payments_periodicity == 3) {

                    foreach ($getCalculation["schedule"] as $schedule_item) {

                        if (array_key_exists('loan_other_year_fee_' . $key, $schedule_item)) {

                            $other_payments_yearly_amount = $schedule_item['loan_other_year_fee_' . $key];

                            $other_payments_sum_amount += $schedule_item['loan_other_year_fee_' . $key];
                        }
                    }
                } else if ($productCurrOtherPayment->other_payments_periodicity == 2) {

                    foreach ($getCalculation["schedule"] as $schedule_item) {

                        if (array_key_exists('loan_other_mount_fee_' . $key, $schedule_item)) {

                            $other_payments_monthly_amount = $schedule_item['loan_other_mount_fee_' . $key];

                            $other_payments_sum_amount += $schedule_item['loan_other_mount_fee_' . $key];
                        }
                    }

                    $other_payments_yearly_amount = 12 * $other_payments_monthly_amount;
                }

                $require_payments_schedule_annually_and_summary[] =
                    ["name" => $other_payments_name, "anually" => $other_payments_yearly_amount, "summary" => $other_payments_sum_amount];

                $require_payments_anually_sum += $other_payments_yearly_amount;

                $require_payments_full_sum += $other_payments_sum_amount;
            }
        }

        $getCompareInfo = $this->getCompareInfo();

        $more_payment_amount_piece = round($more_payment_amount / $sum_payments, 2);

        $loan_amount_piece = round($loan_amount_converted / $sum_payments, 2);

        return view('product.agricloan', [
            "belonging_id" => $belonging_id,

            "belongings" => $belongings,

            "documents_list" => $documents_list,

            "cost" => $cost,

            "time_type" => $time_type,

            "term" => $term,

            "loan_term_search_in_days" => $loan_term_search_in_days,

            "loan_amount" => $loan_amount,

            "loan_amount_converted" => $loan_amount_converted,

            "product" => $product,

            "product_variation" => $product_variation,

            "getCalculation" => $getCalculation,

            "require_payments_schedule_annually_and_summary" => $require_payments_schedule_annually_and_summary,

            "require_payments_anually_sum" => $require_payments_anually_sum,

            "require_payments_full_sum" => $require_payments_full_sum,

            "factual_percentage" => $factual_percentage,

            "require_payments" => $require_payments,

            "sum_payments" => $sum_payments,

            "more_payment_amount" => $more_payment_amount,

            "more_payment_amount_piece" => $more_payment_amount_piece,

            "loan_amount_piece" => $loan_amount_piece,

            "repayment_loan_interval_types" => $repayment_loan_interval_types,

            "repayment_percent_interval_types" => $repayment_percent_interval_types,

            "productPercentageTypesArr" => $productPercentageTypesArr,

            "getCompareInfo" => $getCompareInfo,
        ]);
    }

    /**
     * mortgage loan Product page
     *
     * @return \Illuminate\Http\Response
     */
    function mortgageLoanProduct($unique_options, $cost, $prepayment, $time_type, $term, Request $request)
    {
        $belonging_id = 8;

        $belongings = Belonging::with('productsByBelongingInfo')->get();

        $documents_list = DocumentList::all();

        $product_variation = ProductsVariation::where(DB::raw("md5(unique_options)"), $unique_options)
            ->with('providingTypeInfo')->with('repaymentTypeInfo')->first();

        $product_id = $product_variation->product_id;

        $product = Mortgage::where('id', $product_id)->with('companyInfo')->with('otherPayments')->first();

        $repayment_loan_interval_types = RepaymentLoanIntervalType::all();

        $repayment_percent_interval_types = RepaymentPercentIntervalType::all();

        $productPercentageTypesArr = ["1" => "+", "2" => "-", "3" => "±"];

        $loan_amount = $cost - $prepayment;

        if ($time_type == 1 || $time_type == "" || is_null($time_type)) {
            $time_type = 1;

            $loan_term_search_in_days = $term;
        } else if ($time_type == 2) {

            $loan_term_search_in_days = $term * 30;
        } else if ($time_type == 3) {

            $loan_term_search_in_days = $term * 365;
        }

        if ($cost > 0) {
            $prepayment_percent = 100 * $prepayment / $cost;
        } else {
            $prepayment_percent = null;
        }

        $currency = $product->currency;

        $loan_amount_converted = $this->getLoanAmountConverted($currency, $loan_amount);

        $cost_converted = $this->getLoanAmountConverted($currency, $cost);

        $getCalculation = $this->getCalculation($product, $product_variation, $cost_converted, $loan_amount_converted, $term, $loan_term_search_in_days, 0, $time_type);//calculate factual_percentage and other

        $require_payments_schedule_annually_and_summary = [];

        $factual_percentage = 100 * $getCalculation["xirr"];

        $factual_percentage = round($factual_percentage, 2); // rounding float 2 chars precision

        $require_payments = $getCalculation["require_payments"];

        $sum_payments = $getCalculation["sum_payments"];

        $more_payment_amount = $sum_payments - $loan_amount;


        $loan_application_fee = $getCalculation["other_fee"]["loan_application_fee"];

        /*calculate $loan_service_fee*/
        $loan_service_fee = 0;

        $loan_service_fee_yearly = 0;

        foreach ($getCalculation["schedule"] as $schedule_item) {
            if (array_key_exists('loan_service_fee', $schedule_item)) {

                $loan_service_fee_yearly = $schedule_item['loan_service_fee'];

                $loan_service_fee += $schedule_item['loan_service_fee'];
            }
        }
        /*calculate $loan_service_fee*/

        /*calculate $borrower_insurance_fee*/
        $borrower_insurance_fee = 0;

        $borrower_insurance_fee_yearly = 0;

        foreach ($getCalculation["schedule"] as $schedule_item) {
            if (array_key_exists('borrower_insurance_fee', $schedule_item)) {

                $borrower_insurance_fee_yearly = $schedule_item['borrower_insurance_fee'];

                $borrower_insurance_fee += $schedule_item['borrower_insurance_fee'];
            }
        }
        /*calculate $borrower_insurance_fee*/

        /*calculate $collateral_insurance_fee*/
        $collateral_insurance_fee = 0;

        $collateral_insurance_fee_yearly = 0;

        foreach ($getCalculation["schedule"] as $schedule_item) {
            if (array_key_exists('loan_service_fee', $schedule_item)) {

                $collateral_insurance_fee_yearly = $schedule_item['collateral_insurance_fee'];

                $collateral_insurance_fee += $schedule_item['collateral_insurance_fee'];
            }
        }
        /*calculate $collateral_insurance_fee*/

        $cash_service_fee = $getCalculation["other_fee"]["cash_service_fee"];

        $collateral_assessment_fee = $getCalculation["other_fee"]["collateral_assessment_fee"];

        $collateral_maintenance_fee = $getCalculation["other_fee"]["collateral_maintenance_fee"];

        $notary_validation_fee = $getCalculation["other_fee"]["notary_validation_fee"];

        $pledge_state_fee = $getCalculation["other_fee"]["pledge_state_fee"];

        $cadastre_fee = $getCalculation["other_fee"]["cadastre_fee"];

        $require_payments_schedule_annually_and_summary[] =
            ["name" => "Վարկային հայտի ուսումնասիրության վճար (միանվագ)", "anually" => $loan_application_fee, "summary" => $loan_application_fee];

        $require_payments_schedule_annually_and_summary[] =
            ["name" => "Վարկի սպասարկման վճար (տարեկան)", "anually" => $loan_service_fee_yearly, "summary" => $loan_service_fee];

        $require_payments_schedule_annually_and_summary[] =
            ["name" => "Գրավի գնահատման վճար (միանվագ)", "anually" => $collateral_assessment_fee, "summary" => $collateral_assessment_fee];

        $require_payments_schedule_annually_and_summary[] =
            ["name" => "Կանխիկացման վճար", "anually" => $cash_service_fee, "summary" => $cash_service_fee];

        $require_payments_schedule_annually_and_summary[] =
            ["name" => "Գրավի ապահովագրության վճար (տարեկան)", "anually" => $collateral_insurance_fee_yearly, "summary" => $collateral_insurance_fee];

        $require_payments_schedule_annually_and_summary[] =
            ["name" => "Նոտարական վավերացման վճար (միանվագ)", "anually" => $notary_validation_fee, "summary" => $notary_validation_fee];

        $require_payments_schedule_annually_and_summary[] =
            ["name" => "Վարկառուի դժբախտ պատահարներից ապահովագրության վճար (տարեկան)", "anually" => $borrower_insurance_fee_yearly, "summary" => $borrower_insurance_fee];

        $require_payments_schedule_annually_and_summary[] =
            ["name" => "Կադաստրի հետ կապված վճար (միանվագ)", "anually" => $cadastre_fee, "summary" => $cadastre_fee];

        $require_payments_anually_sum = array_sum([$loan_application_fee, $loan_service_fee_yearly, $collateral_insurance_fee_yearly, $collateral_assessment_fee, $cash_service_fee, $notary_validation_fee, $borrower_insurance_fee_yearly, $cadastre_fee]);

        $require_payments_full_sum = array_sum([$loan_application_fee, $loan_service_fee, $collateral_assessment_fee, $cash_service_fee, $collateral_insurance_fee, $notary_validation_fee, $borrower_insurance_fee, $cadastre_fee]);


        if ($product->other_payments == 1) {
            foreach ($product->otherPayments as $key => $productCurrOtherPayment) {
                $other_payments_name = $productCurrOtherPayment->other_payments_name;

                $other_payments_sum_amount = 0;

                $other_payments_yearly_amount = 0;

                if ($productCurrOtherPayment->other_payments_periodicity == 1) {

                    $other_payments_sum_amount = @$getCalculation["other_fee"]["loan_other_onetime_fee_" . $key];

                    $other_payments_yearly_amount = @$getCalculation["other_fee"]["loan_other_onetime_fee_" . $key];
                } else if ($productCurrOtherPayment->other_payments_periodicity == 3) {

                    foreach ($getCalculation["schedule"] as $schedule_item) {

                        if (array_key_exists('loan_other_year_fee_' . $key, $schedule_item)) {

                            $other_payments_yearly_amount = $schedule_item['loan_other_year_fee_' . $key];

                            $other_payments_sum_amount += $schedule_item['loan_other_year_fee_' . $key];
                        }
                    }
                } else if ($productCurrOtherPayment->other_payments_periodicity == 2) {

                    foreach ($getCalculation["schedule"] as $schedule_item) {

                        if (array_key_exists('loan_other_mount_fee_' . $key, $schedule_item)) {

                            $other_payments_monthly_amount = $schedule_item['loan_other_mount_fee_' . $key];

                            $other_payments_sum_amount += $schedule_item['loan_other_mount_fee_' . $key];
                        }
                    }

                    $other_payments_yearly_amount = 12 * $other_payments_monthly_amount;
                }

                $require_payments_schedule_annually_and_summary[] =
                    ["name" => $other_payments_name, "anually" => $other_payments_yearly_amount, "summary" => $other_payments_sum_amount];

                $require_payments_anually_sum += $other_payments_yearly_amount;

                $require_payments_full_sum += $other_payments_sum_amount;
            }
        }

        $getCompareInfo = $this->getCompareInfo();

        $more_payment_amount_piece = round($more_payment_amount / $sum_payments, 2);

        $loan_amount_piece = round($loan_amount_converted / $sum_payments, 2);

        return view('product.mortgageloan', [
            "belonging_id" => $belonging_id,

            "belongings" => $belongings,

            "documents_list" => $documents_list,

            "cost" => $cost,

            "time_type" => $time_type,

            "term" => $term,

            "loan_term_search_in_days" => $loan_term_search_in_days,

            "loan_amount" => $loan_amount,

            "prepayment" => $prepayment,

            "loan_amount_converted" => $loan_amount_converted,

            "product" => $product,

            "product_variation" => $product_variation,

            "getCalculation" => $getCalculation,

            "require_payments_schedule_annually_and_summary" => $require_payments_schedule_annually_and_summary,

            "require_payments_anually_sum" => $require_payments_anually_sum,

            "require_payments_full_sum" => $require_payments_full_sum,

            "factual_percentage" => $factual_percentage,

            "require_payments" => $require_payments,

            "sum_payments" => $sum_payments,

            "more_payment_amount" => $more_payment_amount,

            "more_payment_amount_piece" => $more_payment_amount_piece,

            "loan_amount_piece" => $loan_amount_piece,

            "repayment_loan_interval_types" => $repayment_loan_interval_types,

            "repayment_percent_interval_types" => $repayment_percent_interval_types,

            "productPercentageTypesArr" => $productPercentageTypesArr,

            "getCompareInfo" => $getCompareInfo,
        ]);
    }

    /**
     * consumer loan Product page
     *
     * @return \Illuminate\Http\Response
     */
    function consumerLoanProduct($unique_options, $loan_amount, $time_type, $term, Request $request)
    {
        $belonging_id = 6;

        $belongings = Belonging::with('productsByBelongingInfo')->get();

        $documents_list = DocumentList::all();

        $product_variation = ProductsVariation::where(DB::raw("md5(unique_options)"), $unique_options)
            ->with('providingTypeInfo')->with('repaymentTypeInfo')->first();

        $product_id = $product_variation->product_id;

        $product = ConsumerCredit::where('id', $product_id)->with('companyInfo')->with('otherPayments')->first();

        $repayment_loan_interval_types = RepaymentLoanIntervalType::all();

        $repayment_percent_interval_types = RepaymentPercentIntervalType::all();

        $productPercentageTypesArr = ["1" => "+", "2" => "-", "3" => "±"];

        if ($time_type == 1 || $time_type == "" || is_null($time_type)) {
            $time_type = 1;

            $loan_term_search_in_days = $term;
        } else if ($time_type == 2) {

            $loan_term_search_in_days = $term * 30;
        } else if ($time_type == 3) {

            $loan_term_search_in_days = $term * 365;
        }

        if (intval($product->loan_pledge_ratio) == 0) {
            $cost = 0;
        } else {
            $cost = 100 * $loan_amount / $product->loan_pledge_ratio;
        }

        $getCalculation = $this->getCalculation($product, $product_variation, $cost, $loan_amount, $term, $loan_term_search_in_days, 0, $time_type);//calculate factual_percentage and other

        $require_payments_schedule_annually_and_summary = [];

        $factual_percentage = 100 * $getCalculation["xirr"];

        $factual_percentage = round($factual_percentage, 2); // rounding float 2 chars precision

        $require_payments = $getCalculation["require_payments"];

        $sum_payments = $getCalculation["sum_payments"];

        $more_payment_amount = $sum_payments - $loan_amount;


        $loan_application_fee = $getCalculation["other_fee"]["loan_application_fee"];

        /*calculate $loan_service_fee*/
        $loan_service_fee = 0;

        $loan_service_fee_yearly = 0;

        foreach ($getCalculation["schedule"] as $schedule_item) {
            if (array_key_exists('loan_service_fee', $schedule_item)) {

                $loan_service_fee_yearly = $schedule_item['loan_service_fee'];

                $loan_service_fee += $schedule_item['loan_service_fee'];
            }
        }
        /*calculate $loan_service_fee*/

        /*calculate $collateral_insurance_fee*/
        $collateral_insurance_fee = 0;

        $collateral_insurance_fee_yearly = 0;

        foreach ($getCalculation["schedule"] as $schedule_item) {
            if (array_key_exists('loan_service_fee', $schedule_item)) {

                $collateral_insurance_fee_yearly = $schedule_item['collateral_insurance_fee'];

                $collateral_insurance_fee += $schedule_item['collateral_insurance_fee'];
            }
        }
        /*calculate $collateral_insurance_fee*/

        $cash_service_fee = $getCalculation["other_fee"]["cash_service_fee"];

        $collateral_assessment_fee = $getCalculation["other_fee"]["collateral_assessment_fee"];

        $notary_validation_fee = $getCalculation["other_fee"]["notary_validation_fee"];

        $require_payments_schedule_annually_and_summary[] =
            ["name" => "Վարկային հայտի ուսումնասիրության վճար (միանվագ)", "anually" => $loan_application_fee, "summary" => $loan_application_fee];

        $require_payments_schedule_annually_and_summary[] =
            ["name" => "Վարկի սպասարկման վճար (տարեկան)", "anually" => $loan_service_fee_yearly, "summary" => $loan_service_fee];

        $require_payments_schedule_annually_and_summary[] =
            ["name" => "Գրավի գնահատման վճար (միանվագ)", "anually" => $collateral_assessment_fee, "summary" => $collateral_assessment_fee];

        $require_payments_schedule_annually_and_summary[] =
            ["name" => "Կանխիկացման վճար", "anually" => $cash_service_fee, "summary" => $cash_service_fee];

        $require_payments_schedule_annually_and_summary[] =
            ["name" => "Գրավի ապահովագրության վճար (տարեկան)", "anually" => $collateral_insurance_fee_yearly, "summary" => $collateral_insurance_fee];

        $require_payments_schedule_annually_and_summary[] =
            ["name" => "Նոտարական վավերացման վճար (միանվագ)", "anually" => $notary_validation_fee, "summary" => $notary_validation_fee];

        $require_payments_anually_sum = array_sum([$loan_application_fee, $loan_service_fee_yearly, $collateral_insurance_fee_yearly, $cash_service_fee, $collateral_assessment_fee, $notary_validation_fee]);

        $require_payments_full_sum = array_sum([$loan_application_fee, $loan_service_fee, $collateral_assessment_fee, $cash_service_fee, $collateral_insurance_fee, $notary_validation_fee]);

        if ($product->other_payments == 1) {
            foreach ($product->otherPayments as $key => $productCurrOtherPayment) {
                $other_payments_name = $productCurrOtherPayment->other_payments_name;

                $other_payments_sum_amount = 0;

                $other_payments_yearly_amount = 0;

                if ($productCurrOtherPayment->other_payments_periodicity == 1) {

                    $other_payments_sum_amount = @$getCalculation["other_fee"]["loan_other_onetime_fee_" . $key];

                    $other_payments_yearly_amount = @$getCalculation["other_fee"]["loan_other_onetime_fee_" . $key];
                } else if ($productCurrOtherPayment->other_payments_periodicity == 3) {

                    foreach ($getCalculation["schedule"] as $schedule_item) {

                        if (array_key_exists('loan_other_year_fee_' . $key, $schedule_item)) {

                            $other_payments_yearly_amount = $schedule_item['loan_other_year_fee_' . $key];

                            $other_payments_sum_amount += $schedule_item['loan_other_year_fee_' . $key];
                        }
                    }
                } else if ($productCurrOtherPayment->other_payments_periodicity == 2) {

                    foreach ($getCalculation["schedule"] as $schedule_item) {

                        if (array_key_exists('loan_other_mount_fee_' . $key, $schedule_item)) {

                            $other_payments_monthly_amount = $schedule_item['loan_other_mount_fee_' . $key];

                            $other_payments_sum_amount += $schedule_item['loan_other_mount_fee_' . $key];
                        }
                    }

                    $other_payments_yearly_amount = 12 * $other_payments_monthly_amount;
                }

                $require_payments_schedule_annually_and_summary[] =
                    ["name" => $other_payments_name, "anually" => $other_payments_yearly_amount, "summary" => $other_payments_sum_amount];

                $require_payments_anually_sum += $other_payments_yearly_amount;

                $require_payments_full_sum += $other_payments_sum_amount;
            }
        }

        $getCompareInfo = $this->getCompareInfo();

        $more_payment_amount_piece = round($more_payment_amount / $sum_payments, 2);

        $loan_amount_piece = round($loan_amount / $sum_payments, 2);

        return view('product.consumerloan', [
            "belonging_id" => $belonging_id,

            "belongings" => $belongings,

            "documents_list" => $documents_list,

            "cost" => $cost,

            "time_type" => $time_type,

            "term" => $term,

            "loan_term_search_in_days" => $loan_term_search_in_days,

            "loan_amount" => $loan_amount,

            "product" => $product,

            "product_variation" => $product_variation,

            "getCalculation" => $getCalculation,

            "require_payments_schedule_annually_and_summary" => $require_payments_schedule_annually_and_summary,

            "require_payments_anually_sum" => $require_payments_anually_sum,

            "require_payments_full_sum" => $require_payments_full_sum,

            "factual_percentage" => $factual_percentage,

            "require_payments" => $require_payments,

            "sum_payments" => $sum_payments,

            "more_payment_amount" => $more_payment_amount,

            "more_payment_amount_piece" => $more_payment_amount_piece,

            "loan_amount_piece" => $loan_amount_piece,

            "repayment_loan_interval_types" => $repayment_loan_interval_types,

            "repayment_percent_interval_types" => $repayment_percent_interval_types,

            "productPercentageTypesArr" => $productPercentageTypesArr,

            "getCompareInfo" => $getCompareInfo,
        ]);
    }

    /**
     * online loan Product page
     *
     * @return \Illuminate\Http\Response
     */
    function onlineLoanProduct($unique_options, $loan_amount, $time_type, $term, Request $request)
    {
        $belonging_id = 13;

        $belongings = Belonging::with('productsByBelongingInfo')->get();

        $documents_list = DocumentList::all();

        $product_variation = ProductsVariation::where(DB::raw("md5(unique_options)"), $unique_options)
            ->with('providingTypeInfo')->with('repaymentTypeInfo')->first();

        $product_id = $product_variation->product_id;

        $product = OnlineLoan::where('id', $product_id)->with('companyInfo')->with('otherPayments')->first();

        $repayment_loan_interval_types = RepaymentLoanIntervalType::all();

        $repayment_percent_interval_types = RepaymentPercentIntervalType::all();

        $productPercentageTypesArr = ["1" => "+", "2" => "-", "3" => "±"];

        if ($time_type == 1 || $time_type == "" || is_null($time_type)) {
            $time_type = 1;

            $loan_term_search_in_days = $term;
        } else if ($time_type == 2) {

            $loan_term_search_in_days = $term * 30;
        } else if ($time_type == 3) {

            $loan_term_search_in_days = $term * 365;
        }

        if (intval($product->loan_pledge_ratio) == 0) {
            $cost = 0;
        } else {
            $cost = 100 * $loan_amount / $product->loan_pledge_ratio;
        }

        $getCalculation = $this->getCalculation($product, $product_variation, $cost, $loan_amount, $term, $loan_term_search_in_days, 0, $time_type);//calculate factual_percentage and other

        $require_payments_schedule_annually_and_summary = [];

        $factual_percentage = 100 * $getCalculation["xirr"];

        $factual_percentage = round($factual_percentage, 2); // rounding float 2 chars precision

        $require_payments = $getCalculation["require_payments"];

        $sum_payments = $getCalculation["sum_payments"];

        $more_payment_amount = $sum_payments - $loan_amount;


        $loan_application_fee = $getCalculation["other_fee"]["loan_application_fee"];

        /*calculate $loan_service_fee*/
        $loan_service_fee = 0;

        $loan_service_fee_yearly = 0;

        foreach ($getCalculation["schedule"] as $schedule_item) {
            if (array_key_exists('loan_service_fee', $schedule_item)) {

                $loan_service_fee_yearly = $schedule_item['loan_service_fee'];

                $loan_service_fee += $schedule_item['loan_service_fee'];
            }
        }
        /*calculate $loan_service_fee*/

        /*calculate $collateral_insurance_fee*/
        $collateral_insurance_fee = 0;

        $collateral_insurance_fee_yearly = 0;

        foreach ($getCalculation["schedule"] as $schedule_item) {
            if (array_key_exists('loan_service_fee', $schedule_item)) {

                $collateral_insurance_fee_yearly = $schedule_item['collateral_insurance_fee'];

                $collateral_insurance_fee += $schedule_item['collateral_insurance_fee'];
            }
        }
        /*calculate $collateral_insurance_fee*/

        $collateral_assessment_fee = $getCalculation["other_fee"]["collateral_assessment_fee"];

        $notary_validation_fee = $getCalculation["other_fee"]["notary_validation_fee"];

        $require_payments_schedule_annually_and_summary[] =
            ["name" => "Վարկային հայտի ուսումնասիրության վճար (միանվագ)", "anually" => $loan_application_fee, "summary" => $loan_application_fee];

        $require_payments_schedule_annually_and_summary[] =
            ["name" => "Վարկի սպասարկման վճար (տարեկան)", "anually" => $loan_service_fee_yearly, "summary" => $loan_service_fee];

        $require_payments_schedule_annually_and_summary[] =
            ["name" => "Գրավի գնահատման վճար (միանվագ)", "anually" => $collateral_assessment_fee, "summary" => $collateral_assessment_fee];

        $require_payments_schedule_annually_and_summary[] =
            ["name" => "Գրավի ապահովագրության վճար (տարեկան)", "anually" => $collateral_insurance_fee_yearly, "summary" => $collateral_insurance_fee];

        $require_payments_schedule_annually_and_summary[] =
            ["name" => "Նոտարական վավերացման վճար (միանվագ)", "anually" => $notary_validation_fee, "summary" => $notary_validation_fee];

        $require_payments_anually_sum = array_sum([$loan_application_fee, $loan_service_fee_yearly, $collateral_insurance_fee_yearly, $collateral_assessment_fee, $notary_validation_fee]);

        $require_payments_full_sum = array_sum([$loan_application_fee, $loan_service_fee, $collateral_assessment_fee, $collateral_insurance_fee, $notary_validation_fee]);

        if ($product->other_payments == 1) {
            foreach ($product->otherPayments as $key => $productCurrOtherPayment) {
                $other_payments_name = $productCurrOtherPayment->other_payments_name;

                $other_payments_sum_amount = 0;

                $other_payments_yearly_amount = 0;

                if ($productCurrOtherPayment->other_payments_periodicity == 1) {

                    $other_payments_sum_amount = @$getCalculation["other_fee"]["loan_other_onetime_fee_" . $key];

                    $other_payments_yearly_amount = @$getCalculation["other_fee"]["loan_other_onetime_fee_" . $key];
                } else if ($productCurrOtherPayment->other_payments_periodicity == 3) {

                    foreach ($getCalculation["schedule"] as $schedule_item) {

                        if (array_key_exists('loan_other_year_fee_' . $key, $schedule_item)) {

                            $other_payments_yearly_amount = $schedule_item['loan_other_year_fee_' . $key];

                            $other_payments_sum_amount += $schedule_item['loan_other_year_fee_' . $key];
                        }
                    }
                } else if ($productCurrOtherPayment->other_payments_periodicity == 2) {

                    foreach ($getCalculation["schedule"] as $schedule_item) {

                        if (array_key_exists('loan_other_mount_fee_' . $key, $schedule_item)) {

                            $other_payments_monthly_amount = $schedule_item['loan_other_mount_fee_' . $key];

                            $other_payments_sum_amount += $schedule_item['loan_other_mount_fee_' . $key];
                        }
                    }

                    $other_payments_yearly_amount = 12 * $other_payments_monthly_amount;
                }

                $require_payments_schedule_annually_and_summary[] =
                    ["name" => $other_payments_name, "anually" => $other_payments_yearly_amount, "summary" => $other_payments_sum_amount];

                $require_payments_anually_sum += $other_payments_yearly_amount;

                $require_payments_full_sum += $other_payments_sum_amount;
            }
        }

        $getCompareInfo = $this->getCompareInfo();

        $more_payment_amount_piece = round($more_payment_amount / $sum_payments, 2);

        $loan_amount_piece = round($loan_amount / $sum_payments, 2);

        return view('product.onlineloan', [
            "belonging_id" => $belonging_id,

            "belongings" => $belongings,

            "documents_list" => $documents_list,

            "cost" => $cost,

            "time_type" => $time_type,

            "term" => $term,

            "loan_term_search_in_days" => $loan_term_search_in_days,

            "loan_amount" => $loan_amount,

            "product" => $product,

            "product_variation" => $product_variation,

            "getCalculation" => $getCalculation,

            "require_payments_schedule_annually_and_summary" => $require_payments_schedule_annually_and_summary,

            "require_payments_anually_sum" => $require_payments_anually_sum,

            "require_payments_full_sum" => $require_payments_full_sum,

            "factual_percentage" => $factual_percentage,

            "require_payments" => $require_payments,

            "sum_payments" => $sum_payments,

            "more_payment_amount" => $more_payment_amount,

            "more_payment_amount_piece" => $more_payment_amount_piece,

            "loan_amount_piece" => $loan_amount_piece,

            "repayment_loan_interval_types" => $repayment_loan_interval_types,

            "repayment_percent_interval_types" => $repayment_percent_interval_types,

            "productPercentageTypesArr" => $productPercentageTypesArr,

            "getCompareInfo" => $getCompareInfo,
        ]);
    }

    /**
     * travel insuranceProduct page
     *
     * @return \Illuminate\Http\Response
     */
    function travelInsuranceLoanProduct($product_id, $age, $term, $currency, $country, Request $request)
    {
        $belonging_id = 12;

        $belongings = Belonging::with('productsByBelongingInfo')->get();

        $documents_list = DocumentList::all();

        $yes_no_answers = YesNo::all();

        $countries = Country::all();

        $loanCurrenciesTypes = LoanCurrenciesType::all();

        $product_variation = TravelInsurancesVariation::where('currency', $currency)
            ->where('product_id', $product_id)->where('travel_age_from', '<=', (int)$age)->where('travel_age_to', '>=', (int)$age)
            ->where('travel_insurance_term_from', '<=', (int)$term)->where('travel_insurance_term_to', '>=', (int)$term)->first();

        $product = TravelInsurance::where('id', $product_id)->with('companyInfo')->first();

        $calcTravelInsuranceFee = $this->calcTravelInsuranceFee($product_variation->travel_insurance_amount, $currency,
            $product_variation->travel_insurance_tariff_amount, $product_variation->travel_insurance_percent,
            $product_variation->term_coefficient, $product_variation->travel_age_coefficient);

        $insurance_fee = number_format(round($calcTravelInsuranceFee), 0, ",", " ");

        $getCompareInfo = $this->getCompareInfo();

        return view('product.travelinsurance', [
            "belonging_id" => $belonging_id,

            "belongings" => $belongings,

            "documents_list" => $documents_list,

            "yes_no_answers" => $yes_no_answers,

            "countries" => $countries,

            "loanCurrenciesTypes" => $loanCurrenciesTypes,

            "term" => $term,

            "age" => $age,

            "currency" => $currency,

            "country" => $country,

            "product" => $product,

            "product_variation" => $product_variation,

            "insurance_fee" => $insurance_fee,

            "getCompareInfo" => $getCompareInfo,
        ]);
    }

    /**
     * online loan Refinancing Product page
     *
     * @return \Illuminate\Http\Response
     */
    function loanRefinancingProduct($unique_options, $loan_amount, $time_type, $term, Request $request)
    {
        $belonging_id = 11;

        $belongings = Belonging::with('productsByBelongingInfo')->get();

        $documents_list = DocumentList::all();

        $product_variation = ProductsVariation::where(DB::raw("md5(unique_options)"), $unique_options)
            ->with('providingTypeInfo')->with('repaymentTypeInfo')->first();

        $product_id = $product_variation->product_id;

        $product = LoanRefinancing::where('id', $product_id)->with('companyInfo')->with('otherPayments')->first();

        $repayment_loan_interval_types = RepaymentLoanIntervalType::all();

        $repayment_percent_interval_types = RepaymentPercentIntervalType::all();

        $productPercentageTypesArr = ["1" => "+", "2" => "-", "3" => "±"];

        if ($time_type == 1 || $time_type == "" || is_null($time_type)) {
            $time_type = 1;

            $loan_term_search_in_days = $term;
        } else if ($time_type == 2) {

            $loan_term_search_in_days = $term * 30;
        } else if ($time_type == 3) {

            $loan_term_search_in_days = $term * 365;
        }

        if (intval($product->loan_pledge_ratio) == 0) {
            $cost = 0;
        } else {
            $cost = 100 * $loan_amount / $product->loan_pledge_ratio;
        }

        $getCalculation = $this->getCalculation($product, $product_variation, $cost, $loan_amount, $term, $loan_term_search_in_days, 0, $time_type);//calculate factual_percentage and other

        $require_payments_schedule_annually_and_summary = [];

        $factual_percentage = 100 * $getCalculation["xirr"];

        $factual_percentage = round($factual_percentage, 2); // rounding float 2 chars precision

        $require_payments = $getCalculation["require_payments"];

        $sum_payments = $getCalculation["sum_payments"];

        $more_payment_amount = $sum_payments - $loan_amount;


        $loan_application_fee = $getCalculation["other_fee"]["loan_application_fee"];

        /*calculate $loan_service_fee*/
        $loan_service_fee = 0;

        $loan_service_fee_yearly = 0;

        foreach ($getCalculation["schedule"] as $schedule_item) {
            if (array_key_exists('loan_service_fee', $schedule_item)) {

                $loan_service_fee_yearly = $schedule_item['loan_service_fee'];

                $loan_service_fee += $schedule_item['loan_service_fee'];
            }
        }
        /*calculate $loan_service_fee*/

        /*calculate $collateral_insurance_fee*/
        $collateral_insurance_fee = 0;

        $collateral_insurance_fee_yearly = 0;

        foreach ($getCalculation["schedule"] as $schedule_item) {
            if (array_key_exists('loan_service_fee', $schedule_item)) {

                $collateral_insurance_fee_yearly = $schedule_item['collateral_insurance_fee'];

                $collateral_insurance_fee += $schedule_item['collateral_insurance_fee'];
            }
        }
        /*calculate $collateral_insurance_fee*/

        $collateral_assessment_fee = $getCalculation["other_fee"]["collateral_assessment_fee"];

        $notary_validation_fee = $getCalculation["other_fee"]["notary_validation_fee"];

        $require_payments_schedule_annually_and_summary[] =
            ["name" => "Վարկային հայտի ուսումնասիրության վճար (միանվագ)", "anually" => $loan_application_fee, "summary" => $loan_application_fee];

        $require_payments_schedule_annually_and_summary[] =
            ["name" => "Վարկի սպասարկման վճար (տարեկան)", "anually" => $loan_service_fee_yearly, "summary" => $loan_service_fee];

        $require_payments_schedule_annually_and_summary[] =
            ["name" => "Գրավի գնահատման վճար (միանվագ)", "anually" => $collateral_assessment_fee, "summary" => $collateral_assessment_fee];

        $require_payments_schedule_annually_and_summary[] =
            ["name" => "Գրավի ապահովագրության վճար (տարեկան)", "anually" => $collateral_insurance_fee_yearly, "summary" => $collateral_insurance_fee];

        $require_payments_schedule_annually_and_summary[] =
            ["name" => "Նոտարական վավերացման վճար (միանվագ)", "anually" => $notary_validation_fee, "summary" => $notary_validation_fee];

        $require_payments_anually_sum = array_sum([$loan_application_fee, $loan_service_fee_yearly, $collateral_insurance_fee_yearly, $collateral_assessment_fee, $notary_validation_fee]);

        $require_payments_full_sum = array_sum([$loan_application_fee, $loan_service_fee, $collateral_assessment_fee, $collateral_insurance_fee, $notary_validation_fee]);

        if ($product->other_payments == 1) {
            foreach ($product->otherPayments as $key => $productCurrOtherPayment) {
                $other_payments_name = $productCurrOtherPayment->other_payments_name;

                $other_payments_sum_amount = 0;

                $other_payments_yearly_amount = 0;

                if ($productCurrOtherPayment->other_payments_periodicity == 1) {

                    $other_payments_sum_amount = @$getCalculation["other_fee"]["loan_other_onetime_fee_" . $key];

                    $other_payments_yearly_amount = @$getCalculation["other_fee"]["loan_other_onetime_fee_" . $key];
                } else if ($productCurrOtherPayment->other_payments_periodicity == 3) {

                    foreach ($getCalculation["schedule"] as $schedule_item) {

                        if (array_key_exists('loan_other_year_fee_' . $key, $schedule_item)) {

                            $other_payments_yearly_amount = $schedule_item['loan_other_year_fee_' . $key];

                            $other_payments_sum_amount += $schedule_item['loan_other_year_fee_' . $key];
                        }
                    }
                } else if ($productCurrOtherPayment->other_payments_periodicity == 2) {

                    foreach ($getCalculation["schedule"] as $schedule_item) {

                        if (array_key_exists('loan_other_mount_fee_' . $key, $schedule_item)) {

                            $other_payments_monthly_amount = $schedule_item['loan_other_mount_fee_' . $key];

                            $other_payments_sum_amount += $schedule_item['loan_other_mount_fee_' . $key];
                        }
                    }

                    $other_payments_yearly_amount = 12 * $other_payments_monthly_amount;
                }

                $require_payments_schedule_annually_and_summary[] =
                    ["name" => $other_payments_name, "anually" => $other_payments_yearly_amount, "summary" => $other_payments_sum_amount];

                $require_payments_anually_sum += $other_payments_yearly_amount;

                $require_payments_full_sum += $other_payments_sum_amount;
            }
        }

        $getCompareInfo = $this->getCompareInfo();

        $more_payment_amount_piece = round($more_payment_amount / $sum_payments, 2);

        $loan_amount_piece = round($loan_amount / $sum_payments, 2);

        return view('product.loanrefinancing', [
            "belonging_id" => $belonging_id,

            "belongings" => $belongings,

            "documents_list" => $documents_list,

            "cost" => $cost,

            "time_type" => $time_type,

            "term" => $term,

            "loan_term_search_in_days" => $loan_term_search_in_days,

            "loan_amount" => $loan_amount,

            "product" => $product,

            "product_variation" => $product_variation,

            "getCalculation" => $getCalculation,

            "require_payments_schedule_annually_and_summary" => $require_payments_schedule_annually_and_summary,

            "require_payments_anually_sum" => $require_payments_anually_sum,

            "require_payments_full_sum" => $require_payments_full_sum,

            "factual_percentage" => $factual_percentage,

            "require_payments" => $require_payments,

            "sum_payments" => $sum_payments,

            "more_payment_amount" => $more_payment_amount,

            "more_payment_amount_piece" => $more_payment_amount_piece,

            "loan_amount_piece" => $loan_amount_piece,

            "repayment_loan_interval_types" => $repayment_loan_interval_types,

            "repayment_percent_interval_types" => $repayment_percent_interval_types,

            "productPercentageTypesArr" => $productPercentageTypesArr,

            "getCompareInfo" => $getCompareInfo,
        ]);
    }
}