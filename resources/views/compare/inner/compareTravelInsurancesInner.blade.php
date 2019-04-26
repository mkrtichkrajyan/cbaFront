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
                        <span>Ճանապարհորդության ապահովագրություն</span>
                    </div>
                    <div class="compare_name_podtitle">
                        Երկիր
                    </div>
                    <div class="compare_name_podtitle">
                        ժամկետ
                    </div>
                    <div class="compare_name_podtitle compare_name_podtitle_mid">
                        գումար / արժույթ / սակագին
                    </div>
                    <div class="compare_name_podtitle">
                        Ապահովագրավճար
                    </div>
                    <div class="compare_name_podtitle">
                        Տարիք
                    </div>
                    <div class="compare_name_podtitle">
                        ՉՀԱՏՈՒՑՎՈՂ ԳՈՒՄԱՐ
                    </div>
                    <div class="compare_name_podtitle compare_name_podtitle_mid">
                        Ապահովագրական պատահար
                    </div>
                    <div class="compare_name_podtitle compare_name_podtitle_mid">
                        Հատուցվող ծախսեր
                    </div>


                    <div class="compare_name_podtitle compare_name_podtitle_mid">
                        Չհատուցվող ծախսեր
                    </div>
                    <div class="compare_name_podtitle compare_name_podtitle_mid">
                        Հատուցման բացառություններ
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
                                    <div class="compare_name_title">
                                        <div class="close">
                                            <i class="icon icon-x"></i>
                                        </div>
                                        <div class="compare-item">
                                            <img src="{{ backend_asset('savedImages/'.$product->companyInfo->image )}}">
                                            <span>{{$product->name}}</span>
                                        </div>
                                    </div>
                                    <div class="servis_name">
                                        {{$countries->find($compareVariationsDataCurr["country"])->name}}
                                    </div>
                                    <div class="servis_name">
                                        {{$compareVariationsDataCurr["term"]}} Օր
                                    </div>
                                    <div class="servis_name servis_name_mid servis_name_scroll">
                                        <span>Գումար` {{ $variation->travel_insurance_amount}}</span>,
                                        <span>Արժույթ՝ {{$loanCurrenciesTypes->find($variation->currency)->name}}</span>,

                                        <span>Սակագնային գումար՝ {{$variation->travel_insurance_tariff_amount}}</span>,
                                        <span>Տոկոս՝ {{$variation->travel_insurance_percent}} %</span>
                                    </div>

                                    <div class="servis_name">
                                        <span>{{$compareVariationsDataCurr["insurance_fee"]}}</span>
                                    </div>

                                    <div class="servis_name">
                                        <span>{{$compareVariationsDataCurr["age"]}}</span>
                                    </div>

                                    <div class="servis_name">
                                        @if($product->nonRecoverableAmountInfo->count() > 0)
                                            <span>{{$product->nonRecoverableAmountInfo->name}}</span>
                                            @if($product->non_recoverable_amount!=2 && !is_null($product->non_recoverable_expense_limits))
                                                <span>{{$product->non_recoverable_expense_limits}}</span>
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </div>

                                    <div class="servis_name servis_name_mid servis_name_scroll">
                                        <div class="insurance_accidents_info_sexion">
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
                                    </div>


                                    <div class="servis_name servis_name_refundable_expenses servis_name_mid servis_name_scroll">

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

                                    <div class="servis_name servis_name_mid servis_name_scroll">
                                        <span> {{$product->non_refundable_expenses}}</span>
                                    </div>

                                    <div class="servis_name  servis_name_mid servis_name_scroll">
                                        <span> {{$product->compensation_exceptions}}</span>
                                    </div>

                                    <div class="servis_name servis_name_mid">
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