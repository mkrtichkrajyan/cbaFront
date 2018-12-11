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
use App\Models\LoanCurrenciesType;
use App\Models\LoanRefinancingPurposeType;
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
use App\Models\YesNo;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use S1calculate\Loan\Finance\Finance;


class MainController extends Controller
{
    public $loan_currencies_types = [];

    public function __construct()
    {
        $this->getloanCurrenciesTypes();
    }

    /**
     * Get Compare Info.
     *
     * @return \Illuminate\Http\Response
     */
    public function getCompareInfo()
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

                $curr_belonging_compare_count = count($curr_belonging_cookie_val);

                if ($curr_belonging_compare_count > 0) {
                    $curr_belonging_compare_display = "flex";
                } else {
                    $curr_belonging_compare_display = "none";
                }

                $curr_belonging_compare_checked_variations = array_keys($curr_belonging_cookie_val);
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


    public function getloanCurrenciesTypes()
    {
        $loan_currencies_types = LoanCurrenciesType::all()->keyBy('id')->toArray();

        $this->loan_currencies_types = $loan_currencies_types;
    }

    /*
     *get Calculation Results
     */
    public function getCalculation($product, $product_variation, $cost, $loan_amount, $loan_term_search_in_days, $prepayment_percent, $time_type = 2)
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
            } else {
                $loanServiceFeeFix = $loanServiceFee;

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
            } else {
                $loanCashServiceFeeFix = $loanCashServiceFee;

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
//
//            if($time_type   ==  1){
//
//                    $loan_term_in_months = ceil($loan_term_search_in_days / 30);
//
//                    //$loan_term_in_years
//
//                dd(fmod(365,365) == 0);
//            }
//
//            $loan_term_search_in_days = $loan_term_in_months


            $r = new Finance();

            $r->setLoanpledge($cost);

            $r->setLoanamount($loan_amount);

            $r->setLoanperiod($loan_term_search_in_days);

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

                $r->setLoanGracePeriodPrincipal($privileged_term_loan_months);

                $r->setLoanGracePeriodInterest($privileged_term_percentage_months);
            }

            $r->setLoanApplicationFee($loanApplicationFeeFix, $loanApplicationFeePercent, $product->loan_application_survey_pay_from, $product->loan_application_survey_pay_to, 1);

            $r->setLoanServiceFee($loanServiceFeeFix, $loanServiceFeePercent, $product->loan_service_pay_from, $product->loan_service_pay_to, $loan_service_pay_type);

            $r->setLoanCollateralAssessmentFee($loanCollateralAssessmentFeeFix, $loanCollateralAssessmentFeePercent, $product->pledge_assessment_pay_from, $product->pledge_assessment_pay_to, 1);

            $r->setLoanCollateralInsuranceFee($loanCollateralInsuranceFeeFix, $loanCollateralInsuranceFeePercent, $product->pledge_insurance_pay_from, $product->pledge_insurance_pay_to, $pledge_insurance_pay_type);

            $r->setLoanNotaryValidationFee($loanNotaryValidationFeeFix, $loanNotaryValidationFeePercent, $product->notarial_ratification_pay_from, $product->notarial_ratification_pay_to, 1);

            $r->setLoanCashServiceFee($loanCashServiceFeeFix, $loanCashServiceFeePercent, $product->cashing_pay_from, $product->cashing_pay_to, 1);

            if ($product->other_payments == 1) {

                foreach ($product->otherPayments as $productCurrOtherPayment) {

                    $loanOtherFee = $productCurrOtherPayment->other_payments_amount;

                    if (strpos($loanOtherFee, '%') !== false) {

                        $loanOtherFeeFix = 0;

                        $loanOtherFeePercent = str_replace('%', '', $loanOtherFee);
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

            $result = $r->Calculate();

            $schedules = $result["schedule"];

            $main_require_payments = array_sum($result["other_fee"]);

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
            //dd($result);
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
                        ->whereIn('product_id', $product_ids)->whereIn('percentage_type', array($percentage_type->id, 1))->count();

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
                        ->whereIn('product_id', $product_ids)->whereIn('providing_type', array($providing_type->id, 1))->count();

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
                        ->whereIn('product_id', $product_ids)->whereIn('repayment_type', array($repayment_type->id, 1))->count();

                    $repayment_types_arr[] = array(
                        "id" => $repayment_type->id,
                        "info" => $repayment_type,
                        "count" => $count
                    );
                }
            }

            $data["repayment_types"] = $repayment_types_arr;


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

        $data["special_project_statuses"] = $special_project_statuses_arr;

        $data["privileged_term_statuses"] = $privileged_term_statuses_arr;

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
    function checkProductParamsCalculableErr($belonging_id, $product, $loan_amount, $cost)
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
}