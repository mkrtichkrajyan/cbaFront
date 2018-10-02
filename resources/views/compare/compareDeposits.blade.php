@extends('layouts.default')

@include('layouts.head')

@include('layouts.header')

<main>
    <div class="back-fon" style="background-image: url({{asset('img/blue-fon.png')}});height: 150px;">
    </div>

    <input type="hidden" value="{{backend_asset_path()}}" id="backend_asset_path" name="backend_asset_path">

    <input type="hidden" value="{{$request_results_count}}" id="request_results_count" name="request_results_count">

    <form id="seachProductForm" enctype="multipart/form-data" class="form-horizontal" name="productForm"
          action="{{ url($currProductByBelongingsView->compare_url) }}" method="get">

        <div class="row">
            <div class="columns large-12 medium-12 small-12">
                <div class="listing-title">
                    <div class="left">
                        <a href="{{$previousUrl}}" class="come-back">
                            <div class="come-back-icon">
                                <i class="icon icon-back-arrow"></i>
                            </div>
                            <span>{{$currBelonging->name}}</span>
                        </a>
                    </div>
                    <div class="right">
                        <div class="listing-icon">
                            <i class="icon {{$currProductByBelongingsView->front_icon}}"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="columns large-12 medium-12 small-12">
                <div class="wrapper slid-wrapper">
                    <div class="row">

                        <div class="columns large-3 medium-6 small-12">
                            <label class="label" for="deposit_type">Տեսակ</label>
                            <div class="custom-select wrapper deposit_type_selectbox">
                                <select id="deposit_type" name="deposit_type" class="">
                                    <option value="">Տեսակ</option>
                                    @if(is_null($deposit_type))
                                        @php($deposit_type = 1)
                                    @endif
                                    @foreach($deposit_types_list as $deposit_types_list_curr)
                                        <option @if($deposit_types_list_curr->id == $deposit_type) selected @endif
                                        value="{{$deposit_types_list_curr->id}}">{{$deposit_types_list_curr->name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            @if ($errors->has('deposit_type'))
                                <span class="help-block err-field">
                                    <strong>{{ $errors->first('deposit_type') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="columns large-3 medium-6 small-12">
                            <label class="label" for="currency">Արժույթ</label>
                            <div class="custom-select wrapper currency_type_selectbox">
                                <select id="currency" name="currency"
                                        class="filter_product filter_currency">
                                    <option value="">Արժույթ</option>
                                    @if(is_null($currency))
                                        @php($currency = 1)
                                    @endif
                                    @foreach($loanCurrenciesTypes as $loanCurrenciesType)
                                        <option @if($loanCurrenciesType->id == $currency) selected @endif
                                        value="{{$loanCurrenciesType->id}}">{{$loanCurrenciesType->name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            @if ($errors->has('currency'))
                                <span class="help-block err-field">
                                    <strong>{{ $errors->first('currency') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="columns large-3 medium-6 small-12 condition_from_deposit_type_term_part">
                            <label class="label" for="amount">Ժամկետ</label>
                            <div class="rel">

                                <input type="number" min="0" id="loan_term" name="loan_term"
                                       value="{{$loan_term}}" class="input no_negative_value">

                                <input type="hidden" name="loan_term_search" id="loan_term_search"
                                       value="{{$loan_term}}">

                                <input type="hidden" name="time_type" id="time_type" value="{{$time_type}}">

                                <input type="hidden" name="time_type_search" id="time_type_search"
                                       value="{{$time_type}}">

                                <div class="chenge-time">
                                    @if(is_null($time_type))
                                        @php($time_type = $time_types->first()->id)
                                    @endif
                                    @foreach($time_types as $time_type_curr)

                                        <span data-type="{{$time_type_curr->id}}"
                                              class="time_type_select chenge-time-active @if($time_type == $time_type_curr->id) active @endif">
                                            {{mb_strtolower ($time_type_curr->name)}}
                                        </span>
                                    @endforeach
                                </div>
                            </div>

                            @if ($errors->has('loan_term'))
                                <span class="help-block err-field">
                                    <strong>{{ $errors->first('loan_term') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="columns large-3 medium-6 small-12">
                            <label class="label" for="amount">Գումար</label>
                            <div class="rel">
                                <input type="number" id="loan_amount" name="loan_amount" value="{{$loan_amount}}"
                                       class="input">

                                <input type="hidden" name="loan_amount_search" id="loan_amount_search"
                                       value="{{$loan_amount}}">


                                <input type="hidden" id="deposit_money_min" name="deposit_money_min"
                                       value="{{$deposit_money_min}}">
                                <input type="hidden" id="deposit_money_max" name="deposit_money_max"
                                       value="{{$deposit_money_max}}">
                                <div id="slider-range-deposit"></div>

                                <i class="icon icon-right icon-dram"></i>
                            </div>

                            @if ($errors->has('loan_amount'))
                                <span class="help-block err-field">
                                    <strong>{{ $errors->first('loan_amount') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="right">
                            <button id="seachProductFormSubmitCheck" type="button" class="btn btn-red">
                                <i class="icon icon-left  icon-search"></i>
                                <span>Որոնել</span>
                            </button>
                        </div>

                        <div class="columns large-12 medium-12 small-12">
                            <div class="read-more">
                                <i class="icon icon-left icon-filters"></i> Այլ պայմաններ
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="columns large-12 medium-12 small-12" id="insert-after">
                <div class="wrapper warning">
                    <i class="icon icon-Info"></i>
                    <span>
                        ՀՀ տարածքում բանկային հաշվով փոխանցումը կատարվում է առավելագույնը 3 աշխատանքային
                        օրվա ընթացքում: Արտասահման փոխանցում իրականացնելու ժամկետը կախված է այն բանկերի թվից,
                            որոնց միջոցով կատարվում է փոխանցումը:
                    </span>
                    <div class="close-warning">
                        <i class="icon icon-x"></i>
                    </div>
                </div>
            </div>

            @if(!is_null($productsGroupByCompany))

                <div class="margin-top none columns large-3 medium-3 small-12">

                    <div class="wrapper check-box-panel-wrapper  popup">
                        <form>

                            <div class="check-drop-down-wrapper">
                                <div class="check-drop-title">
                                    <span>Կապիտալացում</span>
                                    <i></i>
                                </div>
                                <div class="check-drop-down">

                                    @foreach ($deposit_capitalizations_list as $deposit_capitalization)
                                        <label class="container">{{$deposit_capitalization["info"]->name}}
                                            <input type="checkbox"
                                                   id="deposit_capitalization_{{$deposit_capitalization['id']}}"
                                                   data-id="{{$deposit_capitalization['id']}}"
                                                   name="deposit_capitalization_{{$deposit_capitalization['id']}}"
                                                   value="1"
                                                   class="filter_product filter_checkbox filter_capitalization">
                                            <span class="checkmark"></span>
                                            <span class="single_filter_count capitalization_filter_count">{{$deposit_capitalization["count"]}}</span>
                                        </label>
                                    @endforeach

                                </div>
                            </div>

                            <div class="check-drop-down-wrapper">
                                <div class="check-drop-title">
                                    <span>Տոկոսագումարների վճարում</span>
                                    <i></i>
                                </div>
                                <div class="check-drop-down">

                                    @foreach ($deposit_interest_rates_payments as $deposit_interest_rates_payment)
                                        <label class="container">{{$deposit_interest_rates_payment["info"]->name}}
                                            <input type="checkbox"
                                                   id="deposit_interest_rates_payment_{{$deposit_interest_rates_payment['id']}}"
                                                   data-id="{{$deposit_interest_rates_payment['id']}}"
                                                   name="deposit_interest_rates_payment_{{$deposit_interest_rates_payment['id']}}"
                                                   value="1"
                                                   class="filter_product filter_checkbox filter_deposit_interest_rates_payment">
                                            <span class="checkmark"></span>
                                            <span class="single_filter_count interest_rates_payment_filter_count">{{$deposit_interest_rates_payment["count"]}}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div class="check-drop-down-wrapper">
                                <div class="check-drop-title">
                                    <span>Փոփոխության հնարավորություն </span>
                                    <i></i>
                                </div>

                                <div class="check-drop-down">
                                    <label>Գումարի ավելացում</label>
                                    @foreach($yes_no_answers as $yes_no_answer_curr)
                                        <label class="container">{{$yes_no_answer_curr->name}}
                                            <input id="money_increasing_{{$yes_no_answer_curr->id}}"
                                                   name="money_increasing_{{$yes_no_answer_curr->id}}"
                                                   type="checkbox" value="1" data-id="{{$yes_no_answer_curr->id}}"
                                                   class="filter_product filter_checkbox filter_money_increasing">
                                            <span class="checkmark"></span>
                                            <span class="single_filter_count money_increasing_filter_count">{{$money_increasing[$yes_no_answer_curr->id]}}</span>
                                        </label>
                                    @endforeach
                                </div>
                                <div class="check-drop-down">
                                    <label>Գումարի նվազեցում</label>
                                    @foreach($yes_no_answers as $yes_no_answer_curr)
                                        <label class="container">{{$yes_no_answer_curr->name}}
                                            <input id="money_decreasing_{{$yes_no_answer_curr->id}}"
                                                   name="money_decreasing_{{$yes_no_answer_curr->id}}"
                                                   type="checkbox" value="1" data-id="{{$yes_no_answer_curr->id}}"
                                                   class="filter_product filter_checkbox filter_money_decreasing">
                                            <span class="checkmark"></span>
                                            <span class="single_filter_count money_decreasing_filter_count">{{$money_decreasing[$yes_no_answer_curr->id]}}</span>
                                        </label>
                                    @endforeach
                                </div>
                                <div class="check-drop-down">
                                    <label>Արժույթի փոփոխում</label>
                                    @foreach($yes_no_answers as $yes_no_answer_curr)
                                        <label class="container">{{$yes_no_answer_curr->name}}
                                            <input id="currency_changing_{{$yes_no_answer_curr->id}}"
                                                   name="currency_changing_{{$yes_no_answer_curr->id}}"
                                                   type="checkbox" value="1" data-id="{{$yes_no_answer_curr->id}}"
                                                   class="filter_product filter_checkbox filter_currency_changing">
                                            <span class="checkmark"></span>
                                            <span class="single_filter_count currency_changing_filter_count">{{$currency_changing[$yes_no_answer_curr->id]}}</span>
                                        </label>
                                    @endforeach
                                </div>
                                <div class="check-drop-down">
                                    <label>Ավանդի ընդհատում</label>
                                    @foreach($yes_no_answers as $yes_no_answer_curr)
                                        <label class="container">{{$yes_no_answer_curr->name}}
                                            <input id="deposit_interruption_{{$yes_no_answer_curr->id}}"
                                                   name="deposit_interruption_{{$yes_no_answer_curr->id}}"
                                                   type="checkbox" value="1" data-id="{{$yes_no_answer_curr->id}}"
                                                   class="filter_product filter_checkbox filter_deposit_interruption">
                                            <span class="checkmark"></span>
                                            <span class="single_filter_count deposit_interruption_filter_count">{{$deposit_interruption[$yes_no_answer_curr->id]}}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div class="check-drop-down-wrapper">
                                <div class="check-drop-title">
                                    <span>Նվազագույն գումար</span>
                                    <i></i>
                                </div>
                                <div class="check-drop-down">
                                    @foreach($yes_no_answers as $yes_no_answer_curr)
                                        <label class="container">{{$yes_no_answer_curr->name}}
                                            <input id="minimum_money_{{$yes_no_answer_curr->id}}"
                                                   name="minimum_money_{{$yes_no_answer_curr->id}}"
                                                   type="checkbox" value="1" data-id="{{$yes_no_answer_curr->id}}"
                                                   class="filter_product filter_checkbox filter_minimum_money">
                                            <span class="checkmark"></span>
                                            <span class="single_filter_count deposit_interruption_filter_count">{{$minimum_money[$yes_no_answer_curr->id]}}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div class="check-drop-down-wrapper">
                                <div class="check-drop-title">
                                    <span>Հատուկ ավանդներ</span>
                                    <i></i>
                                </div>
                                <div class="check-drop-down">

                                    @foreach ($deposits_specials_list as $deposits_special)
                                        <label class="container">{{$deposits_special["info"]->name}}
                                            <input type="checkbox" id="deposits_special_{{$deposits_special['id']}}"
                                                   data-id="{{$deposits_special['id']}}"
                                                   name="deposits_special_{{$deposits_special['id']}}"
                                                   value="1"
                                                   class="filter_product filter_checkbox filter_deposits_special">
                                            <span class="checkmark"></span>
                                            <span class="single_filter_count deposits_special_filter_count">{{$deposits_special["count"]}}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="margin-top columns large-9 medium-auto product_results">

                    <div class="listing-title">
                        <div class="left">
                            Գտնվել է <span>{{$products->count()}}</span> առաջարկ
                        </div>
                        <div class="right">
                            <div class="listing-icon">
                                <div class="add-function">
                                    <a href="" download>
                                        <i class="icon icon-right  icon-download"></i>
                                    </a>
                                    <a href="">
                                        <i class="icon icon-right  icon-more"></i>
                                    </a>
                                    <a href="">
                                        <i class="icon icon-right icon-print"></i>
                                    </a>
                                </div>
                                <div class="btn-icon-blue">
                                    <i class="chenge icon icon-right  icon-list"></i>
                                    <i class="chenge icon icon-right  icon-list-tow"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="change_item">
                        @foreach($productsGroupByCompany as $companyProducts)


                            <div class="wrapper pading">
                                <div class="listing-title">
                                    <div class="left">
                                        <div class="category-title">
                                            {{$companyProducts->first()->name}}
                                        </div>
                                    </div>
                                    <div class="right">
                                        <div class="category-logo">
                                            <img style="max-width: 80px;"
                                                 src="{{ backend_asset('savedImages/'.$companyProducts->first()->companyInfo->image )}}">
                                        </div>
                                    </div>
                                </div>
                                <div class="table">
                                    <div class="table-pise-wrapper">
                                        <div class="table-pise">
                                            <div class="table-pise-title">
                                                Անվանական տոկոսադրույք
                                            </div>
                                            <div class="table-pise-text">
                                                98%
                                            </div>
                                        </div>
                                        <div class="table-pise">
                                            <div class="table-pise-title">
                                                Պարտադիր ճարներ <i class="icon icon-right  icon-question"></i>
                                            </div>
                                            <div class="table-pise-text">
                                                2 000 000
                                            </div>
                                        </div>
                                    </div>
                                    <div class="table-pise-wrapper">
                                        <div class="table-pise">
                                            <div class="table-pise-title">
                                                Հետ վճարվող գումար
                                            </div>
                                            <div class="table-pise-text">
                                                2 000 000 <i class="icons "></i>
                                            </div>
                                        </div>
                                        <div class="table-pise">
                                            <div class="table-pise-title">
                                                Անվանական տոկոսադրույք
                                            </div>
                                            <div class="table-pise-text">
                                                98%
                                            </div>
                                        </div>
                                    </div>
                                    <div class="table-pise-wrapper">
                                        <div class="table-pise">
                                            <div class="table-pise-title">
                                                Անվանական
                                            </div>
                                            <div class="table-pise-text">
                                                98%
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="listing-title">
                                    <div class="left">
                                        <button type="button" class="btn btn-white btn_compare">
                                            <i class="icon icon-left  icon-add"></i>
                                            <span>
                                            համեմատել
                                    </span>
                                        </button>
                                        <a href="?p=prod-page" class="btn btn-more">
                                    <span>
                                            ավելին
                                    </span>
                                            <i class="icon icon-right  icon-arrow-right"></i>
                                        </a>
                                    </div>
                                    <div class="right">
                                        <button type="button" class="btn btn-pink other_suggestions_open_close">
                                            <section>{{$companyProducts->count()-1}}</section>
                                            <span>
                                            այլ առաջարկ
                                    </span>
                                            <i class="icon icon-arrow-down"></i>
                                        </button>
                                    </div>
                                </div>

                                <section class="hide-show">

                                    @if($companyProducts->count() > 1)
                                        @php(
                                           $companyProductsFiltered = $companyProducts->filter(function ($value, $key) use($companyProducts) {
                                               return $key > $companyProducts->keys()->first();
                                           })
                                       )

                                        @foreach($companyProductsFiltered as $companyProductCurr)
                                            <div class="add-result pading">
                                                <div class="listing-title">
                                                    <div class="left">
                                                        <div class="category-title">
                                                            {{$companyProductCurr->name}}
                                                        </div>
                                                    </div>
                                                    <div class="right">
                                                        <div class="category-logo">
                                                            <img style="max-width: 80px;"
                                                                 src="{{ backend_asset('savedImages/'.$companyProductCurr->companyInfo->image )}}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="table">
                                                    <div class="table-pise-wrapper">
                                                        <div class="table-pise">
                                                            <div class="table-pise-title">
                                                                Անվանական տոկոսադրույք
                                                            </div>
                                                            <div class="table-pise-text">
                                                                98%
                                                            </div>
                                                        </div>
                                                        <div class="table-pise">
                                                            <div class="table-pise-title">
                                                                Պարտադիր ճարներ <i
                                                                        class="icon icon-right  icon-question"></i>
                                                            </div>
                                                            <div class="table-pise-text">
                                                                2 000 000
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="table-pise-wrapper">
                                                        <div class="table-pise">
                                                            <div class="table-pise-title">
                                                                Հետ վճարվող գումար
                                                            </div>
                                                            <div class="table-pise-text">
                                                                2 000 000 <i class="icons "></i>
                                                            </div>
                                                        </div>
                                                        <div class="table-pise">
                                                            <div class="table-pise-title">
                                                                Անվանական տոկոսադրույք
                                                            </div>
                                                            <div class="table-pise-text">
                                                                98%
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="table-pise-wrapper">
                                                        <div class="table-pise">
                                                            <div class="table-pise-title">
                                                                Անվանական
                                                            </div>
                                                            <div class="table-pise-text">
                                                                98%
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="listing-title">
                                                    <div class="left">
                                                        <button type="button" class="btn btn-white btn_compare">
                                                            <i class="icon icon-left  icon-add"></i>
                                                            <span>
                                                    համեմատել
                                            </span>
                                                        </button>
                                                        <a href="?p=prod-page" class="btn btn-more">
                                            <span>
                                                    ավելին
                                            </span>
                                                            <i class="icon icon-right  icon-arrow-right"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </section>
                            </div>
                        @endforeach
                    </div>

                    <div class="change_item">

                        <div class="table-wrap">
                            <div class="td">
                                <span>Կազմակերպության անվանում</span>
                            </div>

                            <div class="td">
                                <span>Անվանական տոկոսադրույք</span><i></i>
                            </div>

                            <div class="td">
                                <span>Պարտադիր վճարներ </span>
                            </div>

                            <div class="td">
                                <div class="flex-wrapper">
                                    <span>Հետ վճարվող գումար </span>
                                    <span>
                                        <div class="come-back-icon">
                                            <i class="icon icon-arrow-right"></i>
                                        </div>
                                    </span>
                                </div>
                            </div>

                            <div class="td">
                                <span> Փաստացի տոկոսադրույք </span>
                            </div>
                            <div class="td"></div>
                        </div>
                        @foreach($productsGroupByCompany as $productsGroupByCompanyCurr)

                            <div class="wrapper min-pading">
                                <div class="table-wrapper">
                                    <div class="th"><img
                                                src="{{backend_asset('savedImages/'.$productsGroupByCompanyCurr->first()->companyInfo->image )}}">
                                    </div>

                                    <div class="th"><span>98</span></div>

                                    <div class="th"><span>2 000 000</span></div>

                                    <div class="th"><span>2 000 000</span></div>

                                    <div class="th"><span>98</span></div>

                                    <div class="th flex-wrapper">
                                        <button class="btn btn-pink">
                                            <section>{{$productsGroupByCompanyCurr->count()-1}}</section>
                                            <i class="icon icon-arrow-down"></i>
                                        </button>

                                        <button class="btn btn-white btn_compare"><i class="icon  icon-add"></i>
                                        </button>

                                        <a href="" class="btn btn-more"><i
                                                    class="icon icon-right  icon-arrow-right"></i></a>
                                    </div>
                                </div>
                                <div class="hide-show">

                                    @if($productsGroupByCompanyCurr->count() > 1)
                                        @php(
                                           $productsGroupByCompanyCurrFiltered = $productsGroupByCompanyCurr->filter(function ($value, $key) use($productsGroupByCompanyCurr) {
                                               return $key > $productsGroupByCompanyCurr->keys()->first();
                                           })
                                       )

                                        @foreach($productsGroupByCompanyCurrFiltered as $companyOtherProduct)
                                            <div class="table-wrapper">
                                                <div class="th">
                                                    <span><img src="{{backend_asset('savedImages/'.$companyOtherProduct->companyInfo->image )}}"></span>
                                                </div>

                                                <div class="th"><span> 98 </span></div>

                                                <div class="th"><span>2 000 000</span></div>

                                                <div class="th"><span>2 000 000</span></div>

                                                <div class="th"><span>98</span></div>

                                                <div class="th flex-wrapper ">

                                                    <button class="btn btn-white btn_compare"><i
                                                                class="icon  icon-add"></i></button>

                                                    <a href="" class="btn btn-more"><i
                                                                class="icon  icon-arrow-right"></i></a>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        @endforeach

                    </div>

                </div>
            @endif

        </div>
    </form>

</main>


<script type="text/javascript">
    $(document).ready(function () {

        $.ajaxSetup({headers: {'csrftoken': '{{ csrf_token() }}'}});

        $(".filter_selectbox").parent().find('.select-selected').bind("DOMSubtreeModified", function () {

            filter_products("{{url('/car-loans-filters/')}}");
        });

        $(".filter_product").click(function () {

            filter_products("{{url('/car-loans-filters/')}}");
        });
    });
</script>

@include('layouts.footer')