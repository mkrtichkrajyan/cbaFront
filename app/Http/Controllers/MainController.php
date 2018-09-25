<?php

namespace App\Http\Controllers;

use App\Models\CarType;
use App\Models\CreditPurposeTypes;
use App\Models\CreditsPurpose;
use App\Models\GoldAssayType;
use App\Models\PercentageType;
use App\Models\ProductsGoldAssayType;
use App\Models\ProductsSecurityType;
use App\Models\ProvidingType;
use App\Models\PurposeType;
use App\Models\RepaymentType;
use App\Models\SecurityType;
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
                        ->where('belonging_id', 2)
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