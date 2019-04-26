<?php

namespace App\Http\Controllers;

use App\Models\Belonging;
use App\Models\CarType;
use App\Models\CreditPurposeTypes;
use App\Models\CreditsPurpose;
use App\Models\DepositCapitalizationsList;
use App\Models\DepositInterestRatesPayment;
use App\Models\DepositsSpecialsList;
use App\Models\DepositTypesList;
use App\Models\GoldAssayType;
use App\Models\GoldLoan;
use App\Models\LoanCurrenciesType;
use App\Models\LoanRefinancingPurposeType;
use App\Models\MortgagePurposeType;
use App\Models\NonRecoverableExpensesAnswer;
use App\Models\PaymentCardCurrency;
use App\Models\PaymentCardProductType;
use App\Models\PaymentCardRegion;
use App\Models\PaymentCardType;
use App\Models\PaymentExtraCard;
use App\Models\PaymentSpecialCard;
use App\Models\PercentageType;
use App\Models\ProductDepositsCapitalization;
use App\Models\ProductDepositsCurrencyChanging;
use App\Models\ProductDepositsDepositInterruption;
use App\Models\ProductDepositsInterestRatesPayment;
use App\Models\ProductDepositsMoneyDecreasing;
use App\Models\ProductDepositsMoneyIncreasing;
use App\Models\ProductDepositsSpecial;
use App\Models\ProductMoneyTransferBank;
use App\Models\ProductMoneyTransferSystem;
use App\Models\ProductsAgricLoansPurposeType;
use App\Models\ProductsGoldAssayType;
use App\Models\ProductsLoanRefinancingPurposeType;
use App\Models\ProductsMortgagesPurposeType;
use App\Models\ProductsPaymentCardsCardType;
use App\Models\ProductsPaymentCardsCurrency;
use App\Models\ProductsPaymentCardsExtraType;
use App\Models\ProductsPaymentCardsRegion;
use App\Models\ProductsPaymentCardsType;
use App\Models\ProductsSecurityType;
use App\Models\ProductsSpecialsCardsType;
use App\Models\ProductsVariation;
use App\Models\ProvidingType;
use App\Models\PurposeType;
use App\Models\RepaymentType;
use App\Models\SecurityType;
use App\Models\TransferBank;
use App\Models\TransferSystem;
use App\Models\TravelInsurance;
use App\Models\TravelInsurancesVariation;
use App\Models\YesNo;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cookie;
use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use S1calculate\Loan\Finance\Finance;


use Illuminate\Support\Facades\DB;

class MainController extends Controller
{
    public $loan_currencies_types = [];

    public function __construct()
    {
        $this->getloanCurrenciesTypes();
    }

    protected $per_page = 10;

    /**
     * get Loan Amount Amd
     *
     * @return \Illuminate\Http\Response
     */
    public
    function getLoanAmountConverted($currency, $loan_amount)
    {
        $loan_amount_converted = LoanCurrenciesType::find($currency)->extra * $loan_amount;

        return $loan_amount_converted;
    }

    /**
     * remove If Diff Confidention CompareInfo
     *
     * @return \Illuminate\Http\Response
     */
    public function removeIfDiffConfidentionCompareInfo($belonging_id, $check_data)
    {
        $getCompareInfo = $this->getCompareInfo();

        $curr_belonging_key = "belonging_" . $belonging_id;

        $diff = 0;

        if (array_key_exists($curr_belonging_key, $_COOKIE)) {

            $curr_belonging_basic = json_decode($_COOKIE["belonging_" . $belonging_id], true);

            if (count($curr_belonging_basic) > 0) {
                $curr_belonging_basic_first = reset($curr_belonging_basic);

                if (!is_null($check_data["term"]) && $curr_belonging_basic_first["term"] != $check_data["term"]) {
                    $diff = 1;
                }

                if (in_array($belonging_id, [2, 5, 4, 6, 11, 13])) {

                    if (!is_null($check_data["loan_amount"]) && $curr_belonging_basic_first["loan_amount"] != $check_data["loan_amount"]) {
                        $diff = 1;
                    }
                }
                if ($belonging_id == 2) {

                    if (!is_null($check_data["gold_pledge_type"]) && $curr_belonging_basic_first["gold_pledge_type"] != $check_data["gold_pledge_type"]) {
                        $diff = 1;
                    }
                }
                if ($belonging_id == 5) {

                    if (!is_null($check_data["currency"]) && $curr_belonging_basic_first["currency"] != $check_data["currency"]) {
                        $diff = 1;
                    }
                }
                if (in_array($belonging_id, [1, 3])) {
                    if (!is_null($check_data["cost"]) && $curr_belonging_basic_first["cost"] != $check_data["cost"]) {
                        $diff = 1;
                    } else if (!is_null($check_data["prepayment"]) && intval($curr_belonging_basic_first["prepayment"]) != intval($check_data["prepayment"])) {
                        $diff = 1;
                    }
                }


                if ($belonging_id == 12) {

                    if (!is_null($check_data["age"]) && $curr_belonging_basic_first["age"] != $check_data["age"]) {
                        $diff = 1;
                    } else if (!is_null($check_data["country"]) && $curr_belonging_basic_first["country"] != $check_data["country"]) {
                        $diff = 1;
                    }
                }
            }

            if ($diff == 1) {
                $curr_belonging_key = "belonging_" . $belonging_id;

                setcookie($curr_belonging_key, NULL, time() - 3600, "/");

                Cookie::forget($curr_belonging_key);

                $getCompareInfo[$belonging_id]["checked_variations"] = [];

                $getCompareInfo[$belonging_id]["checked_variations_full_info"] = [];

                $getCompareInfo[$belonging_id]["count"] = 0;

                $getCompareInfo[$belonging_id]["display"] = "none";
            }

        }

        return $getCompareInfo;
    }

    /**
     * Get Compare Info.
     *
     * @return \Illuminate\Http\Response
     */
    public
    function checkVariationExist($belonging_id, $curr_variation_options)
    {
        $loan_belonging_ids_arr = [1, 2, 3, 4, 5, 6, 8, 11, 13];

        if (in_array($belonging_id, $loan_belonging_ids_arr)) {
            return ProductsVariation::where(DB::raw("md5(unique_options)"), $curr_variation_options)->count();
        } else {
            if ($belonging_id == 12) {
                return 1;
            }
        }

    }

    /**
     * Get Compare Info.
     *
     * @return \Illuminate\Http\Response
     */
    public
    function getCompareInfo()
    {
        $belongings = Belonging::all();

        $belongingsArr = [];

        foreach ($belongings as $belonging) {
            $curr_belonging_cookie_key = "belonging_" . $belonging->id;

            if (array_key_exists($curr_belonging_cookie_key, $_COOKIE) == false) {
                $curr_belonging_compare_count = 0;

                $curr_belonging_compare_display = "none";

                $curr_belonging_cookie_val = [];

                $curr_belonging_compare_checked_variations = [];
            } else {
                $curr_belonging_cookie_val = json_decode($_COOKIE[$curr_belonging_cookie_key], true);

                $curr_belonging_compare_checked_variations = array_keys($curr_belonging_cookie_val);

                foreach ($curr_belonging_compare_checked_variations as $key => $curr_belonging_compare_checked_variation) {

                    if ($this->checkVariationExist($belonging->id, $curr_belonging_compare_checked_variation) == 0) {

                        unset($curr_belonging_compare_checked_variations[$key]);

                        unset($curr_belonging_cookie_val[$curr_belonging_compare_checked_variation]);
                        //array_search($curr_belonging_compare_checked_variation, $curr_belonging_compare_checked_variations);
                    }
                }

                $curr_belonging_compare_count = count($curr_belonging_cookie_val);

                if ($curr_belonging_compare_count > 0) {
                    $curr_belonging_compare_display = "flex";
                } else {
                    $curr_belonging_compare_display = "none";
                }
            }

            $belongingsArr[$belonging->id] = [
                "id" => $belonging->id,
                "icon" => $belonging->icon,
                "name" => $belonging->name,
                "count" => $curr_belonging_compare_count,
                "checked_variations" => $curr_belonging_compare_checked_variations,
                "checked_variations_full_info" => $curr_belonging_cookie_val,
                "display" => $curr_belonging_compare_display
            ];
        }

        return $belongingsArr;
    }

    /**
     * Get Compare Info.
     *
     * @return \Illuminate\Http\Response
     */
    public
    function getCompareInfoGlobal()
    {
        $belongings = Belonging::all();

        $belongingsArr = [];

        foreach ($belongings as $belonging) {
            $curr_belonging_cookie_key = "belonging_" . $belonging->id;

            if (array_key_exists($curr_belonging_cookie_key, $_COOKIE) == false) {
                $curr_belonging_compare_count = 0;

                $curr_belonging_compare_display = "none";

                $curr_belonging_cookie_val = [];

                $curr_belonging_compare_checked_variations = [];
            } else {
                $curr_belonging_cookie_val = json_decode($_COOKIE[$curr_belonging_cookie_key], true);

                $curr_belonging_compare_checked_variations = array_keys($curr_belonging_cookie_val);

                foreach ($curr_belonging_compare_checked_variations as $key => $curr_belonging_compare_checked_variation) {

                    if ($this->checkVariationExist($belonging->id, $curr_belonging_compare_checked_variation) == 0) {

                        unset($curr_belonging_compare_checked_variations[$key]);

                        unset($curr_belonging_cookie_val[$curr_belonging_compare_checked_variation]);
                        //array_search($curr_belonging_compare_checked_variation, $curr_belonging_compare_checked_variations);
                    }
                }

                $curr_belonging_compare_count = count($curr_belonging_cookie_val);

                if ($curr_belonging_compare_count > 0) {
                    $curr_belonging_compare_display = "flex";
                } else {
                    $curr_belonging_compare_display = "none";
                }
            }

            $belongingsArr[$belonging->id] = [
                "id" => $belonging->id,
                "icon" => $belonging->icon,
                "name" => $belonging->name,
                "count" => $curr_belonging_compare_count,
                "checked_variations" => $curr_belonging_compare_checked_variations,
                "checked_variations_full_info" => $curr_belonging_cookie_val,
                "display" => $curr_belonging_compare_display
            ];
        }

        return $belongingsArr;
    }

    /**
     * Remove Current Belonging Products From Compare(Cookies) if
     * Detect Search Parameters are changed.
     *
     * @return \Illuminate\Http\Response
     */
    public
    function detectSearchParamsChanged($belonging_id)
    {

        //return $belongingsArr;
    }


    public
    function getloanCurrenciesTypes()
    {
        $loan_currencies_types = LoanCurrenciesType::all()->keyBy('id')->toArray();

        $this->loan_currencies_types = $loan_currencies_types;
    }

    /*
     *make Calculation global Object
     */
    public
    function makeCalculationGlobal($product, $product_variation, $cost, $loan_amount, $loan_term_real, $loan_term_search_in_days, $prepayment_percent, $time_type = 2)
    {

    }

    /*
     *get Calculation Results
     */
    public
    function getCalculation($product, $product_variation, $cost, $loan_amount, $loan_term_real, $loan_term_search_in_days, $prepayment_percent, $time_type = 2)
    {
        try {
            /*loan Application Fee*/
            $loanApplicationFee = $product->loan_application_absolute_amount;

            if (strpos($loanApplicationFee, '%') !== false) {

                $loanApplicationFeeFix = 0;

                $loanApplicationFeePercent = str_replace('%', '', $loanApplicationFee);
            } else {
                $loanApplicationFeeFix = $loanApplicationFee;

                $loanApplicationFeePercent = 0;
            }
            /*loan Application Fee*/

            /*loan Service Fee*/
            $loanServiceFee = $product->loan_service_absolute_amount;

            if (strpos($loanServiceFee, '%') !== false) {

                $loanServiceFeeFix = 0;

                $loanServiceFeePercent = str_replace('%', '', $loanServiceFee);

                $loanServiceFeePercent = floatval($loanServiceFeePercent);
            } else {
                $loanServiceFeeFix = intval($loanServiceFee);

                $loanServiceFeePercent = 0;
            }
            /*loan Service Fee*/

            /*loan Collateral Assessment Fee*/
            $loanCollateralAssessmentFee = $product->pledge_assessment_absolute_amount;

            if (strpos($loanCollateralAssessmentFee, '%') !== false) {

                $loanCollateralAssessmentFeeFix = 0;

                $loanCollateralAssessmentFeePercent = str_replace('%', '', $loanCollateralAssessmentFee);
            } else {
                $loanCollateralAssessmentFeeFix = $loanCollateralAssessmentFee;

                $loanCollateralAssessmentFeePercent = 0;
            }
            /*loan Collateral Assessment Fee*/

            /*loan Cash Service Fee*/
            $loanCashServiceFee = $product->cashing_pay_absolute_amount;

            if (strpos($loanCashServiceFee, '%') !== false) {

                $loanCashServiceFeeFix = 0;

                $loanCashServiceFeePercent = str_replace('%', '', $loanCashServiceFee);

                $loanCashServiceFeePercent = floatval($loanCashServiceFeePercent);
            } else {
                $loanCashServiceFeeFix = intval($loanCashServiceFee);

                $loanCashServiceFeePercent = 0;
            }
            /*loan Cash Service Fee*/

            /*loan Collateral Insurance Fee*/
            $loanCollateralInsuranceFee = $product->pledge_insurance_absolute_amount;

            if (strpos($loanCollateralInsuranceFee, '%') !== false) {

                $loanCollateralInsuranceFeeFix = 0;

                $loanCollateralInsuranceFeePercent = str_replace('%', '', $loanCollateralInsuranceFee);
            } else {
                $loanCollateralInsuranceFeeFix = $loanCollateralInsuranceFee;

                $loanCollateralInsuranceFeePercent = 0;
            }
            /*loan Collateral Assessment Fee*/

            /*loan Collateral Maintenance Fee*/
            $loanCollateralMaintenanceFee = $product->pledge_keep_absolute_amount;

            if (strpos($loanCollateralMaintenanceFee, '%') !== false) {

                $loanCollateralMaintenanceFeeFix = 0;

                $loanCollateralMaintenanceFeePercent = str_replace('%', '', $loanCollateralMaintenanceFee);
            } else {
                $loanCollateralMaintenanceFeeFix = $loanCollateralMaintenanceFee;

                $loanCollateralMaintenanceFeePercent = 0;
            }
            /*loan Collateral Maintenance Fee*/

            /*loan Notary Validation Fee*/
            $loanNotaryValidationFee = $product->notarial_ratification_pay;

            if (strpos($loanNotaryValidationFee, '%') !== false) {

                $loanNotaryValidationFeeFix = 0;

                $loanNotaryValidationFeePercent = str_replace('%', '', $loanNotaryValidationFee);
            } else {
                $loanNotaryValidationFeeFix = $loanNotaryValidationFee;

                $loanNotaryValidationFeePercent = 0;
            }
            /*loan Notary Validation Fee*/

            /*loan Pledge State Fee*/
            $loanPledgeStateFee = $product->pledge_state_registration_pay;

            if (strpos($loanPledgeStateFee, '%') !== false) {

                $loanPledgeStateFeeFix = 0;

                $loanPledgeStateFeePercent = str_replace('%', '', $loanPledgeStateFee);

                $loanPledgeStateFeePercent = floatval($loanPledgeStateFeePercent);
            } else {
                $loanPledgeStateFeeFix = $loanPledgeStateFee;

                $loanPledgeStateFeeFix = intval($loanPledgeStateFeeFix);

                $loanPledgeStateFeePercent = 0;
            }
            /*loan Pledge State Fee*/

            /*Loan Borrower Insurance Fee*/
            $loanBorrowerInsuranceFee = $product->borrower_accident_insurance_fee_absolute_amount;

            if (strpos($loanBorrowerInsuranceFee, '%') !== false) {

                $loanBorrowerInsuranceFeeFix = 0;

                $loanBorrowerInsuranceFeePercent = str_replace('%', '', $loanBorrowerInsuranceFee);
            } else {
                $loanBorrowerInsuranceFeeFix = $loanBorrowerInsuranceFee;

                $loanBorrowerInsuranceFeePercent = 0;
            }
            /*Loan Borrower Insurance Fee*/

            /*Loan Cadastre Fee*/
            $loanCadastreFeeFix = $product->cadastre_related_fee;
            /*Loan Cadastre Fee*/

            $map_loan_repayment_period_arr = [2 => 2, 3 => 1, 4 => 3];

            $map_loan_percent_repayment_period_arr = [2 => 2, 3 => 1, 4 => 4, 5 => 3];

            $repayment_method = $product_variation->repayment_type - 1;

            if ($repayment_method == 2) {

                $loan_repayment_period = $map_loan_repayment_period_arr[$product_variation->repayment_loan_interval_type_id];

                $loan_percent_repayment_period = $map_loan_percent_repayment_period_arr[$product_variation->repayment_percent_interval_type_id];
            } else {
                $loan_repayment_period = 1;

                $loan_percent_repayment_period = 1;
            }

            $pledge_insurance_pay_type = $product->pledge_insurance_pay_type;

            $loan_service_pay_type = $product->loan_service_pay_type;

            if (is_null($pledge_insurance_pay_type) || $pledge_insurance_pay_type == 4) {
                $pledge_insurance_pay_type = 1;
            }
            if (is_null($loan_service_pay_type)) {
                $loan_service_pay_type = 1;
            }

            if ($loan_term_search_in_days < 30) {
                $loan_term_search_in_days_calculate = 30;
            } else {
                $loan_term_search_in_days_calculate = $loan_term_search_in_days;
            }

            if ($time_type == 1) {
                $loan_term_in_months = ceil($loan_term_search_in_days / 30);//  dd(fmod(365,365) == 0);
            } else if ($time_type == 3) {
                $loan_term_in_months = $loan_term_real * 12;
            } else if ($time_type == 2) {
                $loan_term_in_months = $loan_term_real;
            }

            $r = new Finance();

            $r->setLoanpledge($cost);

            $r->setLoanamount(intval($loan_amount));

            $r->setLoanperiod(intval($loan_term_search_in_days_calculate));

            $r->setLoanpercent($product_variation->percentage);

            $r->setLoanRepaymentMethod($repayment_method);

            $r->setLoanRepaymentPeriod($loan_repayment_period);

            $r->setLoanInterestRepaymentPeriod($loan_percent_repayment_period);

            if ($product->privileged_term == 1 && $repayment_method == 2) {

                if ($product->privileged_term_loan_time_type == 1) {

                    $privileged_term_loan_months = ceil($product->privileged_term_loan / 30);
                } else if ($product->privileged_term_loan_time_type == 2) {

                    $privileged_term_loan_months = $product->privileged_term_loan;
                } else if ($product->privileged_term_loan_time_type == 3) {

                    $privileged_term_loan_months = $product->privileged_term_loan * 12;
                }

                if ($product->privileged_term_percentage_time_type == 1) {

                    $privileged_term_percentage_months = ceil($product->privileged_term_percentage / 30);
                } else if ($product->privileged_term_percentage_time_type == 2) {

                    $privileged_term_percentage_months = $product->privileged_term_percentage;
                } else if ($product->privileged_term_percentage_time_type == 3) {

                    $privileged_term_percentage_months = $product->privileged_term_percentage * 12;
                }
//
//                $r->setLoanGracePeriodPrincipal($privileged_term_loan_months);
//
//                $r->setLoanGracePeriodInterest($privileged_term_percentage_months);
            }

//            dd(array_key_exists('',$product->toArray()));

            $loan_service_pay_type = $product->loan_service_pay_type;

            $pledge_assessment_pay_once_type = $product->pledge_assessment_pay_once_type;

            if (is_null($loan_service_pay_type)) {
                $loan_service_pay_type = 1;
            }

            if (is_null($pledge_assessment_pay_once_type)) {
                $pledge_assessment_pay_once_type = 1;
            }

            $r->setLoanApplicationFee($loanApplicationFeeFix, $loanApplicationFeePercent, $product->loan_application_survey_pay_from, $product->loan_application_survey_pay_to, 1); // every loanType time is 1

            if ($loanServiceFeePercent > 0) {
                $r->setLoanServiceFee($loanServiceFeeFix, $loanServiceFeePercent, $product->loan_service_pay_from, intval($product->loan_service_pay_to), 1);//$loan_service_pay_type
            } else {
                $r->setLoanServiceFee($loanServiceFeeFix);
            }

            $r->setLoanCollateralAssessmentFee($loanCollateralAssessmentFeeFix, $loanCollateralAssessmentFeePercent, $product->pledge_assessment_pay_from, $product->pledge_assessment_pay_to, 1); // every loanType time is 1

            $r->setLoanCollateralInsuranceFee($loanCollateralInsuranceFeeFix, $loanCollateralInsuranceFeePercent, $product->pledge_insurance_pay_from, $product->pledge_insurance_pay_to, 1);

            $r->setLoanCollateralMaintenanceFee($loanCollateralMaintenanceFeeFix, $loanCollateralMaintenanceFeePercent, $product->pledge_keep_pay_from, $product->pledge_keep_pay_to, 1);

            $r->setLoanNotaryValidationFee($loanNotaryValidationFeeFix, $loanNotaryValidationFeePercent, $product->notarial_ratification_pay_from, $product->notarial_ratification_pay_to, 1); // every loanType time is 1

            if ($loanCashServiceFeePercent > 0) {
                $r->setLoanCashServiceFee($loanCashServiceFeeFix, $loanCashServiceFeePercent, $product->cashing_pay_from, $product->cashing_pay_to, 1); // every loanType time is 1
            } else {
                $r->setLoanCashServiceFee($loanCashServiceFeeFix); // every loanType time is 1
            }

            if ($loanPledgeStateFeePercent > 0) {
                $r->setLoanPledgeStateFee($loanPledgeStateFeeFix, $loanPledgeStateFeePercent, $product->pledge_state_registration_pay_from, $product->pledge_state_registration_pay_to, 1); // every loanType time is 1
            } else {
                $r->setLoanPledgeStateFee($loanPledgeStateFeeFix); // case $loanPledgeStateFee is fix
            }

            $r->setLoanCadastreFee($loanCadastreFeeFix); // every loan is fix

            $r->setLoanBorrowerInsuranceFee($loanBorrowerInsuranceFeeFix, $loanBorrowerInsuranceFeePercent, $product->borrower_accident_insurance_fee_pay_from, $product->borrower_accident_insurance_fee_pay_to, 1); //
//dd($product->otherPayments);

            if ($product->other_payments == 1) {

                foreach ($product->otherPayments as $productCurrOtherPayment) {

                    $loanOtherFee = $productCurrOtherPayment->other_payments_amount;

                    if (strpos($loanOtherFee, '%') !== false) {

                        $loanOtherFeeFix = 0;

                        $loanOtherFeePercent = str_replace('%', '', $loanOtherFee);

                        $loanOtherFeePercent = floatval($loanOtherFeePercent);
                    } else {
                        $loanOtherFeeFix = $loanOtherFee;

                        $loanOtherFeePercent = 0;
                    }

                    $loanOtherFeeType = $productCurrOtherPayment->other_payments_periodicity;

                    $loanOtherFeeFrom = $productCurrOtherPayment->other_payments_from;

                    $loanOtherFeeTo = $productCurrOtherPayment->other_payments_to;

                    $loanOtherFeeLoanType = $productCurrOtherPayment->other_payments_sum_percent_type;

                    if (is_null($loanOtherFeeLoanType)) {
                        $loanOtherFeeLoanType = 1;
                    }

                    $r->setLoanOtherFee($loanOtherFeeFix, $loanOtherFeeType, $loanOtherFeePercent, $loanOtherFeeFrom, $loanOtherFeeTo, $loanOtherFeeLoanType);
                }
            }

//            $executionStartTime = microtime(true);
            $result = $r->Calculate();
//            $executionEndTime = microtime(true);
//            $seconds = $executionEndTime - $executionStartTime;
//            dd($seconds);
            $schedules = $result["schedule"];

            $main_require_payments = array_sum($result["other_fee"]);
//
//            if ($main_require_payments > 100) {
//                dd($loanServiceFeeFix);
//            dd($result["other_fee"]);
//        }

            $other_require_payments = 0;

            $sum_payments = 0;

            foreach ($schedules as $currSchedule) {

                $currScheduleCopy = $currSchedule; // copy current Schedule for calculating sum payments

                unset($currSchedule["principal_balance"], $currSchedule["monthly_interest"], $currSchedule["monthly_principal_amount"], $currSchedule["loan_pay_day"]);

                $other_require_payments = $other_require_payments + array_sum($currSchedule);


                unset($currScheduleCopy["principal_balance"], $currScheduleCopy["loan_pay_day"]);

                $sum_payments = $sum_payments + array_sum($currScheduleCopy);
            }

            $result["require_payments"] = $main_require_payments + $other_require_payments;

            $result["sum_payments"] = $main_require_payments + $sum_payments;

            return $result;
        } catch (\Exception $e) {
            dd($e->getFile(), $e->getLine(), $e->getMessage());
        }
    }

    /**
     * Get product searched results filters single counts
     *
     * @return \Illuminate\Http\Response
     */
    public
    function compareProductsGetSomeFilters($belonging_id, $products)
    {
        $data = [];

        $yes_no_answers = YesNo::all();

        $product_ids = $products->pluck('id')->toArray();

        $loan_belonging_ids_arr = [1, 2, 3, 4, 5, 6, 8, 11, 13];

        if (in_array($belonging_id, $loan_belonging_ids_arr)) {

            $percentage_types = PercentageType::all();

            $percentage_types_arr = [];

            foreach ($percentage_types as $percentage_type) {
                if ($percentage_type->id != 1) {

                    $count = ProductsVariation::where('belonging_id', $belonging_id)
                        ->whereIn('product_id', $product_ids)->where('percentage_type', $percentage_type->id)->count();

                    $percentage_types_arr[] = array(
                        "id" => $percentage_type->id,
                        "info" => $percentage_type,
                        "count" => $count
                    );
                }
            }

            $data["percentage_types"] = $percentage_types_arr;


            $providing_types = ProvidingType::all();

            $providing_types_arr = [];

            foreach ($providing_types as $providing_type) {
                if ($providing_type->id != 1) {

                    $count = ProductsVariation::where('belonging_id', $belonging_id)
                        ->whereIn('product_id', $product_ids)->where('providing_type', $providing_type->id)->count();

                    $providing_types_arr[] = array(
                        "id" => $providing_type->id,
                        "info" => $providing_type,
                        "count" => $count
                    );
                }
            }

            $data["providing_types"] = $providing_types_arr;


            $repayment_types = RepaymentType::all();

            $repayment_types_arr = [];

            foreach ($repayment_types as $repayment_type) {
                if ($repayment_type->id != 1) {

                    $count = ProductsVariation::where('belonging_id', $belonging_id)
                        ->whereIn('product_id', $product_ids)->where('repayment_type', $repayment_type->id)->count();

                    $repayment_types_arr[] = array(
                        "id" => $repayment_type->id,
                        "info" => $repayment_type,
                        "count" => $count
                    );
                }
            }

            $data["repayment_types"] = $repayment_types_arr;

            if ($belonging_id != 2) {

                $security_types = SecurityType::all();

                $security_types_arr = [];

                foreach ($security_types as $security_type) {

                    $product_ids_filtered_security_type = ProductsSecurityType::where('security_type', $security_type->id)
                        ->where('belonging_id', $belonging_id)
                        ->whereIn('product_id', $product_ids)->pluck('product_id')->toArray();

                    $count = 0;

                    if (count($product_ids_filtered_security_type) > 0) {
                        foreach ($products->whereIn('id', $product_ids_filtered_security_type) as $product) {
                            $count = $count + $product->variations_count;
                        }
                    }

                    $security_types_arr[] = array(
                        "id" => $security_type->id,
                        "info" => $security_type,
                        "count" => $count
                    );
                }

                $data["security_types"] = $security_types_arr;
            }
        }

        if ($belonging_id == 2) {
            $gold_assay_types = GoldAssayType::all();

            $gold_assay_types_arr = [];

            foreach ($gold_assay_types as $gold_assay_type) {

                $count_gold_assay_types = 0;

                foreach ($products as $product) {

                    $count_curr_product_curr_gold_assay_type = ProductsGoldAssayType::where('gold_assay_type_id', $gold_assay_type->id)
                        ->where('belonging_id', $belonging_id)
                        ->where('product_id', $product->id)->count();

                    $count_curr_product_variations_curr_gold_assay_type = $count_curr_product_curr_gold_assay_type * $product->variations_count;

                    $count_gold_assay_types = $count_gold_assay_types + $count_curr_product_variations_curr_gold_assay_type;
                }

                $gold_assay_types_arr[] = array(
                    "id" => $gold_assay_type->id,
                    "info" => $gold_assay_type,
                    "count" => $count_gold_assay_types
                );
            }

            $data["gold_assay_types"] = $gold_assay_types_arr;
        }

        if ($belonging_id == 5) {
            $purposeTypes = PurposeType::all();

            $purpose_types_arr = [];

            foreach ($purposeTypes as $purposeType) {

                $product_ids_filtered_purpose_type = ProductsAgricLoansPurposeType::where('purpose_type', $purposeType->id)
                    ->whereIn('product_id', $product_ids)->pluck('product_id')->toArray();

                $count = 0;

                if (count($product_ids_filtered_purpose_type) > 0) {
                    foreach ($products->whereIn('id', $product_ids_filtered_purpose_type) as $product) {
                        $count = $count + $product->variations_count;
                    }
                }

                $purpose_types_arr[] = array(
                    "id" => $purposeType->id,
                    "info" => $purposeType,
                    "count" => $count
                );
            }

            $data["purposeTypes"] = $purpose_types_arr;
        }

        if ($belonging_id == 8) {
            $mortgagePurposeTypes = MortgagePurposeType::all();

            $purpose_types_arr = [];

            foreach ($mortgagePurposeTypes as $purposeType) {

                $product_ids_filtered_purpose_type = ProductsMortgagesPurposeType::where('purpose_type', $purposeType->id)
                    ->whereIn('product_id', $product_ids)->pluck('product_id')->toArray();

                $count = 0;

                if (count($product_ids_filtered_purpose_type) > 0) {
                    foreach ($products->whereIn('id', $product_ids_filtered_purpose_type) as $product) {
                        $count = $count + $product->variations_count;
                    }
                }

                $purpose_types_arr[] = array(
                    "id" => $purposeType->id,
                    "info" => $purposeType,
                    "count" => $count
                );
            }

            $data["purposeTypes"] = $purpose_types_arr;
        }

        if ($belonging_id == 1) {
            $car_types = CarType::all();

            $car_types_arr = [];

            foreach ($car_types as $car_type) {
                if ($car_type->id != 1) {
                    $car_types_arr[] = array(
                        "id" => $car_type->id,
                        "info" => $car_type,
                        "count" => $products->whereIn('car_type', array($car_type->id, 1))->sum('variations_count'));
                }
            }

            $data["car_types"] = $car_types_arr;
        }

        if ($belonging_id == 3) {

            $creditPurposeTypes = CreditPurposeTypes::all();

            $creditPurposeTypes_arr = [];

            foreach ($creditPurposeTypes as $creditPurposeType) {

                $product_ids_filtered_purpose_type = CreditsPurpose::where('purpose_type', $creditPurposeType->id)
                    ->whereIn('product_id', $product_ids)->pluck('product_id')->toArray();

                $count = 0;

                if (count($product_ids_filtered_purpose_type) > 0) {
                    foreach ($products->whereIn('id', $product_ids_filtered_purpose_type) as $product) {
                        $count = $count + $product->variations_count;
                    }
                }

                $creditPurposeTypes_arr[] = array(
                    "id" => $creditPurposeType->id,
                    "info" => $creditPurposeType,
                    "count" => $count
                );
            }

            $data["creditPurposeTypes"] = $creditPurposeTypes_arr;
        }

        if ($belonging_id == 11) {

            $loanRefinancingPurposeTypes = LoanRefinancingPurposeType::all();

            $loanRefinancingPurposeTypes_arr = [];

            foreach ($loanRefinancingPurposeTypes as $loanRefinancingPurposeType) {
                $loanRefinancingPurposeTypes_arr[] = array(
                    "id" => $loanRefinancingPurposeType->id,
                    "info" => $loanRefinancingPurposeType,
                    "count" => ProductsLoanRefinancingPurposeType::where('purpose_type', $loanRefinancingPurposeType->id)
                        ->whereIn('product_id', $product_ids)->count());
            }

            $data["loanRefinancingPurposeTypes"] = $loanRefinancingPurposeTypes_arr;
        }

        if ($belonging_id == 7) {

            /*deposit_capitalizations_list*/
            $deposit_capitalizations_list = DepositCapitalizationsList::all();

            $deposit_capitalizations_list_arr = [];

            foreach ($deposit_capitalizations_list as $deposit_capitalization_curr) {
                $deposit_capitalizations_list_arr[] = array(
                    "id" => $deposit_capitalization_curr->id,
                    "info" => $deposit_capitalization_curr,
                    "count" => ProductDepositsCapitalization::where('type_id', $deposit_capitalization_curr->id)
                        ->where('belonging_id', $belonging_id)
                        ->whereIn('product_id', $product_ids)->count());
            }

            $data["deposit_capitalizations_list"] = $deposit_capitalizations_list_arr;
            /*deposit_capitalizations_list*/

            /*deposit_interest_rates_payments*/
            $deposit_interest_rates_payments = DepositInterestRatesPayment::all();

            $deposit_interest_rates_payments_arr = [];

            foreach ($deposit_interest_rates_payments as $deposit_interest_rates_payment) {
                $deposit_interest_rates_payments_arr[] = array(
                    "id" => $deposit_interest_rates_payment->id,
                    "info" => $deposit_interest_rates_payment,
                    "count" => ProductDepositsInterestRatesPayment::where('interest_rate_id', $deposit_interest_rates_payment->id)
                        ->where('belonging_id', $belonging_id)
                        ->whereIn('product_id', $product_ids)->count());
            }

            $data["deposit_interest_rates_payments"] = $deposit_interest_rates_payments_arr;
            /*deposit_interest_rates_payments*/

            /*deposits_specials_list*/
            $deposits_specials_list = DepositsSpecialsList::all();

            $deposits_specials_list_arr = [];

            foreach ($deposits_specials_list as $deposits_special) {
                $deposits_specials_list_arr[] = array(
                    "id" => $deposits_special->id,
                    "info" => $deposits_special,
                    "count" => ProductDepositsSpecial::where('type_id', $deposits_special->id)
                        ->where('belonging_id', $belonging_id)
                        ->whereIn('product_id', $product_ids)->count());
            }

            $data["deposits_specials_list"] = $deposits_specials_list_arr;
            /*deposits_specials_list*/

            /*Changes possibility*/
            $money_increasing = [];

            $money_decreasing = [];

            $currency_changing = [];

            $deposit_interruption = [];

            foreach ($yes_no_answers as $yes_no_answer) {
                $money_increasing[$yes_no_answer->id] = ProductDepositsMoneyIncreasing::where('answer', $yes_no_answer->id)
                    ->where('belonging_id', $belonging_id)
                    ->whereIn('product_id', $product_ids)->count();
            }

            foreach ($yes_no_answers as $yes_no_answer) {
                $money_decreasing[$yes_no_answer->id] = ProductDepositsMoneyDecreasing::where('answer', $yes_no_answer->id)
                    ->where('belonging_id', $belonging_id)
                    ->whereIn('product_id', $product_ids)->count();
            }

            foreach ($yes_no_answers as $yes_no_answer) {
                $currency_changing[$yes_no_answer->id] = ProductDepositsCurrencyChanging::where('answer', $yes_no_answer->id)
                    ->where('belonging_id', $belonging_id)
                    ->whereIn('product_id', $product_ids)->count();
            }

            foreach ($yes_no_answers as $yes_no_answer) {
                $deposit_interruption[$yes_no_answer->id] = ProductDepositsDepositInterruption::where('answer', $yes_no_answer->id)
                    ->where('belonging_id', $belonging_id)
                    ->whereIn('product_id', $product_ids)->count();
            }

            $data["money_increasing"] = $money_increasing;

            $data["money_decreasing"] = $money_decreasing;

            $data["currency_changing"] = $currency_changing;

            $data["deposit_interruption"] = $deposit_interruption;
            /*Changes possibility*/
        }

        if ($belonging_id == 10) {

            /*transfer_systems*/
            $transfer_systems = TransferSystem::all();

            $transfer_systems_arr = [];

            foreach ($transfer_systems as $transfer_system) {
                $transfer_systems_arr[] = array(
                    "id" => $transfer_system->id,
                    "info" => $transfer_system,
                    "count" => ProductMoneyTransferSystem::where('transfer_system_id', $transfer_system->id)
                        ->where('belonging_id', $belonging_id)
                        ->whereIn('product_id', $product_ids)->count());
            }

            $data["transfer_systems"] = $transfer_systems_arr;
            /*transfer_systems*/

            /*transfer_banks*/
            $transfer_banks = TransferBank::all();

            $transfer_banks_arr = [];

            foreach ($transfer_banks as $transfer_bank) {
                $transfer_banks_arr[] = array(
                    "id" => $transfer_bank->id,
                    "info" => $transfer_bank,
                    "count" => ProductMoneyTransferBank::where('bank_id', $transfer_bank->id)
                        ->where('belonging_id', $belonging_id)
                        ->whereIn('product_id', $product_ids)->count());
            }

            $data["transfer_banks"] = $transfer_banks_arr;
            /*transfer_banks*/

        }

        if ($belonging_id == 9) {
            /*payment_card_types*/
            $payment_card_types = PaymentCardType::all();

            $payment_card_types_arr = [];

            foreach ($payment_card_types as $payment_card_type) {
                $payment_card_types_arr[] = array(
                    "id" => $payment_card_type->id,
                    "info" => $payment_card_type,
                    "count" => ProductsPaymentCardsType::where('type_id', $payment_card_type->id)
                        ->where('belonging_id', $belonging_id)
                        ->whereIn('product_id', $product_ids)->count());
            }

            $data["payment_card_types"] = $payment_card_types_arr;
            /*payment_card_types*/

            /*payment_card_product_types*/
            $payment_card_product_types = PaymentCardProductType::all();

            $payment_card_product_types_arr = [];

            foreach ($payment_card_product_types as $payment_card_product_type) {
                $payment_card_product_types_arr[] = array(
                    "id" => $payment_card_product_type->id,
                    "info" => $payment_card_product_type,
                    "count" => ProductsPaymentCardsCardType::where('card_type_id', $payment_card_product_type->id)
                        ->where('belonging_id', $belonging_id)
                        ->whereIn('product_id', $product_ids)->count());
            }

            $data["payment_card_product_types"] = $payment_card_product_types_arr;
            /*payment_card_product_types*/

            /*payment_card_regions*/
            $payment_card_regions = PaymentCardRegion::all();

            $payment_card_regions_arr = [];

            foreach ($payment_card_regions as $payment_card_region) {
                $payment_card_regions_arr[] = array(
                    "id" => $payment_card_region->id,
                    "info" => $payment_card_region,
                    "count" => ProductsPaymentCardsRegion::where('region_id', $payment_card_region->id)
                        ->where('belonging_id', $belonging_id)
                        ->whereIn('product_id', $product_ids)->count());
            }

            $data["payment_card_regions"] = $payment_card_regions_arr;
            /*payment_card_regions*/

            /*payment_extra_cards*/
            $payment_extra_cards = PaymentExtraCard::all();

            $payment_extra_cards_arr = [];

            foreach ($payment_extra_cards as $payment_extra_card) {
                $payment_extra_cards_arr[] = array(
                    "id" => $payment_extra_card->id,
                    "info" => $payment_extra_card,
                    "count" => ProductsPaymentCardsExtraType::where('extra_type_id', $payment_extra_card->id)
                        ->where('belonging_id', $belonging_id)
                        ->whereIn('product_id', $product_ids)->count());
            }

            $data["payment_extra_cards"] = $payment_extra_cards_arr;
            /*payment_extra_cards*/

            /*payment_specials_cards*/
            $payment_specials_cards = PaymentSpecialCard::all();

            $payment_specials_cards_arr = [];

            foreach ($payment_specials_cards as $payment_specials_card) {
                $payment_specials_cards_arr[] = array(
                    "id" => $payment_specials_card->id,
                    "info" => $payment_specials_card,
                    "count" => ProductsSpecialsCardsType::where('special_card_type_id', $payment_specials_card->id)
                        ->where('belonging_id', $belonging_id)
                        ->whereIn('product_id', $product_ids)->count());
            }

            $data["payment_specials_cards"] = $payment_specials_cards_arr;
            /*payment_specials_cards*/
        }

        if ($belonging_id == 12) {

            $non_recoverable_expenses_answers = NonRecoverableExpensesAnswer::all();

            $non_recoverable_expenses_answers_arr = [];

            foreach ($non_recoverable_expenses_answers as $non_recoverable_expenses_answer) {

                if ($non_recoverable_expenses_answer->id != 1) {

                    $product_ids_filtered_non_recoverable_expenses_answer = $products->whereIn('non_recoverable_amount', array($non_recoverable_expenses_answer->id, 1))->pluck('id')->toArray();

                    $count = 0;

                    if (count($product_ids_filtered_non_recoverable_expenses_answer) > 0) {
                        foreach ($products->whereIn('id', $product_ids_filtered_non_recoverable_expenses_answer) as $product) {
                            $count = $count + $product->variations->count();
                        }
                    }

                    $non_recoverable_expenses_answers_arr[$non_recoverable_expenses_answer->id] = array(
                        "id" => $non_recoverable_expenses_answer->id,
                        "info" => $non_recoverable_expenses_answer,
                        "count" => $count
                    );
                }
            }

            $data["non_recoverable_expenses_answers"] = $non_recoverable_expenses_answers_arr;


            $term_inputs_quantities = [1 => "մեկ անգամ", 2 => "մեկից ավել անգամ"];

            $term_inputs_quantities_arr = [];

            foreach ($term_inputs_quantities as $key => $term_inputs_quantity_curr) {

                $count = 0;

                foreach ($products as $product) {

                    if ($key == 1) {
                        $curr_count = $product->variations->where('term_inputs_quantity', 1)->count();
                    } else {
                        $curr_count = $product->variations->where('term_inputs_quantity', '>', 1)->count();
                    }

                    $count = $count + $curr_count;
                }

                $term_inputs_quantities_arr[$key] = array(
                    "id" => $key,
                    "info" => $term_inputs_quantity_curr,
                    "count" => $count
                );
            }

            $data["term_inputs_quantities"] = $term_inputs_quantities_arr;
        }

        $special_project_statuses_arr = [];

        $privileged_term_statuses_arr = [];

        if (in_array($belonging_id, [2, 11, 13])) {
            foreach ($yes_no_answers as $yes_no_answer) {
                $count_privileged_term = 0;

                if ($yes_no_answer->id == 1) {
                    $products_filtered_privileged_term_status = $products->where('privileged_term', 1);
                } else {
                    $products_filtered_privileged_term_status = $products->where('privileged_term', '!=', 1);
                }

                if ($products_filtered_privileged_term_status->count() > 0) {
                    foreach ($products_filtered_privileged_term_status as $product) {
                        $count_privileged_term = $count_privileged_term + $product->variations_count;
                    }
                }

                $privileged_term_statuses_arr[$yes_no_answer->id] = array(
                    "id" => $yes_no_answer->id,
                    "info" => $yes_no_answer,
                    "count" => $count_privileged_term
                );
            }

        } else {
            foreach ($yes_no_answers as $yes_no_answer) {
                $count_special_project = 0;

                $count_privileged_term = 0;

                if ($yes_no_answer->id == 1) {
                    $products_filtered_special_project_status = $products->where('special_projects', 1);

                    $products_filtered_privileged_term_status = $products->where('privileged_term', 1);
                } else {
                    $products_filtered_special_project_status = $products->where('special_projects', '!=', 1);

                    $products_filtered_privileged_term_status = $products->where('privileged_term', '!=', 1);
                }

                if ($products_filtered_special_project_status->count() > 0) {
                    foreach ($products_filtered_special_project_status as $product) {
                        $count_special_project = $count_special_project + $product->variations_count;
                    }
                }

                if ($products_filtered_privileged_term_status->count() > 0) {
                    foreach ($products_filtered_privileged_term_status as $product) {
                        $count_privileged_term = $count_privileged_term + $product->variations_count;
                    }
                }

                $special_project_statuses_arr[$yes_no_answer->id] = array(
                    "id" => $yes_no_answer->id,
                    "info" => $yes_no_answer,
                    "count" => $count_special_project
                );

                $privileged_term_statuses_arr[$yes_no_answer->id] = array(
                    "id" => $yes_no_answer->id,
                    "info" => $yes_no_answer,
                    "count" => $count_privileged_term
                );
            }
        }

        $data["special_project_statuses"] = $special_project_statuses_arr;

        $data["privileged_term_statuses"] = $privileged_term_statuses_arr;

        return $data;
    }

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

        if (in_array($belonging_id, $loan_belonging_ids_arr)) {
            $providing_types_filter = $filter_transfer_data["providing_types"];

            $percentage_types_filter = $filter_transfer_data["percentage_types"];

            $repayment_types_filter = $filter_transfer_data["repayment_types"];

            $special_project_answers_filter = $filter_transfer_data["special_project_answers"];

            $privileged_term_answers_filter = $filter_transfer_data["privileged_term_answers"];
        }

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
                $term_inputs_quantities_arr = [];

                $non_recoverable_expenses_arr = [];

                $non_recoverable_expenses_answers = NonRecoverableExpensesAnswer::all();

                $term_inputs_quantities = [1 => "մեկ անգամ", 2 => "մեկից ավել անգամ"];

                $non_recoverable_expenses_filter = $filter_transfer_data["non_recoverable_expenses"];

                $term_inputs_quantities_filter = $filter_transfer_data["term_inputs_quantities"];

                if (is_null($non_recoverable_expenses_filter)) {
                    $non_recoverable_expenses_filter = NonRecoverableExpensesAnswer::pluck('id')->toArray();
                } else {
                    $non_recoverable_expenses_filter = array_merge($non_recoverable_expenses_filter, array(1));
                }

                if (is_null($term_inputs_quantities_filter)) {
                    $term_inputs_quantities_filter = [1, 2];
                }

                $products_without_filter_by_non_recoverable_expenses_filter_ids = $products_ids_without_any_filter;

                $products_without_filter_by_term_inputs_quantities_ids = TravelInsurance::whereIn('id', $products_ids_without_any_filter)
                    ->whereIn('non_recoverable_amount', $non_recoverable_expenses_filter)->pluck('id')->toArray();

                if (is_array($term_inputs_quantities_filter) && count($term_inputs_quantities_filter) == 1) {
                    if ($term_inputs_quantities_filter[0] == 1) {
                        $products_variations_without_non_recoverable_expenses = TravelInsurancesVariation::whereIn('id', $product_variations_ids_without_any_filter)
                            ->whereIn('product_id', $products_without_filter_by_non_recoverable_expenses_filter_ids)->where('term_inputs_quantity', $term_inputs_quantities_filter[0])->get();
                    } else {
                        $products_variations_without_non_recoverable_expenses = TravelInsurancesVariation::whereIn('id', $product_variations_ids_without_any_filter)
                            ->whereIn('product_id', $products_without_filter_by_non_recoverable_expenses_filter_ids)->where('term_inputs_quantity', '>', 1)->get();
                    }
                } else {
                    $products_variations_without_non_recoverable_expenses = TravelInsurancesVariation::whereIn('id', $product_variations_ids_without_any_filter)
                        ->whereIn('product_id', $products_without_filter_by_non_recoverable_expenses_filter_ids)->get();
                }

                foreach ($term_inputs_quantities as $key => $term_inputs_quantity_curr) {

                    if ($key == 1) {
                        $count = TravelInsurancesVariation::whereIn('id', $product_variations_ids_without_any_filter)
                            ->whereIn('product_id', $products_without_filter_by_term_inputs_quantities_ids)
                            ->where('term_inputs_quantity', 1)->count();
                    } else if ($key == 2) {
                        $count = TravelInsurancesVariation::whereIn('id', $product_variations_ids_without_any_filter)
                            ->whereIn('product_id', $products_without_filter_by_term_inputs_quantities_ids)
                            ->where('term_inputs_quantity', '>', 1)->count();
                    }

                    $term_inputs_quantities_arr[$key] = array(
                        "id" => $key,
                        "count" => $count
                    );
                }

                foreach ($non_recoverable_expenses_answers as $non_recoverable_expenses_answer) {

                    if ($non_recoverable_expenses_answer->id != 1) {

                        $product_ids_filtered_non_recoverable_expenses_answer = TravelInsurance::whereIn('id', $products_ids_without_any_filter)
                            ->whereIn('non_recoverable_amount', array($non_recoverable_expenses_answer->id, 1))->pluck('id')->toArray();

                        $count = $products_variations_without_non_recoverable_expenses->whereIn('product_id', $product_ids_filtered_non_recoverable_expenses_answer)->count();

                        $non_recoverable_expenses_answers_arr[$non_recoverable_expenses_answer->id] = array(
                            "id" => $non_recoverable_expenses_answer->id,
                            "count" => $count
                        );
                    }
                }

                $data["non_recoverable_expenses_answers"] = $non_recoverable_expenses_answers_arr;

                $data["term_inputs_quantities"] = $term_inputs_quantities_arr;

                break;
            case
            13:

                break;
            default:
        }

//        $data["special_project_statuses"] = $special_project_statuses_arr;
//
//        $data["privileged_term_statuses"] = $privileged_term_statuses_arr;

        return $data;
    }

    /**
     *  return paginate Collection.
     */
    function paginateCollection($collection, $perPage, $pageName = 'page', $fragment = null)
    {
        $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage($pageName);

        // LengthAwarePaginator::
        $currentPageItems = $collection->slice(($currentPage - 1) * $perPage, $perPage);

        parse_str(request()->getQueryString(), $query);

        unset($query[$pageName]);

        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(

            $currentPageItems,

            $collection->count(),

            $perPage,

            $currentPage,
            [
                'pageName' => $pageName,

                'path' => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath(),

                'query' => $query,

                'fragment' => $fragment
            ]
        );

        return $paginator;
    }

    /**
     * calculate Travel Insurance Fee
     *
     * @return \Illuminate\Http\Response
     */
    public
    function calcTravelInsuranceFee($travel_insurance_amount, $currency, $travel_insurance_tariff_amount, $travel_insurance_percent, $term_coefficient, $travel_age_coefficient)
    {
        $coefficient = $this->loan_currencies_types[$currency]["extra"];

        $insuranceFee = 0;

        if (floatval($travel_insurance_tariff_amount) > 0) {
            $insuranceFee = $insuranceFee + $travel_insurance_tariff_amount;
//            $insuranceFee = $insuranceFee + $coefficient * $travel_insurance_tariff_amount;
        }

        if (floatval($travel_insurance_percent) > 0) {

            $insurance_percent_calc_amount = $travel_insurance_amount * $travel_insurance_percent / 100;

            $insurance_percent_calc_amount_converted_amd = $coefficient * $insurance_percent_calc_amount;

            $insuranceFee = $insuranceFee + $insurance_percent_calc_amount_converted_amd;
        }

        if (floatval($term_coefficient) > 0) {
            $insuranceFee = $insuranceFee * $term_coefficient;
        }

        if (floatval($travel_age_coefficient) > 0) {
            $insuranceFee = $insuranceFee * $travel_age_coefficient;
        }


        return $insuranceFee;
    }


    /**
     * check Product Params Calculable
     *
     * @return \Illuminate\Http\Response
     */
    public
    function checkProductParamsCalculableErr($belonging_id, $product, $loan_amount, $cost = null)
    {
        if ($product->percentage_type == 1 || $product->percentage_type == 2) {
            $percentage = $product->percentage_fixed;
        } else {
            $percentage = $product->percentage_changing_from;
        }

        $loan_amount_plus_percents = $loan_amount + $percentage * $loan_amount / 100;

        $loanApplicationFee = $this->getFeeFinal($product->loan_application_absolute_amount, $product->loan_application_survey_pay_from, $product->loan_application_survey_pay_to,
            1, $loan_amount, $loan_amount_plus_percents, $cost); /*loan Application Fee*/

        $loanServiceFee = $this->getFeeFinal($product->loan_service_absolute_amount, $product->loan_service_pay_from, $product->loan_service_pay_to, $product->loan_service_pay_type,
            $loan_amount, $loan_amount_plus_percents, $cost); /*loan Service Fee*/

        $loanCollateralAssessmentFee = $this->getFeeFinal($product->pledge_assessment_absolute_amount, $product->pledge_assessment_pay_from, $product->pledge_assessment_pay_to,
            1, $loan_amount, $loan_amount_plus_percents, $cost); /*loan Collateral Assessment Fee*/

        $loanCashServiceFee = $this->getFeeFinal($product->cashing_pay_absolute_amount, $product->cashing_pay_from, $product->cashing_pay_to,
            1, $loan_amount, $loan_amount_plus_percents, $cost); /*loan Cash Service Fee*/

        $loanCollateralInsuranceFee = $this->getFeeFinal($product->pledge_insurance_absolute_amount, $product->pledge_insurance_pay_from, $product->pledge_insurance_pay_to,
            $product->pledge_insurance_pay_type, $loan_amount, $loan_amount_plus_percents, $cost); /*loan Collateral Insurance Fee*/

        $loanNotaryValidationFee = $this->getFeeFinal($product->notarial_ratification_pay, $product->notarial_ratification_pay_from, $product->notarial_ratification_pay_to,
            1, $loan_amount, $loan_amount_plus_percents, $cost); /*loan Notary Validation Fee*/

        $main_require_payments = $loanApplicationFee + $loanServiceFee + $loanCollateralAssessmentFee + $loanCashServiceFee + $loanCollateralInsuranceFee + $loanNotaryValidationFee;

        $other_require_payments = 0;

        if ($product->other_payments == 1) {

            foreach ($product->otherPayments as $productCurrOtherPayment) {

                $currOtherFee = $this->getFeeFinal($productCurrOtherPayment->other_payments_amount, $productCurrOtherPayment->other_payments_from, $productCurrOtherPayment->other_payments_to,
                    $productCurrOtherPayment->other_payments_sum_percent_type, $loan_amount, $loan_amount_plus_percents, $cost);

                $other_require_payments = $other_require_payments + $currOtherFee;
            }
        }

        $require_payments = $main_require_payments + $other_require_payments;

        if ($require_payments * 2 >= $loan_amount) {
            $calculable_err = 1; // if error exists
        } else {
            $calculable_err = 0; // if no error exists
        }

        return $calculable_err;
    }


    /*
     *get Fee Final Absolute Value
     */
    public
    function
    getMainRequireFeesSum()
    {

    }

    /*
     *get Fee Final Absolute Value
     */
    public
    function getFeeFinal($fee, $min, $max, $pay_type, $loan_amount, $loan_amount_plus_percents, $cost)
    {
        if (strpos($fee, '%') !== false) {

            if ($pay_type == 3) {
                $amount = $cost;
            } else if ($pay_type == 4) {
                $amount = $loan_amount_plus_percents;
            } else {
                $amount = $loan_amount;
            }

            $feePercent = str_replace('%', '', $fee);

            $feePercent = floatval($feePercent);

            $fee = $feePercent * $amount / 100;

            if ($fee < $min) {
                $fee = $min;
            }

            if ($fee > intval($max) && intval($max) > 0) {
                $fee = $max;
            }
        } else {
            $fee = intval($fee);
        }

        return $fee;
    }

    /**
     * array Multisort by field values
     *
     * @return \Illuminate\Http\Response
     */
    public
    function arrayMultisort($arr, $sort_by_field)
    {
//        $arr = array(
//            array("type" => "pork", "price" => 5.43),
//            array("type" => "fruit", "price" => 3.50),
//            array("type" => "milk", "price" => 2.90),
//        );
        //$sort_by_field = "price";

        $fields = array();

        foreach ($arr as $key => $row) {
            $fields[$key] = $row["$sort_by_field"];
        }

        array_multisort($fields, SORT_ASC, $arr);

        return $arr;
    }

    /**
     * array Multidemsional Sum count Column,which is array
     *
     * @return \Illuminate\Http\Response
     */
    public
    function arrayMultiCountColumn($arr_multidemsional, $column)
    {
        $totalCountColumn = array_sum(array_map(function ($item) use ($column) {
            return count($item[$column]);
        }, $arr_multidemsional));

        return $totalCountColumn;
    }


}