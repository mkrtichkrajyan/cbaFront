@extends('layouts.default')

@include('layouts.head')

@include('layouts.headerCompare')

<input type="hidden" id="more_payment_amount_piece" name="more_payment_amount_piece"
       value="{{$more_payment_amount_piece}}">

<input type="hidden" id="loan_amount_piece" name="loan_amount_piece"
       value="{{$loan_amount_piece}}">

<input type="hidden" value="{{backend_asset_path()}}" id="backend_asset_path" name="backend_asset_path">

@php($checked_variations =  $getCompareInfo[$belonging_id]["checked_variations"] )

@if($product_variation->percentage_type == 2)
    @php($percentage    =   $product_variation->percentage)
@else
    @php($percentage    =   $product->percentage_changing_from.@$productPercentageTypesArr[$product->percentage_changing_in].$product->percentage_changing_to)

    @if($product->percentage_changing_2 == 1 )
        @php($percentage.= ",".$product->percentage_changing_from_2.$productPercentageTypesArr[$product->percentage_changing_in_2].$product->percentage_changing_to_2)
    @endif
@endif


<main>
    <div class="row" style="position: relative;">
        <div class="back-fon position">

        </div>
        <div class="columns large-12 medium-12 small-12">
            <div class="listing-title product-listing">
                <div class="left">
                    <a href="" class="come-back product-come-back title-map">
                        <div class="come-back-icon">
                            <i class="icon icon-back-arrow"></i>
                        </div>
                        {{--<span>Պրոդուկտ</span>--}}
                    </a>
                </div>
                <div class="right">
                    <div class="listing-icon prodCompanyImg">
                        <img src="{{backend_asset('savedImages/'.@$product->companyInfo->image )}}" alt="">
                    </div>
                </div>
            </div>
        </div>

        <div class="columns large-12 medium-12 small-12">
            <div class="listing-title product-wrapper">
                <div class="left">
                    <section class="inform-wrapper">
                        <div class="product-title">
                            {{$product->name}}
                        </div>
                        <div class="product-inform">
                            {!! $product->more_information !!}
                            <div class="blurring"></div>
                        </div>
                        <div class="more-info">
                            <button class="btn-text-blue">
                                <span>ավելին</span>
                                <i class="icon icon-arrow-down"></i>
                            </button>
                        </div>
                    </section>
                </div>
                <div class="right">
                    <div class="add-functions">
                        @php($unique_options    =   "bel_".$belonging_id."_prod_".$product->id."_prov_" .$product_variation->providing_type."_perc_".
                         $product_variation->percentage_type."_rep_" . $product_variation->repayment_type."_rep_loan_" .
                         intval($product_variation->repayment_loan_interval_type_id) . "_rep_perc_" .intval($product_variation->repayment_percent_interval_type_id))

                        @php($unique_options_and_search_params  =   $unique_options."*"."_loan_amount_".$loan_amount. "_term_".$loan_term_search_in_days)

                        @php($unique_options_and_search_params    =   str_rot13($unique_options_and_search_params))

                        @php($unique_options    =   md5($unique_options))

                        <button type="button" data-options="{{$unique_options}}"
                                data-options_and_search_params="{{$unique_options_and_search_params}}"
                                data-belongingId="{{$belonging_id}}"
                                data-product-id="{{$product->id}}"
                                data-loan_amount="{{$loan_amount}}"
                                data-term="{{$loan_term_search_in_days}}"
                                class="btn btn_compare btn-white  @if(in_array($unique_options,$checked_variations)) compare_act_button_checked @endif">

                            <i class="icon icon-left  icon-add"></i>
                            <span>համեմատել</span>
                        </button>
                        <a href="{{url('/company-branches-and-bankomats/'.$product->company_id)}}" class="btn btn-more">
                            <section class="right-border">
                                <i class="icon icon-location">
                                </i>
                            </section>
                            <span>Մասնաճյուղեր և Բանկոմատներ</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="margins wrapper-diogram columns large-12 medium-12 small-12">
            <div class="wrapper diogram-cont">
                <div class="diogramma">
                    <div class="diogram">
                        <div class="chart-container">
                            <div id="pieChart">
                                <svg id="pieChartSVG">
                                    <defs>
                                        <filter id='pieChartInsetShadow'>
                                            <feOffset dx='0' dy='0'/>
                                            <feGaussianBlur stdDeviation='3' result='offset-blur'/>
                                            <feComposite operator='out' in='SourceGraphic' in2='offset-blur'
                                                         result='inverse'/>
                                            <feFlood flood-color='black' flood-opacity='1' result='color'/>
                                            <feComposite operator='in' in='color' in2='inverse' result='shadow'/>
                                            <feComposite operator='over' in='shadow' in2='SourceGraphic'/>
                                        </filter>
                                        <filter id="pieChartDropShadow">
                                            <feGaussianBlur in="SourceAlpha" stdDeviation="3" result="blur"/>
                                            <feOffset in="blur" dx="0" dy="3" result="offsetBlur"/>
                                            <feMerge>
                                                <feMergeNode/>
                                                <feMergeNode in="SourceGraphic"/>
                                            </feMerge>
                                        </filter>
                                    </defs>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="price-wrap">
                    <div class="all-price">
                        <div class="all-price-title">Վարկի գումար</div>
                        <div class="all-total-price">
                            <span>{{$loan_amount_converted}}</span>
                            <i class=" icon icon-dram"></i>
                        </div>
                    </div>
                    <div class="pircent-price">
                        <div class="all-price-title">Ավել վճարվող գումար</div>
                        <div class="all-total-price">
                            <span>{{$more_payment_amount}}</span>
                            <i class=" icon icon-dram"></i>
                        </div>
                    </div>
                </div>

                <div class="end-price">
                    <div>
                        <div class="all-price-title">Հետ վճարվող գումար</div>
                        <div class="all-total-price">
                            <span>{{$sum_payments}}</span>
                            <i class=" icon icon-dram"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="final-settlement">
                <div class="actual-percent">
                    <div class="all-price-title">Փաստացի տոկոսադրույք</div>
                    <div class="all-total-price">
                        <span class="chart-count-1">{{$factual_percentage}}</span>
                    </div>
                </div>
                <div class="nominal-percent">
                    <div class="all-price-title">Անվանական տոկոսադրույք</div>
                    <div class="all-total-price">
                        <span class="chart-count-2">{{$percentage}}%</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="columns large-12 medium-12 small-12">
            <div class="wrapper margins prise">
                <div class="listing-title">
                    <div class="left">
                        <div class="document-title">
                            <span>Պարտադիր վճարներ</span>
                        </div>
                    </div>

                    <div class="right">
                        <div class="chenge-wrap">
                            <button class="chenge_req">
                                <span>Տարեկան</span>
                            </button>
                            <button class="chenge_req">
                                <span>Ընդհանուր</span>
                            </button>
                        </div>
                    </div>
                </div>


                <div class="change_item_req">
                    <div class="prise-cont ">
                        <div class="product-prise-wrapper">

                            @foreach($require_payments_schedule_annually_and_summary as $curr_require_payments_schedule_annually_and_summary)

                                <div class="prise-title">
                                    <div class="left product-prise">
                                        <span>{{$curr_require_payments_schedule_annually_and_summary["name"]}}</span>
                                    </div>
                                    <div class="right product-prise">
                                        <span>{{$curr_require_payments_schedule_annually_and_summary["anually"]}}</span>
                                        <i class="icon  icon-dram"></i>
                                    </div>
                                </div>

                            @endforeach

                        </div>
                    </div>
                    <div class="total-prise-title">
                        <div class="left total-prise">
                            <span>Ընդհանուր գումար</span>
                        </div>
                        <div class="right total-prise">
                            <span>{{$require_payments_anually_sum}}</span>
                            <i class="icon  icon-dram"></i>
                        </div>
                    </div>
                </div>

                <div class="change_item_req">
                    <div class="prise-cont ">
                        <div class="product-prise-wrapper">

                            @foreach($require_payments_schedule_annually_and_summary as $curr_require_payments_schedule_annually_and_summary)

                                <div class="prise-title">
                                    <div class="left product-prise">
                                        <span>{{$curr_require_payments_schedule_annually_and_summary["name"]}}</span>
                                    </div>
                                    <div class="right product-prise">
                                        <span>{{$curr_require_payments_schedule_annually_and_summary["summary"]}}</span>
                                        <i class="icon  icon-dram"></i>
                                    </div>
                                </div>

                            @endforeach

                        </div>
                    </div>
                    <div class="total-prise-title">
                        <div class="left total-prise">
                            <span>Ընդհանուր գումար</span>
                        </div>
                        <div class="right total-prise">
                            <span>{{$require_payments_full_sum}}</span>
                            <i class="icon  icon-dram"></i>
                        </div>
                    </div>
                </div>

            </div>
        </div>


        <div class="columns large-12 medium-12 small-12">
            <div class="wrapper margins">
                <div class="listing-title bank-listing-map">
                    <div class="gradient">
                    </div>
                    <div class="left">
                        <div class="map-chenge chenge active">
                            Հիմնական տվյալներ
                        </div>
                        <div class="map-chenge chenge">
                            Մարման գրաֆիկ
                        </div>
                    </div>
                </div>
                <div class="change_item item-scroll">
                    <div class="prise-cont other-info-wrapper">
                        <div class="product-prise-wrapper other-info">

                            <div class="prise-title">
                                <div class="left  other-info-title">
                                    <span>Նպատակ</span>
                                </div>
                                <div class="right other-info-text">

                                    @if($product->purposesInfo->count() > 0)
                                        @php($productPurposeTypesArr    =   [])

                                        @foreach($product->purposesInfo as $productCurrPurpose)
                                            @php($productPurposeTypesArr[]  =  $productCurrPurpose->currPurposeInfo->name )
                                        @endforeach

                                        <span> {{implode(', ',$productPurposeTypesArr)}}</span>
                                    @else
                                        -
                                    @endif
                                </div>
                            </div>

                            <div class="prise-title">
                                <div class="left  other-info-title">
                                    <span>Ապահովվածություն</span>
                                </div>
                                <div class="right other-info-text">

                                    @php($securityTypesArr  =   [])

                                    @foreach($product->securityTypes as $key=>$productSecurityType)
                                        @php($securityTypesArr[]  =  $productSecurityType->securityTypeInfo->name )
                                    @endforeach
                                    <span>{{implode(',',$securityTypesArr)}}</span>
                                </div>
                            </div>
                            <div class="prise-title">
                                <div class="left  other-info-title">
                                    <span>Մարման եղանակ</span>
                                </div>
                                <div class="right other-info-text repayment-info-text">
                                    <span>{{@$product_variation->repaymentTypeInfo->name}}</span></br>

                                    @if($product_variation->repayment_type == 3)
                                        <span class="standart_p">
                                            Վարկ: {{$repayment_loan_interval_types->find($product_variation->repayment_loan_interval_type_id)->name}}</span>

                                        <span class="standart_p">
                                            Տոկոս: {{$repayment_percent_interval_types->find($product_variation->repayment_percent_interval_type_id)->name}}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="prise-title">
                                <div class="left  other-info-title">
                                    <span>Տրամադրման եղանակ</span>
                                </div>
                                <div class="right other-info-text">
                                    <span>{{@$product_variation->providingTypeInfo->name}}</span>
                                </div>
                            </div>
                        </div>
                        <div class="product-prise-wrapper other-info">
                            {{--<div class="prise-title">--}}
                            {{--<div class="left  other-info-title">--}}
                            {{--<span>Ապահովվածություն</span>--}}
                            {{--</div>--}}
                            {{--<div class="right other-info-text">--}}

                            {{--@php($securityTypesArr  =   [])--}}

                            {{--@foreach($product->securityTypes as $key=>$productSecurityType)--}}
                            {{--@php($securityTypesArr[]  =  $productSecurityType->securityTypeInfo->name )--}}
                            {{--@endforeach--}}
                            {{--<span>{{implode(',',$securityTypesArr)}}</span>--}}
                            {{--</div>--}}
                            {{--</div>--}}
                            <div class="prise-title">
                                <div class="left  other-info-title">
                                    <span>Ծառայությունը ընկերության կայքէջում</span>
                                </div>
                                <div class="right other-info-text">
                                    @if(strlen($product->service_on_company_website) > 0)
                                        <a target="_blank"
                                           href="{{$product->service_on_company_website}}">{{$product->service_on_company_website}}</a>
                                    @else
                                        -
                                    @endif
                                </div>
                            </div>

                            <div class="prise-title">
                                <div class="left  other-info-title">
                                    <span>Տոկոսադրույք</span>
                                </div>
                                <div class="right other-info-text">
                                    <span>{{@$product_variation->percentageTypeInfo->name}}՝ {{$percentage}}</span>
                                </div>
                            </div>

                            <div class="prise-title">
                                <div class="left  other-info-title">
                                    <span>Վարկ/գրավ հարաբերակցություն</span>
                                </div>
                                <div class="right other-info-text">
                                    <span>{{$product->loan_pledge_ratio}}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="change_item item-scroll">
                    <table class="prise-table">
                        <tr>
                            <th>
                                Ամիս
                            </th>
                            <th>
                                Վարկի Մնացորդ
                            </th>
                            <th>
                                Վճարվող Տոկոսագումար
                            </th>
                            <th>
                                Մարում Վարկից
                            </th>
                            <th>
                                Այլ վճարներ
                            </th>
                            <th>
                                Ընդամենը Վճարում
                            </th>
                        </tr>
                        <tr class="highlight">
                            <th>

                            </th>
                            <th>

                            </th>
                            <th>

                            </th>
                            <th>

                            </th>
                            <th>

                            </th>
                            <th>

                            </th>
                        </tr>

                        @foreach($getCalculation["schedule"] as $key => $currSchedule)

                            @php($currScheduleOtherPaymentsFilterNoKeys = ["principal_balance","monthly_interest","monthly_principal_amount","loan_pay_day"])

                            @php($currScheduleSumPaymentsFilterNoKeys = ["principal_balance","loan_pay_day"])

                            @php($currScheduleCollect = collect($currSchedule))

                            @php($currScheduleOtherPayments = $currScheduleCollect->filter(function ($value, $key) use ($currScheduleOtherPaymentsFilterNoKeys){
                                    return !in_array($key,$currScheduleOtherPaymentsFilterNoKeys);
                                })
                            )

                            @php($currScheduleCopy = $currScheduleCollect->filter(function ($value, $key) use ($currScheduleSumPaymentsFilterNoKeys){
                                    return !in_array($key,$currScheduleSumPaymentsFilterNoKeys);
                                })
                            )

                            <tr>
                                <th>
                                    {{$key + 1}}
                                </th>
                                <th>
                                    {{$currSchedule["principal_balance"]}}
                                </th>
                                <th>
                                    {{$currSchedule["monthly_interest"]}}
                                </th>
                                <td>
                                    {{$currSchedule["monthly_principal_amount"]}}
                                </td>
                                <td>
                                    {{$currScheduleOtherPayments->sum()}}
                                </td>
                                <td>
                                    {{$currScheduleCopy->sum()}}
                                </td>
                            </tr>
                        @endforeach


                    </table>
                </div>

                <div class="more-info">
                    <button class="btn-text-blue">
                   <span>
                       ավելին
                   </span>
                        <i class="icon icon-arrow-down"></i>
                    </button>
                </div>

                <div class="blurring">
                </div>
            </div>
        </div>

        <div class="columns large-12 medium-12 small-12">
            <div class="wrapper margins prise">
                <div class="listing-title">
                    <div class="left">
                        <div class="document-title">
                            <span>Տույժ/տուգանք</span>
                        </div>
                    </div>
                </div>
                <div class="prise-cont ">
                    <div class="product-prise-wrapper">
                        <div class="prise-title">
                            <div class="left product-prise">
                                <span>Վարկի մայր գումարը չվճարելու դեպքում</span>
                            </div>
                            <div class="right product-prise">
                                <span>{{$product->loan_main_amount_non_payment_case}}</span>
                            </div>
                        </div>
                        <div class="prise-title">
                            <div class="left product-prise">
                                <span>Տոկոսագումարները չվճարելու դեպքում</span>
                            </div>
                            <div class="right product-prise">
                                <span>{{$product->percentage_sum_non_payment_case}}</span>
                            </div>
                        </div>
                        <div class="prise-title">
                            <div class="left product-prise">
                                <span>Այլ վճարները չկատարելու դեպքում</span>
                            </div>
                            <div class="right product-prise">
                           <span>
                               {{$product->another_non_payments_case}}
                           </span>
                            </div>
                        </div>

                        <div class="prise-title">
                            <div class="left product-prise">
                                <span>Այլ</span>
                            </div>
                            <div class="right product-prise">
                           <span>
                              {{$product->other_non_payment}}
                           </span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="columns large-12 medium-12 small-12">
            <div class="wrapper margins">
                <div class="document-title">
                    Փաստաթղթերի ցանկ
                </div>
                <div class="document-wrapper">

                    @if($product->mainDocuments->count() > 0 )
                        @foreach($product->mainDocuments as $productMainDocument)
                            <div class="document">
                                <i class="icon  icon-doc"></i>
                                <span>{{$documents_list->find($productMainDocument->document_id)->name}}</span>
                            </div>
                        @endforeach
                    @endif

                    @if($product->customDocuments->count() > 0 )
                        @foreach($product->customDocuments as $productCustomDocument)
                            <div class="document">
                                <i class="icon  icon-doc"></i>
                                <span>{{ $productCustomDocument->document_name}}</span>
                            </div>
                        @endforeach
                    @endif
                    <div class="blurring"></div>
                </div>
                <div class="more-info">
                    <button class="btn-text-blue">
                   <span>
                           ավելին
                   </span>
                        <i class="icon icon-arrow-down"></i>
                    </button>
                </div>
            </div>
        </div>

    </div>
</main>

@include('layouts.footer')