@extends('layouts.default')

@include('layouts.head')

@include('layouts.headerCompare')
{{--@php($checked_variations =  $getCompareInfo[$belonging_id]["checked_variations"] )--}}

<main>
    <div class="back-fon" style="height:200px;">

    </div>
    <input type="hidden" value="{{backend_asset_path()}}" id="backend_asset_path" name="backend_asset_path"/>

    <div class="row">
        <div class="columns large-12 medium-12 small-12">
            <div class="compare_title">Համեմատություն</div>
        </div>
        <div class="columns large-12 medium-12 small-12">
            <div class="wrapper slider">
                <div class="compare_name_servis">
                    <div class="compare_name_title">
                        <i class="icon {{$currProductByBelongingsView->front_icon}}"></i>
                        <span>Ոսկու վարկեր</span>
                    </div>
                    <div class="compare_name_podtitle">
                        Գրավ
                    </div>
                    <div class="compare_name_podtitle gold_assay_types_podtitle">
                        Ոսկյա իրերի հարգ
                    </div>
                    <div class="compare_name_podtitle">
                        Վարկի գումար
                    </div>
                    <div class="compare_name_podtitle">
                        Վարկի ժամկետ
                    </div>
                    <div class="compare_name_podtitle">
                        Փաստացի տոկոսադրույք
                    </div>
                    <div class="compare_name_podtitle privileged_term_podtitle">
                        Արտոնյալ ժամկետ
                    </div>
                    <div class="compare_name_podtitle repayment_podtitle">
                        Մարման եղանակ
                    </div>
                    <div class="compare_name_podtitle">
                        Տրամադրման եղանակ
                    </div>
                    <div class="compare_name_podtitle percentage_podtitle">
                        Տոկոսադրույք
                    </div>

                    <div class="compare_name_podtitle assessment_assays_podtitle">
                        Գնահատման արժեքներ
                    </div>

                    <div class="compare_name_podtitle compare_name_podtitle_mid">
                        Վարկային հայտի ուսումնասիրության վճար(միանվագ)
                    </div>
                    <div class="compare_name_podtitle compare_name_podtitle_mid">
                        Վարկի սպասարկման վճար(տարեկան)
                    </div>
                    <div class="compare_name_podtitle compare_name_podtitle_mid">
                        Գրավի գնահատման վճար(միանվագ)
                    </div>
                    <div class="compare_name_podtitle">
                        Կանխիկացման վճար
                    </div>
                    <div class="compare_name_podtitle compare_name_podtitle_mid">
                        Գրավի պահպանման վճար (միանվագ)
                    </div>
                    <div class="compare_name_podtitle other_payments_podtitle">
                        Այլ վճարներ
                    </div>


                    <div class="compare_name_podtitle compare_name_podtitle_mid">
                        Վարկի մայր գումարը չվճարելու դեպքում
                    </div>
                    <div class="compare_name_podtitle compare_name_podtitle_mid">
                        Տոկոսագումարները չվճարելու դեպքում
                    </div>
                    <div class="compare_name_podtitle compare_name_podtitle_mid">
                        Այլ վճարները չկատարելու դեպքում
                    </div>
                    <div class="compare_name_podtitle compare_name_podtitle_mid">
                        Այլ
                    </div>
                    <div class="compare_name_podtitle compare_name_podtitle_mid">
                        Ծառայությունը ընկերության կայքէջում
                    </div>
                    <div class="compare_name_podtitle more_information_podtitle">
                        Լրացուցիչ տեղեկատվություն
                    </div>
                </div>
                <div class="slider-info">
                    <div class="owl-carousel owl-theme">
                        @foreach($compareVariationsData as $compareVariationsDataCurr)

                            @php($product   =   $compareVariationsDataCurr["product_info"])

                            @php($variation   =   $compareVariationsDataCurr["variation_info"])

                            <div class="slider-item">
                                <div class="compare_name">
                                    <div class="compare_name_title servis_name_scroll">
                                        <div class="close">
                                            <i class="icon icon-x"></i>
                                        </div>
                                        <div class="compare-item">
                                            <img src="{{ backend_asset('savedImages/'.$product->companyInfo->image )}}">
                                            <span>{{$product->name}}</span>
                                        </div>
                                    </div>
                                    <div class="servis_name">
                                        @if(!is_null($product->goldPledgeTypeInfo))
                                            {{$product->goldPledgeTypeInfo->name}}
                                        @endif
                                    </div>
                                    <div class="servis_name servis_name_gold_assay_types">
                                        @php($goldAssayTypeArr = [])

                                        @if(!is_null($product->goldAssayTypes) && $product->goldAssayTypes->count() >0)
                                            @foreach($product->goldAssayTypes as $goldAssayType)
                                                @php($goldAssayTypeArr[] = $gold_assay_types->find($goldAssayType->gold_assay_type_id)->name)
                                            @endforeach
                                            <span>{{implode(',',$goldAssayTypeArr) }}</span>
                                        @endif
                                    </div>
                                    <div class="servis_name">
                                        {{$compareVariationsDataCurr["loan_amount"]}}
                                    </div>
                                    <div class="servis_name">
                                        {{$compareVariationsDataCurr["term"]}} Օր
                                    </div>
                                    <div class="servis_name">
                                        {{round($compareVariationsDataCurr["factual_percentage"],2)}}
                                    </div>
                                    <div class="servis_name servis_name_privileged_term">
                                        @if($product->privileged_term == 1)
                                            <p class="standart_p">Վարկ
                                                - {{$product->privileged_term_loan}} {{$time_types->find($product->privileged_term_loan_time_type)->name}}</p>

                                            <p class="standart_p">Տոկոս
                                                - {{$product->privileged_term_percentage}} {{$time_types->find($product->privileged_term_loan_time_type)->name}}</p>
                                        @else
                                            -
                                        @endif
                                    </div>

                                    <div class="servis_name servis_name_repayment_type">
                                        <p class="standart_p">{{$repayment_types->find($variation->repayment_type)->name}}</p>

                                        @if($variation->repayment_type == 3)
                                            <span>Վարկ: {{$repayment_loan_interval_types->find($variation->repayment_loan_interval_type_id)->name}}</span>

                                            <span>Տոկոս: {{$repayment_percent_interval_types->find($variation->repayment_percent_interval_type_id)->name}}</span>
                                        @endif
                                    </div>

                                    <div class="servis_name">
                                        {{$variation->providing_type}}
                                        <span> {{@$providing_types->find($variation->providing_type)->name}}</span>
                                    </div>
                                    <div class="servis_name servis_name_percentage">
                                        <span> {{$percentage_types->find($variation->percentage_type)->name}}</span>

                                        @if($variation->percentage_type == 2)
                                            <p class="standart_p">{{$product->percentage_fixed}}</p>
                                        @else
                                            <p class="standart_p">
                                                {{$product->percentage_changing_from}}{{@$productPercentageTypesArr[$product->percentage_changing_in]}}{{$product->percentage_changing_to}}
                                                @if($product->percentage_changing_2 == 1 )
                                                    ,{{ $product->percentage_changing_from_2. " " .$productPercentageTypesArr[$product->percentage_changing_in_2]." ".$product->percentage_changing_to_2 }}
                                                @endif
                                            </p>
                                        @endif
                                    </div>

                                    <div class="servis_name servis_name_assessment_assays">
                                        <div class="assessment_assays_info_sexion">
                                            @if($product->assessment_assay_exist == 1)
                                                <ul>
                                                    @foreach($product->goldLoanAssessmentAssaysValues as $productCurrGoldLoanAssessmentAssaysValues)
                                                        <li>
                                                            <div class="assessment_assays_current">
                                                                {{$productCurrGoldLoanAssessmentAssaysValues->assessment_assay}}
                                                                ->
                                                                {{$productCurrGoldLoanAssessmentAssaysValues->assessment_assay_value}}
                                                            </div>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                -
                                            @endif
                                        </div>
                                    </div>

                                    <div class="servis_name servis_name_mid">
                                        <span>{{$product->loan_application_absolute_amount}}</span>
                                        @if(strpos($product->loan_application_absolute_amount, "%") != false )
                                            {{$product->loan_application_survey_pay_from}}
                                            - {{$product->loan_application_survey_pay_to}}
                                        @endif
                                    </div>
                                    <div class="servis_name servis_name_mid">
                                        <span>{{$product->loan_service_absolute_amount}}</span>
                                        @if(strpos($product->loan_service_absolute_amount, "%") != false )
                                            {{$loan_service_pay_types->find($product->loan_service_pay_type)->name}}
                                            {{$product->loan_service_pay_from}} - {{$product->loan_service_pay_to}}
                                        @endif
                                    </div>
                                    <div class="servis_name servis_name_mid">
                                        <span>{{$product->pledge_assessment_absolute_amount}}</span>
                                        @if(strpos($product->pledge_assessment_absolute_amount, "%") != false )
                                            {{$product->pledge_assessment_pay_from}}
                                            - {{$product->pledge_assessment_pay_to}}
                                        @endif
                                    </div>
                                    <div class="servis_name">
                                        <span>{{$product->cashing_pay_absolute_amount}}</span>
                                        @if(strpos($product->cashing_pay_absolute_amount, "%") != false )
                                            {{$product->cashing_pay_from}}
                                            - {{$product->cashing_pay_to}}
                                        @endif
                                    </div>

                                    <div class="servis_name servis_name_mid">
                                        <span>{{$product->pledge_keep_absolute_amount}}</span>
                                        @if(strpos($product->pledge_keep_absolute_amount, "%") != false )
                                            {{$absolute_amount_or_percent_only_pay_types->find($product->pledge_keep_pay_type)->name}}
                                            {{$product->pledge_keep_pay_from}} - {{$product->pledge_keep_pay_to}}
                                        @endif
                                    </div>

                                    <div class="servis_name servis_name_other_payments">
                                        <div class="other_payments_info_sexion">

                                            @if($product->other_payments == 1)
                                                <ul>
                                                    @foreach($product->otherPayments as $productCurrOtherPayment)
                                                        <li>
                                                            <div class="other_payment_current">
                                                                <span>{{$productCurrOtherPayment->other_payments_name}}</span>

                                                                <span>{{$periodicity_types->find($productCurrOtherPayment->other_payments_periodicity)->name}}</span>

                                                                <span>{{$productCurrOtherPayment->other_payments_amount}}</span>

                                                                @if(strpos($productCurrOtherPayment->other_payments_amount, "%") != false )
                                                                    {{@$absolute_amount_or_percent_pay_types->find($productCurrOtherPayment->other_payments_sum_percent_type)->name}}
                                                                    @if(!is_null($productCurrOtherPayment->other_payments_from) || !is_null($productCurrOtherPayment->other_payments_to))
                                                                        <span>
                                                                {{$productCurrOtherPayment->other_payments_from}}
                                                                            - {{$productCurrOtherPayment->other_payments_to}}</span>
                                                                    @endif
                                                                @endif
                                                            </div>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                -
                                            @endif
                                        </div>
                                    </div>

                                    <div class="servis_name servis_name_mid servis_name_scroll">
                                        <span> {{$product->loan_main_amount_non_payment_case}} </span>
                                    </div>

                                    <div class="servis_name servis_name_mid servis_name_scroll">
                                        <span> {{$product->percentage_sum_non_payment_case}} </span>
                                    </div>

                                    <div class="servis_name servis_name_mid servis_name_scroll">
                                        <span> {{$product->another_non_payments_case}} </span>
                                    </div>

                                    <div class="servis_name servis_name_mid servis_name_scroll">
                                        <span> {{$product->other_non_payment}} </span>
                                    </div>

                                    <div class="servis_name servis_name_mid servis_name_scroll">
                                        @if(strlen($product->service_on_company_website) > 0)
                                            <a class="service_on_company_website" target="_blank"
                                               href="{{$product->service_on_company_website}}">{{$product->service_on_company_website}}</a>
                                        @else
                                            -
                                        @endif
                                    </div>

                                    <div class="servis_name servis_name_more_information">
                                        <div class="more_information_info_sexion">
                                            {!! $product->more_information !!}
                                        </div>
                                    </div>
                                </div>


                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

</main>


<script type="text/javascript">
    $(document).ready(function () {

        //$.ajaxSetup({headers: {'csrftoken': '{{ csrf_token() }}'}});
    });
</script>

@include('layouts.footer')