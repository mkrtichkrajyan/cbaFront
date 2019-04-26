@extends('layouts.default')

@include('layouts.head')

@include('layouts.headerCompare')

<input type="hidden" value="{{backend_asset_path()}}" id="backend_asset_path" name="backend_asset_path">

@php($checked_variations =  $getCompareInfo[$belonging_id]["checked_variations"] )

<main>
    <div class="row" style="position: relative;">
        <div class="back-fon position">

        </div>
        <div class="columns large-12 medium-12 small-12">
            <div class="listing-title product-listing">
                <div class="left">
                    <a href="" class="come-back product-come-back  title-map">
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
                        @php($unique_options    =   "product_".$product->id."_age_".$age."_loan_term_".$term."_currency_".$currency."_term_inputs_quantity_".$product_variation->term_inputs_quantity)

                        <button type="button"
                                data-options="{{$unique_options}}"
                                data-belongingId="{{$belonging_id}}"
                                data-product-id='{{$product->id}}'
                                data-country="{{$country}}"
                                data-age="{{$age}}"
                                data-term="{{$term}}"
                                data-term_inputs_quantity="{{$product_variation->term_inputs_quantity}}"
                                data-currency="{{$currency}}"
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
                        <div class="all-price-title">Ապահովագրական գումար</div>
                        <div class="all-total-price">
                            <span>{{$product_variation->travel_insurance_amount}} {{$loanCurrenciesTypes->find($product_variation->currency)->name}}</span>
                            <i class="icon icon-"></i>
                        </div>
                    </div>
                    {{--<div class="pircent-price">--}}
                        {{--<div class="all-price-title">Ավել վճարվող գումար</div>--}}
                        {{--<div class="all-total-price">--}}
                            {{--<span>{{$more_payment_amount}}</span>--}}
                            {{--<i class=" icon icon-dram"></i>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                </div>

                {{--<div class="end-price">--}}
                    {{--<div>--}}
                        {{--<div class="all-price-title">Հետ վճարվող գումար</div>--}}
                        {{--<div class="all-total-price">--}}
                            {{--<span>{{$sum_payments}}</span>--}}
                            {{--<i class=" icon icon-dram"></i>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
            </div>
            <div class="final-settlement">
                <div class="actual-percent">
                    <div class="all-price-title">Ապահովագրավճար</div>
                    <div class="all-total-price">
                        <span class="chart-count-1">{{$insurance_fee}}</span>
                    </div>
                </div>
                {{--<div class="nominal-percent">--}}
                {{--<div class="all-price-title">Անվանական տոկոսադրույք</div>--}}
                {{--<div class="all-total-price">--}}
                {{--<span class="chart-count-2">{{$percentage}}%</span>--}}
                {{--</div>--}}
                {{--</div>--}}
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
                            Ապահովագրական պատահար
                        </div>
                        <div class="map-chenge chenge">
                            Հատուցվող ծախսեր
                        </div>
                        <div class="map-chenge chenge">
                            Չհատուցող ծախսեր
                        </div>
                    </div>
                </div>
                <div class="change_item item-scroll">
                    <div class="prise-cont other-info-wrapper">
                        <div class="product-prise-wrapper other-info">

                            <div class="prise-title">
                                <div class="left  other-info-title">
                                    <span>Ապահովագրական գումար</span>
                                </div>
                                <div class="right other-info-text">

                                    <span>{{$product_variation->travel_insurance_amount}} {{$loanCurrenciesTypes->find($product_variation->currency)->name}}</span>
                                </div>
                            </div>
                            <div class="prise-title">
                                <div class="left  other-info-title">
                                    <span>Ֆրանշիզա</span>
                                </div>
                                <div class="right other-info-text repayment-info-text">
                                    @if($product->nonRecoverableAmountInfo->count() > 0)
                                        <span>{{$product->nonRecoverableAmountInfo->name}}</span>
                                        @if($product->non_recoverable_amount!=2 && !is_null($product->non_recoverable_expense_limits))
                                            <span>{{$product->non_recoverable_expense_limits}}</span>
                                        @endif
                                    @else
                                        -
                                    @endif
                                </div>
                            </div>
                            <div class="prise-title">
                                <div class="left  other-info-title">
                                    <span>Ապահովագրության  ժամկետը</span>
                                </div>
                                <div class="right other-info-text">
                                    <span>{{$term}} Օր</span>
                                </div>
                            </div>
                        </div>
                        <div class="product-prise-wrapper other-info">

                            <div class="prise-title">
                                <div class="left  other-info-title">
                                    <span>Երկիր</span>
                                </div>
                                <div class="right other-info-text">
                                    <span>{{$countries->find($country)->name}}</span>
                                </div>
                            </div>

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
                        </div>
                    </div>
                </div>

                <div class="change_item item-scroll">
                    @if($product->accidentsInfo->count() > 0)
                        <ul>
                            @foreach($product->accidentsInfo as $productAccident)
                                <li>{{$productAccident->currAccidentInfo->name}}</li>
                            @endforeach
                        </ul>
                    @else
                        -
                    @endif
                </div>

                <div class="change_item item-scroll">
                    @if($product->refundableExpensesInfo->count() > 0)
                        @php($productRefundableExpensesArr    =   [])
                        <ul>
                            @foreach($product->refundableExpensesInfo as $productRefundableExpense)
                                <li>{{$productRefundableExpense->currRefundableExpenseInfo->name}}</li>
                                @php($productRefundableExpensesArr[]  =  $productRefundableExpense->currRefundableExpenseInfo->name )
                            @endforeach
                        </ul>
                    @else
                        -
                    @endif
                </div>

                <div class="change_item item-scroll">
                    <span> {{$product->non_refundable_expenses}}</span>
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