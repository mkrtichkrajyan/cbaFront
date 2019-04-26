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
use App\Models\LoanCurrenciesType;
use App\Models\LoanRefinancing;
use App\Models\LoanServicePayTypes;
use App\Models\MoneyTransfer;
use App\Models\Mortgage;
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
use App\Models\TravelInsurancesVariation;
use App\Models\YesNo;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use Illuminate\Database\Eloquent\Model;

trait ProductsCompareInnerTrait
{
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

            $getCalculation = $this->getCalculation($curr_data["product_info"], $curr_data["variation_info"], $compareVariation["cost"], $curr_data["loan_amount"], $curr_data["term"], $curr_data["term"], 0, 1);//calculate factual_percentage and other

            $factual_percentage = 100 * $getCalculation["xirr"];

            $curr_data["factual_percentage"] = $factual_percentage;

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
     * compare compare Credit Loans Inner page.
     *
     * @return \Illuminate\Http\Response
     */
    public
    function compareCreditsInner(Request $request)
    {
        $belongings = Belonging::with('productsByBelongingInfo')->get();

        $belonging_id = 3;

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

            $curr_data["product_info"] = CreditLoan::with('companyInfo')->with('otherPayments')
                ->with('sellSalons')->find($compareVariation["product_id"]);

            $curr_data["variation_info"] = ProductsVariation::where(DB::raw("md5(unique_options)"), $compareVariation["curr_variation_options"])->first();

            $curr_data["cost"] = $compareVariation["cost"];

            $curr_data["prepayment"] = $compareVariation["prepayment"];

            $curr_data["term"] = $compareVariation["term"];


            $curr_data["loan_amount"] = intval($compareVariation["cost"]) - intval($curr_data["prepayment"]);

            $getCalculation = $this->getCalculation($curr_data["product_info"], $curr_data["variation_info"], $compareVariation["cost"], $curr_data["loan_amount"], $curr_data["term"], $curr_data["term"], 0, 1);//calculate factual_percentage and other

            $factual_percentage = 100 * $getCalculation["xirr"];

            $curr_data["factual_percentage"] = $factual_percentage;

            $compareVariationsData[] = $curr_data;
        }
//dd($compareVariationsData);
        return view('compare.inner.compareCreditsInner',
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
     * compare compare Gold Loans Inner page.
     *
     * @return \Illuminate\Http\Response
     */
    public
    function compareGoldLoansInner(Request $request)
    {
        $belongings = Belonging::with('productsByBelongingInfo')->get();

        $belonging_id = 2;

        $currProductByBelongingsView = ProductByBelongingsView::where("belonging_id", $belonging_id)->first();

        $productPercentageTypesArr = ["1" => "+", "2" => "-", "3" => "±"];

        $currBelonging = Belonging::where("id", $belonging_id)->first();

        $gold_assay_types = GoldAssayType::all();

        $time_types = TimeType::all();

        $repayment_types = RepaymentType::all();

        $repayment_loan_interval_types = RepaymentLoanIntervalType::all();

        $repayment_percent_interval_types = RepaymentPercentIntervalType::all();

        $providing_types = ProvidingType::all();

        $percentage_types = PercentageType::all();

        $loan_service_pay_types = LoanServicePayTypes::all();

        $absolute_amount_or_percent_only_pay_types = AbsoluteAmountORPercentOnlyPayType::all();

        $absolute_amount_or_percent_pay_types = AbsoluteAmountOrPercentPayType::all();

        $periodicity_types = PeriodicityType::all();

        $previous_url = $currBelonging->productsByBelongingInfo->first()->compare_url;

        $getCompareInfo = $this->getCompareInfo();

        $compareVariations = $getCompareInfo[$belonging_id]["checked_variations_full_info"];

        $compareVariationsData = [];

        foreach ($compareVariations as $key => $compareVariation) {

            $curr_data = [];

            $curr_data["product_info"] = GoldLoan::with('companyInfo')->with('goldPledgeTypeInfo')->with('goldAssayTypes')->with('goldLoanAssessmentAssaysValues')->with('otherPayments')->find($compareVariation["product_id"]);

            $curr_data["variation_info"] = ProductsVariation::where(DB::raw("md5(unique_options)"), $compareVariation["curr_variation_options"])->first();

//            $cost = 100 * $curr_data["product_info"]->loan_amount / $curr_data["product_info"]->loan_pledge_ratio;

            $curr_data["term"] = $compareVariation["term"];


            $curr_data["loan_amount"] = intval($compareVariation["loan_amount"]);

            if (intval($curr_data["product_info"]->loan_pledge_ratio) == 0) {
                $cost = 0;
            } else {
                $cost = 100 * $curr_data["loan_amount"] / $curr_data["product_info"]->loan_pledge_ratio;
            }

            $getCalculation = $this->getCalculation($curr_data["product_info"], $curr_data["variation_info"], $cost, $curr_data["loan_amount"], $curr_data["term"], $curr_data["term"], 0, 1);//calculate factual_percentage and other

            $factual_percentage = 100 * $getCalculation["xirr"];

            $curr_data["factual_percentage"] = $factual_percentage;

            if (!is_null($curr_data["variation_info"])) {
                $compareVariationsData[] = $curr_data;
            }
        }

        return view('compare.inner.compareGoldLoansInner',
            [
                "belongings" => $belongings,

                "belonging_id" => $belonging_id,

                "currProductByBelongingsView" => $currProductByBelongingsView,

                "currBelonging" => $currBelonging,

                "productPercentageTypesArr" => $productPercentageTypesArr,

                "gold_assay_types" => $gold_assay_types,

                "time_types" => $time_types,

                "repayment_types" => $repayment_types,

                "repayment_loan_interval_types" => $repayment_loan_interval_types,

                "repayment_percent_interval_types" => $repayment_percent_interval_types,

                "providing_types" => $providing_types,

                "percentage_types" => $percentage_types,

                "loan_service_pay_types" => $loan_service_pay_types,

                "periodicity_types" => $periodicity_types,

                "absolute_amount_or_percent_pay_types" => $absolute_amount_or_percent_pay_types,

                "absolute_amount_or_percent_only_pay_types" => $absolute_amount_or_percent_only_pay_types,

                "previous_url" => $previous_url,

                "getCompareInfo" => $getCompareInfo,

                "compareVariationsData" => $compareVariationsData,
            ]);
    }

    /**
     * compare Student Loans Inner page.
     *
     * @return \Illuminate\Http\Response
     */
    public
    function compareStudentLoansInner(Request $request)
    {
        $belongings = Belonging::with('productsByBelongingInfo')->get();

        $belonging_id = 4;

        $currProductByBelongingsView = ProductByBelongingsView::where("belonging_id", $belonging_id)->first();

        $productPercentageTypesArr = ["1" => "+", "2" => "-", "3" => "±"];

        $currBelonging = Belonging::where("id", $belonging_id)->first();

        $time_types = TimeType::all();

        $repayment_types = RepaymentType::all();

        $repayment_loan_interval_types = RepaymentLoanIntervalType::all();

        $repayment_percent_interval_types = RepaymentPercentIntervalType::all();

        $providing_types = ProvidingType::all();

        $percentage_types = PercentageType::all();

        $loan_service_pay_types = LoanServicePayTypes::all();

        $absolute_amount_or_percent_only_pay_types = AbsoluteAmountORPercentOnlyPayType::all();

        $absolute_amount_or_percent_pay_types = AbsoluteAmountOrPercentPayType::all();

        $periodicity_types = PeriodicityType::all();

        $previous_url = $currBelonging->productsByBelongingInfo->first()->compare_url;

        $getCompareInfo = $this->getCompareInfo();

        $compareVariations = $getCompareInfo[$belonging_id]["checked_variations_full_info"];

        $compareVariationsData = [];

        foreach ($compareVariations as $key => $compareVariation) {

            $curr_data = [];

            $curr_data["product_info"] = StudentLoan::with('companyInfo')->with('otherPayments')->find($compareVariation["product_id"]);

            $curr_data["variation_info"] = ProductsVariation::where(DB::raw("md5(unique_options)"), $compareVariation["curr_variation_options"])->first();

            $curr_data["term"] = $compareVariation["term"];


            $curr_data["loan_amount"] = intval($compareVariation["loan_amount"]);


            if (intval($curr_data["product_info"]->loan_pledge_ratio) == 0) {
                $cost = 0;
            } else {
                $cost = 100 * $curr_data["loan_amount"] / $curr_data["product_info"]->loan_pledge_ratio;
            }

            $getCalculation = $this->getCalculation($curr_data["product_info"], $curr_data["variation_info"], $cost, $curr_data["loan_amount"], $curr_data["term"], $curr_data["term"], 0, 1);//calculate factual_percentage and other

            $factual_percentage = 100 * $getCalculation["xirr"];

            $curr_data["factual_percentage"] = $factual_percentage;

            if (!is_null($curr_data["variation_info"])) {
                $compareVariationsData[] = $curr_data;
            }
        }

        return view('compare.inner.compareStudentLoansInner',
            [
                "belongings" => $belongings,

                "belonging_id" => $belonging_id,

                "currProductByBelongingsView" => $currProductByBelongingsView,

                "currBelonging" => $currBelonging,

                "productPercentageTypesArr" => $productPercentageTypesArr,

                "time_types" => $time_types,

                "repayment_types" => $repayment_types,

                "repayment_loan_interval_types" => $repayment_loan_interval_types,

                "repayment_percent_interval_types" => $repayment_percent_interval_types,

                "providing_types" => $providing_types,

                "percentage_types" => $percentage_types,

                "loan_service_pay_types" => $loan_service_pay_types,

                "periodicity_types" => $periodicity_types,

                "absolute_amount_or_percent_pay_types" => $absolute_amount_or_percent_pay_types,

                "absolute_amount_or_percent_only_pay_types" => $absolute_amount_or_percent_only_pay_types,

                "previous_url" => $previous_url,

                "getCompareInfo" => $getCompareInfo,

                "compareVariationsData" => $compareVariationsData,
            ]);
    }

    /**
     * compare Travel Insurances Inner Inner page.
     *
     * @return \Illuminate\Http\Response
     */
    public
    function compareTravelInsurancesInner(Request $request)
    {
        $belongings = Belonging::with('productsByBelongingInfo')->get();

        $belonging_id = 12;

        $currProductByBelongingsView = ProductByBelongingsView::where("belonging_id", $belonging_id)->first();

        $currBelonging = Belonging::where("id", $belonging_id)->first();

        $yes_no_answers = YesNo::all();

        $countries = Country::all();

        $loanCurrenciesTypes = LoanCurrenciesType::all();

        $previous_url = $currBelonging->productsByBelongingInfo->first()->compare_url;

        $getCompareInfo = $this->getCompareInfo();

        $compareVariations = $getCompareInfo[$belonging_id]["checked_variations_full_info"];

        $compareVariationsData = [];
//dd($compareVariations);
        foreach ($compareVariations as $key => $compareVariation) {

            $curr_data = [];

            $curr_data["product_info"] = TravelInsurance::with('companyInfo')->find($compareVariation["product_id"]);


            $curr_data["term"] = $compareVariation["term"];

            $curr_data["age"] = $compareVariation["age"];

            $curr_data["currency"] = $compareVariation["currency"];

            $curr_data["country"] = $compareVariation["country"];


            $curr_data["variation_info"] = TravelInsurancesVariation::where('currency', $compareVariation["currency"])
                    ->where('product_id', $compareVariation["product_id"])->where('travel_age_from', '<=', (int)$curr_data["age"])->where('travel_age_to', '>=', (int)$curr_data["age"])
                ->where('travel_insurance_term_from', '<=', (int)$compareVariation["term"])->where('travel_insurance_term_to', '>=', (int)$compareVariation["term"])->first();

            if (!is_null($curr_data["variation_info"])) {
                $calcTravelInsuranceFee = $this->calcTravelInsuranceFee($curr_data["variation_info"]->travel_insurance_amount, $compareVariation["currency"],
                    $curr_data["variation_info"]->travel_insurance_tariff_amount, $curr_data["variation_info"]->travel_insurance_percent,
                    $curr_data["variation_info"]->term_coefficient, $curr_data["variation_info"]->travel_age_coefficient);

                $curr_data["insurance_fee"] =  number_format(round($calcTravelInsuranceFee), 0, ",", " ");

                $compareVariationsData[] = $curr_data;
            }
        }

        return view('compare.inner.compareTravelInsurancesInner',
            [
                "belongings" => $belongings,

                "belonging_id" => $belonging_id,

                "currProductByBelongingsView" => $currProductByBelongingsView,

                "currBelonging" => $currBelonging,

                "yes_no_answers" => $yes_no_answers,

                "countries" => $countries,

                "previous_url" => $previous_url,

                "getCompareInfo" => $getCompareInfo,

                "loanCurrenciesTypes" => $loanCurrenciesTypes,

                "compareVariationsData" => $compareVariationsData,
            ]);
    }

    /**
     * compare Agric Loans Inner page.
     *
     * @return \Illuminate\Http\Response
     */
    public
    function compareAgricLoansInner(Request $request)
    {
        $belongings = Belonging::with('productsByBelongingInfo')->get();

        $belonging_id = 5;

        $currProductByBelongingsView = ProductByBelongingsView::where("belonging_id", $belonging_id)->first();

        $productPercentageTypesArr = ["1" => "+", "2" => "-", "3" => "±"];

        $currBelonging = Belonging::where("id", $belonging_id)->first();

        $time_types = TimeType::all();

        $repayment_types = RepaymentType::all();

        $repayment_loan_interval_types = RepaymentLoanIntervalType::all();

        $repayment_percent_interval_types = RepaymentPercentIntervalType::all();

        $providing_types = ProvidingType::all();

        $percentage_types = PercentageType::all();

        $loan_service_pay_types = LoanServicePayTypes::all();

        $absolute_amount_or_percent_only_pay_types = AbsoluteAmountORPercentOnlyPayType::all();

        $absolute_amount_or_percent_pay_types = AbsoluteAmountOrPercentPayType::all();

        $periodicity_types = PeriodicityType::all();

        $previous_url = $currBelonging->productsByBelongingInfo->first()->compare_url;

        $getCompareInfo = $this->getCompareInfo();
//        dd($getCompareInfo);
        $compareVariations = $getCompareInfo[$belonging_id]["checked_variations_full_info"];
//        dd($compareVariations);
        $compareVariationsData = [];

        foreach ($compareVariations as $key => $compareVariation) {

            $curr_data = [];

            $curr_data["product_info"] = AgricLoan::with('companyInfo')->with('otherPayments')->with('currencyInfo')->find($compareVariation["product_id"]);

            $curr_data["variation_info"] = ProductsVariation::where(DB::raw("md5(unique_options)"), $compareVariation["curr_variation_options"])->first();

            $curr_data["term"] = $compareVariation["term"];


            $curr_data["loan_amount"] = intval($compareVariation["loan_amount"]);

            $loan_amount_converted = $this->getLoanAmountConverted($curr_data["product_info"]->currency, $compareVariation["loan_amount"]);


            if (intval($curr_data["product_info"]->loan_pledge_ratio) == 0) {
                $cost = 0;
            } else {
                $cost = 100 * $loan_amount_converted / $curr_data["product_info"]->loan_pledge_ratio;
            }

            $getCalculation = $this->getCalculation($curr_data["product_info"], $curr_data["variation_info"], $cost, $loan_amount_converted, $curr_data["term"], $curr_data["term"], 0, 1);//calculate factual_percentage and other

            $factual_percentage = 100 * $getCalculation["xirr"];

            $curr_data["factual_percentage"] = $factual_percentage;

            if (!is_null($curr_data["variation_info"])) {
                $compareVariationsData[] = $curr_data;
            }
        }

        return view('compare.inner.compareAgricLoansInner',
            [
                "belongings" => $belongings,

                "belonging_id" => $belonging_id,

                "currProductByBelongingsView" => $currProductByBelongingsView,

                "currBelonging" => $currBelonging,

                "productPercentageTypesArr" => $productPercentageTypesArr,

                "time_types" => $time_types,

                "repayment_types" => $repayment_types,

                "repayment_loan_interval_types" => $repayment_loan_interval_types,

                "repayment_percent_interval_types" => $repayment_percent_interval_types,

                "providing_types" => $providing_types,

                "percentage_types" => $percentage_types,

                "loan_service_pay_types" => $loan_service_pay_types,

                "periodicity_types" => $periodicity_types,

                "absolute_amount_or_percent_pay_types" => $absolute_amount_or_percent_pay_types,

                "absolute_amount_or_percent_only_pay_types" => $absolute_amount_or_percent_only_pay_types,

                "previous_url" => $previous_url,

                "getCompareInfo" => $getCompareInfo,

                "compareVariationsData" => $compareVariationsData,
            ]);
    }

    /**
     * compare Mortgage Inner page.
     *
     * @return \Illuminate\Http\Response
     */
    public
    function compareMortgagesInner(Request $request)
    {
        $belongings = Belonging::with('productsByBelongingInfo')->get();

        $belonging_id = 8;

        $currProductByBelongingsView = ProductByBelongingsView::where("belonging_id", $belonging_id)->first();

        $productPercentageTypesArr = ["1" => "+", "2" => "-", "3" => "±"];

        $currBelonging = Belonging::where("id", $belonging_id)->first();

        $time_types = TimeType::all();

        $loanCurrenciesTypes = LoanCurrenciesType::all();

        $repayment_types = RepaymentType::all();

        $repayment_loan_interval_types = RepaymentLoanIntervalType::all();

        $repayment_percent_interval_types = RepaymentPercentIntervalType::all();

        $providing_types = ProvidingType::all();

        $percentage_types = PercentageType::all();

        $loan_service_pay_types = LoanServicePayTypes::all();

        $absolute_amount_or_percent_only_pay_types = AbsoluteAmountORPercentOnlyPayType::all();

        $absolute_amount_or_percent_pay_types = AbsoluteAmountOrPercentPayType::all();

        $periodicity_types = PeriodicityType::all();

        $previous_url = $currBelonging->productsByBelongingInfo->first()->compare_url;

        $getCompareInfo = $this->getCompareInfo();

        $compareVariations = $getCompareInfo[$belonging_id]["checked_variations_full_info"];

        $compareVariationsData = [];

        foreach ($compareVariations as $key => $compareVariation) {

            $curr_data = [];

            $curr_data["product_info"] = Mortgage::with('companyInfo')->with('otherPayments')->with('currencyInfo')->find($compareVariation["product_id"]);

            $curr_data["variation_info"] = ProductsVariation::where(DB::raw("md5(unique_options)"), $compareVariation["curr_variation_options"])->first();

            $curr_data["term"] = $compareVariation["term"];


            $curr_data["cost"] = intval($compareVariation["cost"]);

            $curr_data["prepayment"] = intval($compareVariation["prepayment"]);

            $curr_data["loan_amount"] = intval($compareVariation["cost"]) - intval($curr_data["prepayment"]);

            $loan_amount_converted = $this->getLoanAmountConverted($curr_data["product_info"]->currency, $compareVariation["loan_amount"]);

            $cost_converted = $this->getLoanAmountConverted($curr_data["product_info"]->currency, $curr_data["cost"]);

            $getCalculation = $this->getCalculation($curr_data["product_info"], $curr_data["variation_info"], $cost_converted, $loan_amount_converted, $curr_data["term"], $curr_data["term"], 0, 1);//calculate factual_percentage and other

            $factual_percentage = 100 * $getCalculation["xirr"];

            $curr_data["factual_percentage"] = $factual_percentage;

            if (!is_null($curr_data["variation_info"])) {
                $compareVariationsData[] = $curr_data;
            }
        }

        return view('compare.inner.compareMortgagesInner',
            [
                "belongings" => $belongings,

                "belonging_id" => $belonging_id,

                "currProductByBelongingsView" => $currProductByBelongingsView,

                "currBelonging" => $currBelonging,

                "productPercentageTypesArr" => $productPercentageTypesArr,

                "time_types" => $time_types,

                "repayment_types" => $repayment_types,

                "repayment_loan_interval_types" => $repayment_loan_interval_types,

                "repayment_percent_interval_types" => $repayment_percent_interval_types,

                "providing_types" => $providing_types,

                "percentage_types" => $percentage_types,

                "loan_service_pay_types" => $loan_service_pay_types,

                "periodicity_types" => $periodicity_types,

                "absolute_amount_or_percent_pay_types" => $absolute_amount_or_percent_pay_types,

                "absolute_amount_or_percent_only_pay_types" => $absolute_amount_or_percent_only_pay_types,

                "previous_url" => $previous_url,

                "getCompareInfo" => $getCompareInfo,

                "compareVariationsData" => $compareVariationsData,
            ]);
    }

    /**
     * compare Online Loans Inner page.
     *
     * @return \Illuminate\Http\Response
     */
    public
    function compareOnlineLoansInner(Request $request)
    {
        $belongings = Belonging::with('productsByBelongingInfo')->get();

        $belonging_id = 13;

        $currProductByBelongingsView = ProductByBelongingsView::where("belonging_id", $belonging_id)->first();

        $productPercentageTypesArr = ["1" => "+", "2" => "-", "3" => "±"];

        $currBelonging = Belonging::where("id", $belonging_id)->first();

        $time_types = TimeType::all();

        $repayment_types = RepaymentType::all();

        $repayment_loan_interval_types = RepaymentLoanIntervalType::all();

        $repayment_percent_interval_types = RepaymentPercentIntervalType::all();

        $providing_types = ProvidingType::all();

        $percentage_types = PercentageType::all();

        $loan_service_pay_types = LoanServicePayTypes::all();

        $absolute_amount_or_percent_only_pay_types = AbsoluteAmountORPercentOnlyPayType::all();

        $absolute_amount_or_percent_pay_types = AbsoluteAmountOrPercentPayType::all();

        $periodicity_types = PeriodicityType::all();

        $previous_url = $currBelonging->productsByBelongingInfo->first()->compare_url;

        $getCompareInfo = $this->getCompareInfo();

        $compareVariations = $getCompareInfo[$belonging_id]["checked_variations_full_info"];

        $compareVariationsData = [];

        foreach ($compareVariations as $key => $compareVariation) {

            $curr_data = [];

            $curr_data["product_info"] = OnlineLoan::with('companyInfo')->with('otherPayments')->find($compareVariation["product_id"]);

            $curr_data["variation_info"] = ProductsVariation::where(DB::raw("md5(unique_options)"), $compareVariation["curr_variation_options"])->first();

            $curr_data["term"] = $compareVariation["term"];


            $curr_data["loan_amount"] = intval($compareVariation["loan_amount"]);

            if (intval($curr_data["product_info"]->loan_pledge_ratio) == 0) {
                $cost = 0;
            } else {
                $cost = 100 * $curr_data["loan_amount"] / $curr_data["product_info"]->loan_pledge_ratio;
            }

            $getCalculation = $this->getCalculation($curr_data["product_info"], $curr_data["variation_info"], $cost, $curr_data["loan_amount"], $curr_data["term"], $curr_data["term"], 0, 1);//calculate factual_percentage and other

            $factual_percentage = 100 * $getCalculation["xirr"];

            $curr_data["factual_percentage"] = $factual_percentage;

            if (!is_null($curr_data["variation_info"])) {
                $compareVariationsData[] = $curr_data;
            }
        }

        return view('compare.inner.compareOnlineLoansInner',
            [
                "belongings" => $belongings,

                "belonging_id" => $belonging_id,

                "currProductByBelongingsView" => $currProductByBelongingsView,

                "currBelonging" => $currBelonging,

                "productPercentageTypesArr" => $productPercentageTypesArr,

                "time_types" => $time_types,

                "repayment_types" => $repayment_types,

                "repayment_loan_interval_types" => $repayment_loan_interval_types,

                "repayment_percent_interval_types" => $repayment_percent_interval_types,

                "providing_types" => $providing_types,

                "percentage_types" => $percentage_types,

                "loan_service_pay_types" => $loan_service_pay_types,

                "periodicity_types" => $periodicity_types,

                "absolute_amount_or_percent_pay_types" => $absolute_amount_or_percent_pay_types,

                "absolute_amount_or_percent_only_pay_types" => $absolute_amount_or_percent_only_pay_types,

                "previous_url" => $previous_url,

                "getCompareInfo" => $getCompareInfo,

                "compareVariationsData" => $compareVariationsData,
            ]);
    }

    /**
     * compare Consumer Credits Inner page.
     *
     * @return \Illuminate\Http\Response
     */
    public
    function compareConsumerCreditsInner(Request $request)
    {
        $belongings = Belonging::with('productsByBelongingInfo')->get();

        $belonging_id = 6;

        $currProductByBelongingsView = ProductByBelongingsView::where("belonging_id", $belonging_id)->first();

        $productPercentageTypesArr = ["1" => "+", "2" => "-", "3" => "±"];

        $currBelonging = Belonging::where("id", $belonging_id)->first();

        $time_types = TimeType::all();

        $repayment_types = RepaymentType::all();

        $repayment_loan_interval_types = RepaymentLoanIntervalType::all();

        $repayment_percent_interval_types = RepaymentPercentIntervalType::all();

        $providing_types = ProvidingType::all();

        $percentage_types = PercentageType::all();

        $loan_service_pay_types = LoanServicePayTypes::all();

        $absolute_amount_or_percent_only_pay_types = AbsoluteAmountORPercentOnlyPayType::all();

        $absolute_amount_or_percent_pay_types = AbsoluteAmountOrPercentPayType::all();

        $periodicity_types = PeriodicityType::all();

        $previous_url = $currBelonging->productsByBelongingInfo->first()->compare_url;

        $getCompareInfo = $this->getCompareInfo();

        $compareVariations = $getCompareInfo[$belonging_id]["checked_variations_full_info"];

        $compareVariationsData = [];

        foreach ($compareVariations as $key => $compareVariation) {

            $curr_data = [];

            $curr_data["product_info"] = ConsumerCredit::with('companyInfo')->with('otherPayments')->find($compareVariation["product_id"]);

            $curr_data["variation_info"] = ProductsVariation::where(DB::raw("md5(unique_options)"), $compareVariation["curr_variation_options"])->first();

            $curr_data["term"] = $compareVariation["term"];


            $curr_data["loan_amount"] = intval($compareVariation["loan_amount"]);

            if (intval($curr_data["product_info"]->loan_pledge_ratio) == 0) {
                $cost = 0;
            } else {
                $cost = 100 * $curr_data["loan_amount"] / $curr_data["product_info"]->loan_pledge_ratio;
            }

            $getCalculation = $this->getCalculation($curr_data["product_info"], $curr_data["variation_info"], $cost, $curr_data["loan_amount"], $curr_data["term"], $curr_data["term"], 0, 1);//calculate factual_percentage and other

            $factual_percentage = 100 * $getCalculation["xirr"];

            $curr_data["factual_percentage"] = $factual_percentage;

            if (!is_null($curr_data["variation_info"])) {
                $compareVariationsData[] = $curr_data;
            }
        }

        return view('compare.inner.compareConsumerCreditsInner',
            [
                "belongings" => $belongings,

                "belonging_id" => $belonging_id,

                "currProductByBelongingsView" => $currProductByBelongingsView,

                "currBelonging" => $currBelonging,

                "productPercentageTypesArr" => $productPercentageTypesArr,

                "time_types" => $time_types,

                "repayment_types" => $repayment_types,

                "repayment_loan_interval_types" => $repayment_loan_interval_types,

                "repayment_percent_interval_types" => $repayment_percent_interval_types,

                "providing_types" => $providing_types,

                "percentage_types" => $percentage_types,

                "loan_service_pay_types" => $loan_service_pay_types,

                "periodicity_types" => $periodicity_types,

                "absolute_amount_or_percent_pay_types" => $absolute_amount_or_percent_pay_types,

                "absolute_amount_or_percent_only_pay_types" => $absolute_amount_or_percent_only_pay_types,

                "previous_url" => $previous_url,

                "getCompareInfo" => $getCompareInfo,

                "compareVariationsData" => $compareVariationsData,
            ]);
    }

    /**
     * compare Loan Refinancings Inner page.
     *
     * @return \Illuminate\Http\Response
     */
    public
    function compareLoanRefinancingsInner(Request $request)
    {
        $belongings = Belonging::with('productsByBelongingInfo')->get();

        $belonging_id = 11;

        $currProductByBelongingsView = ProductByBelongingsView::where("belonging_id", $belonging_id)->first();

        $productPercentageTypesArr = ["1" => "+", "2" => "-", "3" => "±"];

        $currBelonging = Belonging::where("id", $belonging_id)->first();

        $time_types = TimeType::all();

        $repayment_types = RepaymentType::all();

        $repayment_loan_interval_types = RepaymentLoanIntervalType::all();

        $repayment_percent_interval_types = RepaymentPercentIntervalType::all();

        $providing_types = ProvidingType::all();

        $percentage_types = PercentageType::all();

        $loan_service_pay_types = LoanServicePayTypes::all();

        $absolute_amount_or_percent_only_pay_types = AbsoluteAmountORPercentOnlyPayType::all();

        $absolute_amount_or_percent_pay_types = AbsoluteAmountOrPercentPayType::all();

        $periodicity_types = PeriodicityType::all();

        $previous_url = $currBelonging->productsByBelongingInfo->first()->compare_url;

        $getCompareInfo = $this->getCompareInfo();

        $compareVariations = $getCompareInfo[$belonging_id]["checked_variations_full_info"];

        $compareVariationsData = [];

        foreach ($compareVariations as $key => $compareVariation) {

            $curr_data = [];

            $curr_data["product_info"] = LoanRefinancing::with('companyInfo')->with('otherPayments')->find($compareVariation["product_id"]);

            $curr_data["variation_info"] = ProductsVariation::where(DB::raw("md5(unique_options)"), $compareVariation["curr_variation_options"])->first();

            $curr_data["term"] = $compareVariation["term"];


            $curr_data["loan_amount"] = intval($compareVariation["loan_amount"]);

            if (intval($curr_data["product_info"]->loan_pledge_ratio) == 0) {
                $cost = 0;
            } else {
                $cost = 100 * $curr_data["loan_amount"] / $curr_data["product_info"]->loan_pledge_ratio;
            }

            $getCalculation = $this->getCalculation($curr_data["product_info"], $curr_data["variation_info"], $cost, $curr_data["loan_amount"], $curr_data["term"], $curr_data["term"], 0, 1);//calculate factual_percentage and other

            $factual_percentage = 100 * $getCalculation["xirr"];

            $curr_data["factual_percentage"] = $factual_percentage;

            if (!is_null($curr_data["variation_info"])) {
                $compareVariationsData[] = $curr_data;
            }
        }

        return view('compare.inner.compareLoanRefinancingsInner',
            [
                "belongings" => $belongings,

                "belonging_id" => $belonging_id,

                "currProductByBelongingsView" => $currProductByBelongingsView,

                "currBelonging" => $currBelonging,

                "productPercentageTypesArr" => $productPercentageTypesArr,

                "time_types" => $time_types,

                "repayment_types" => $repayment_types,

                "repayment_loan_interval_types" => $repayment_loan_interval_types,

                "repayment_percent_interval_types" => $repayment_percent_interval_types,

                "providing_types" => $providing_types,

                "percentage_types" => $percentage_types,

                "loan_service_pay_types" => $loan_service_pay_types,

                "periodicity_types" => $periodicity_types,

                "absolute_amount_or_percent_pay_types" => $absolute_amount_or_percent_pay_types,

                "absolute_amount_or_percent_only_pay_types" => $absolute_amount_or_percent_only_pay_types,

                "previous_url" => $previous_url,

                "getCompareInfo" => $getCompareInfo,

                "compareVariationsData" => $compareVariationsData,
            ]);
    }
}