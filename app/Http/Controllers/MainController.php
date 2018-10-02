<?php

namespace App\Http\Controllers;

use App\Models\CarType;
use App\Models\CreditPurposeTypes;
use App\Models\CreditsPurpose;
use App\Models\DepositCapitalizationsList;
use App\Models\DepositInterestRatesPayment;
use App\Models\DepositsSpecialsList;
use App\Models\DepositTypesList;
use App\Models\GoldAssayType;
use App\Models\LoanRefinancingPurposeType;
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
use App\Models\ProductsGoldAssayType;
use App\Models\ProductsLoanRefinancingPurposeType;
use App\Models\ProductsPaymentCardsCardType;
use App\Models\ProductsPaymentCardsCurrency;
use App\Models\ProductsPaymentCardsExtraType;
use App\Models\ProductsPaymentCardsRegion;
use App\Models\ProductsPaymentCardsType;
use App\Models\ProductsSecurityType;
use App\Models\ProductsSpecialsCardsType;
use App\Models\ProvidingType;
use App\Models\PurposeType;
use App\Models\RepaymentType;
use App\Models\SecurityType;
use App\Models\TransferBank;
use App\Models\TransferSystem;
use App\Models\YesNo;
use Illuminate\Http\Request;


class MainController extends Controller
{
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
                    $percentage_types_arr[] = array(
                        "id" => $percentage_type->id,
                        "info" => $percentage_type,
                        "count" => $products->whereIn('percentage_type', array($percentage_type->id, 1))->count());
                }
            }

            $data["percentage_types"] = $percentage_types_arr;

            $providing_types = ProvidingType::all();

            $providing_types_arr = [];

            foreach ($providing_types as $providing_type) {
                if ($providing_type->id != 1) {
                    $providing_types_arr[] = array(
                        "id" => $providing_type->id,
                        "info" => $providing_type,
                        "count" => $products->whereIn('providing_type', array($providing_type->id, 1))->count());
                }
            }

            $data["providing_types"] = $providing_types_arr;

            $repayment_types = RepaymentType::all();

            $repayment_types_arr = [];

            if ($belonging_id == 1) {
                foreach ($repayment_types as $repayment_type) {
                    if ($repayment_type->id != 1) {
                        $repayment_types_arr[] = array(
                            "id" => $repayment_type->id,
                            "info" => $repayment_type,
                            "count" => $products->whereIn('checked_repayment_types', array($repayment_type->id, 1))->count());
                    }
                }
            } else {
                foreach ($repayment_types as $repayment_type) {
                    if ($repayment_type->id != 1) {
                        $repayment_types_arr[] = array(
                            "id" => $repayment_type->id,
                            "info" => $repayment_type,
                            "count" => $products->whereIn('repayment_type', array($repayment_type->id, 1))->count());
                    }
                }
            }

            $data["repayment_types"] = $repayment_types_arr;
        }

        if ($belonging_id == 2) {
            $gold_assay_types = GoldAssayType::all();

            $gold_assay_types_arr = [];

            foreach ($gold_assay_types as $gold_assay_type) {
                $gold_assay_types_arr[] = array(
                    "id" => $gold_assay_type->id,
                    "info" => $gold_assay_type,
                    "count" => ProductsGoldAssayType::where('gold_assay_type_id', $gold_assay_type->id)
                        ->where('belonging_id', $belonging_id)
                        ->whereIn('product_id', $product_ids)->count());
            }

            $data["gold_assay_types"] = $gold_assay_types_arr;
        }

        if ($belonging_id == 5) {
            $purposeTypes = PurposeType::all();

            $purpose_types_arr = [];

            foreach ($purposeTypes as $purposeType) {
                $purpose_types_arr[] = array(
                    "id" => $purposeType->id,
                    "info" => $purposeType,
                    "count" => $products->whereIn('checked_purposes', array($purposeType->id, 1))->count());
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
                        "count" => $products->whereIn('car_type', array($car_type->id, 1))->count());
                }
            }

            $data["car_types"] = $car_types_arr;
        }

        if ($belonging_id == 3) {

            $creditPurposeTypes = CreditPurposeTypes::all();

            $creditPurposeTypes_arr = [];

            foreach ($creditPurposeTypes as $creditPurposeType) {
                $creditPurposeTypes_arr[] = array(
                    "id" => $creditPurposeType->id,
                    "info" => $creditPurposeType,
                    "count" => CreditsPurpose::where('purpose_type', $creditPurposeType->id)
                        ->whereIn('product_id', $product_ids)->count());
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

        $security_types = SecurityType::all();

        $security_types_arr = [];

        foreach ($security_types as $security_type) {
            $security_types_arr[] = array(
                "id" => $security_type->id,
                "info" => $security_type,
                "count" => ProductsSecurityType::where('security_type', $security_type->id)
                    ->where('belonging_id', $belonging_id)
                    ->whereIn('product_id', $product_ids)->count());
        }

        $data["security_types"] = $security_types_arr;

        return $data;
    }

    /**
     * simple selectbox view example
     *
     * @return \Illuminate\Http\Response
     */
    public
    function createSelectBox(Request $request)
    {
        $countries = Country::all();

        $viewData = [
            "countries" => $countries,
        ];

        return view('testSelectBox', $viewData);
    }
}