@extends('layouts.default')

@include('layouts.head')

@include('layouts.headerCompare')

@php($checked_variations =  $getCompareInfo[$belonging_id]["checked_variations"] )


<main>
    <div class="back-fon" style="background-image: url({{asset('img/blue-fon.png')}});height: 150px;">
    </div>

    <input type="hidden" value="{{backend_asset_path()}}" id="backend_asset_path" name="backend_asset_path"/>

    <input type="hidden" value="{{url('/car-loan-product/')}}" id="prod_page_path" name="prod_page_path"/>

    <input type="hidden" value="{{url('/company-branches-and-bankomats/')}}" id="company_path" name="company_path"/>

    <input type="hidden" value="{{$request_results_count}}" id="request_results_count" name="request_results_count"/>


    <input type="hidden" value="{{$car_cost}}" id="prod_cost" name="prod_cost"/>

    <input type="hidden" value="{{$prepayment}}" id="prod_prepayment" name="prod_prepayment"/>

    <input type="hidden" value="{{$loan_term_search_in_days}}" id="prod_loan_term_search_in_days"
           name="prod_loan_term_search_in_days"/>

    <form id="seachProductForm" enctype="multipart/form-data" class="form-horizontal" name="carLoanForm"
          action="{{ url($currProductByBelongingsView->compare_url) }}" method="get">

        <div class="row">
            <div class="columns large-12 medium-12 small-12">
                <div class="listing-title">
                    <div class="left">
                        <a href="{{$previousUrl}}" class="come-back">
                            <div class="come-back-icon">
                                <i class="icon icon-back-arrow"></i>
                            </div>
                            <span>Ավտովարկ</span>
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
                            <label class="label" for="car_cost">Ավտոմեքենայի արժեք</label>
                            <div class="rel">
                                <input type="number" min="0" name="car_cost"
                                       class="input no_negative_value" id="cost" value="{{$car_cost}}">

                                <input type="hidden" name="car_cost_search" id="car_cost_search" value="{{$car_cost}}">

                                <i class="icon icon-right icon-dram"></i>
                            </div>
                            @if ($errors->has('car_cost'))
                                <span class="help-block err-field">
                                    <strong>{{ $errors->first('car_cost') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="columns large-3 medium-6 small-12">
                            <label class="label" for="prepayment">Կանխավճար</label>
                            <div class="rel">
                                <input type="number" id="maximum" name="prepayment"
                                       class="input no_negative_value prepayment" value="{{$prepayment}}">

                                <input type="hidden" id="prepayment_search" name="prepayment_search"
                                       value="{{$prepayment}}">

                                <div id="slider-range-max"></div>
                                <i class="icon icon-right icon-dram"></i>
                            </div>
                            @if ($errors->has('prepayment'))
                                <span class="help-block err-field">
                                    <strong>{{ $errors->first('prepayment') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="columns large-3 medium-6 small-12">
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
                            <label class="label" for="amount">Վարկի գումար</label>
                            <div class="rel">
                                <input type="text" id="loan_amount" name="loan_amount" value="{{$loan_amount}}"
                                       class="input not-allowed" disabled>

                                <input type="hidden" name="loan_amount_search" id="loan_amount_search"
                                       value="{{$loan_amount}}">

                                <i class="icon icon-right icon-dram"></i>
                            </div>
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

            @if(!is_null($productsWithVariations))

                <div class="margin-top none columns large-3 medium-3 small-12">

                    <div class="wrapper check-box-panel-wrapper  popup">
                        <form>
                            <div class="check-drop-down-wrapper">
                                <div class="check-drop-title">
                                    <span>Ավտոմեքենա</span><i></i>
                                </div>
                                <div class="check-drop-down">
                                    @foreach($car_types as $car_type)
                                        <label class="container">{{$car_type["info"]->name}}
                                            <input id="car_type_{{$car_type['id']}}" data-id="{{$car_type['id']}}"
                                                   name="car_type_{{$car_type['id']}}"
                                                   type="checkbox" value="1"
                                                   class="filter_product filter_checkbox filter_car_type">
                                            <span class="checkmark"></span>
                                            <span class="single_filter_count car_type_filter_count">{{$car_type["count"]}}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div class="check-drop-down-wrapper">
                                <div class="check-drop-title">
                                    <span>Տոկոսադրույք</span>
                                </div>
                                <div class="check-drop-down">
                                    @foreach($percentage_types as $percentage_type)
                                        <label class="container">{{$percentage_type["info"]->name}}
                                            <input type="checkbox" id="percentage_type_{{$percentage_type['id']}}"
                                                   data-id="{{$percentage_type['id']}}"
                                                   name="percentage_type_{{$percentage_type['id']}}"
                                                   value="1"
                                                   class="filter_product filter_checkbox filter_percentage_type">
                                            <span class="checkmark"></span>
                                            <span class="single_filter_count percentage_filter_count">{{$percentage_type["count"]}}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div class="check-drop-down-wrapper">
                                <div class="check-drop-title">
                                    <span>Մարման եղանակ </span>
                                </div>
                                <div class="check-drop-down">

                                    @foreach($repayment_types as $repayment_type)
                                        <label class="container">{{$repayment_type["info"]->name}}
                                            <input type="checkbox" id="repayment_type_{{$repayment_type['id']}}"
                                                   data-id="{{$repayment_type['id']}}"
                                                   name="repayment_type_{{$repayment_type['id']}}"
                                                   value="1"
                                                   class="filter_product filter_checkbox filter_repayment_type">
                                            <span class="checkmark"></span>
                                            <span class="single_filter_count repayment_type_filter_count">{{$repayment_type["count"]}}</span>
                                        </label>
                                    @endforeach
                                </div>

                                <div class="check-box check-drop-down">
                                    <div class="custom-select-second wrapper">
                                        <select id="repayment_loan_interval_type" name="repayment_loan_interval_type"
                                                class="filter_product filter_selectbox filter_repayment_loan_interval_type">
                                            <option value="">Վարկ</option>
                                            @foreach($repayment_loan_interval_types as $repayment_loan_interval_type)
                                                <option value="{{$repayment_loan_interval_type->id}}">{{$repayment_loan_interval_type->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="custom-select-second wrapper">
                                        <select id="repayment_percent_interval_type"
                                                name="repayment_percent_interval_type"
                                                class="filter_product filter_selectbox filter_repayment_percent_interval_type">
                                            <option value="">Տոկոս</option>
                                            @foreach($repayment_percent_interval_types as $repayment_percent_interval_type)
                                                <option value="{{$repayment_percent_interval_type->id}}">{{$repayment_percent_interval_type->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="check-drop-down-wrapper">
                                <div class="check-drop-title">
                                    <span>Տրամադրման եղանակ</span><i></i>
                                </div>
                                <div class="check-drop-down">
                                    @foreach($providing_types as $providing_type)

                                        <label class="container">{{$providing_type["info"]->name}}
                                            <input type="checkbox" id="providing_type_{{$providing_type['id']}}"
                                                   data-id="{{$providing_type['id']}}"
                                                   name="providing_type_{{$providing_type['id']}}"
                                                   value="1"
                                                   class="filter_product filter_checkbox filter_providing_type">
                                            <span class="checkmark"></span>
                                            <span class="single_filter_count providing_type_filter_count">{{$providing_type["count"]}}</span>
                                        </label>

                                    @endforeach
                                </div>
                            </div>

                            <div class="check-drop-down-wrapper">
                                <div class="check-drop-title">
                                    <span>Ապահովվածություն </span>
                                </div>
                                <div class="check-drop-down">
                                    @foreach($security_types as $security_type)

                                        <label class="container">{{$security_type["info"]->name}}
                                            <input type="checkbox" id="security_type_{{$security_type['id']}}"
                                                   data-id="{{$security_type['id']}}"
                                                   class="filter_product filter_checkbox filter_security_type"
                                                   name="security_type_{{$security_type['id']}}" value="1" class="">
                                            <span class="checkmark"></span>
                                            <span class="single_filter_count security_type_filter_count">{{$security_type["count"]}}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>


                            @if($special_project_statuses[1]["count"] > 0)
                                <div class="check-drop-down-wrapper">
                                    <div class="check-drop-title">
                                        <span>Հատուկ Ծրագիր  </span>
                                    </div>
                                    <div class="check-drop-down">
                                        @foreach($yes_no_answers as $yes_no_answer)

                                            <label class="container">{{$yes_no_answer->name}}
                                                <input type="checkbox"
                                                       id="special_project_answer_{{$yes_no_answer->id}}"
                                                       name="special_project_answer_{{$yes_no_answer->id}}"
                                                       value="1" data-id="{{$yes_no_answer->id}}"
                                                       class="filter_product filter_checkbox filter_special_project">
                                                <span class="checkmark"></span>
                                                <span class="single_filter_count repayment_type_filter_count">{{$special_project_statuses[$yes_no_answer->id]["count"]}}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{--@if($privileged_term_having_products_count > 0)--}}
                            @if($privileged_term_statuses[1]["count"] > 0)
                                <div class="check-drop-down-wrapper">
                                    <div class="check-drop-title">
                                        <span>Արտոնյալ ժամկետ</span>
                                    </div>
                                    <div class="check-drop-down">

                                        @foreach($yes_no_answers as $yes_no_answer)

                                            <label class="container">{{$yes_no_answer->name}}
                                                <input type="checkbox"
                                                       id="privileged_term_answer_{{$yes_no_answer->id}}"
                                                       name="privileged_term_answer_{{$yes_no_answer->id}}" value="1"
                                                       data-id="{{$yes_no_answer->id}}"
                                                       class="filter_product filter_checkbox filter_privileged_term">
                                                <span class="checkmark"></span>
                                                <span class="single_filter_count repayment_type_filter_count">{{$privileged_term_statuses[$yes_no_answer->id]["count"]}}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </form>
                    </div>
                </div>

                <div class="displayNone">
                    <div class="table-wrap head_grouped_by_company">
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
                </div>


                <div class="margin-top columns large-9 medium-auto product_results">

                    <div class="listing-title result_listing_title">
                        <div class="left">
                            Գտնվել է <span class="count_searched_products">{{$request_results_count}}</span> առաջարկ
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

                    <div class="change_item change_item_product_variations_results">
                        @foreach($productsWithVariations as $currProduct)

                            <div class="wrapper pading">
                                <div class="listing-title">
                                    <div class="left">
                                        <div class="category-title">
                                            {{$currProduct["name"]}}
                                        </div>
                                    </div>
                                    <div class="right">
                                        <a target="_blank"
                                           href="{{url('/company-branches-and-bankomats/'.$currProduct["company_id"])}}"
                                           class="category-logo">
                                            <img style="max-width: 80px;"
                                                 src="{{ backend_asset('savedImages/'.$currProduct["companyInfo"]->image )}}">
                                        </a>
                                    </div>
                                </div>
                                <div class="table">
                                    <div class="table-pise-wrapper">

                                        <div class="table-pise">
                                            <div class="table-pise-title">
                                                Կազմակերպություն
                                            </div>
                                            <div class="table-pise-text">
                                                {{$currProduct["companyInfo"]->name}}
                                            </div>
                                        </div>


                                        <div class="table-pise">
                                            <div class="table-pise-title">
                                                Անվանական տոկոսադրույք
                                            </div>
                                            <div class="table-pise-text">
                                                {{$currProduct["variations"][0]["percentage"]}}
                                            </div>
                                        </div>

                                        <div class="table-pise">
                                            <div class="table-pise-title">
                                                Ընդամենը պարտադիր վճարներ
                                                <i class="icon icon-right  icon-question"></i>
                                            </div>
                                            <div class="table-pise-text">
                                                {{$currProduct["variations"][0]["require_payments"]}}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="table-pise-wrapper">
                                        <div class="table-pise">
                                            <div class="table-pise-title">
                                                Ընդամենը հետ վճարվող գումար
                                            </div>
                                            <div class="table-pise-text">
                                                {{$currProduct["variations"][0]["sum_payments"]}} <i class="icons "></i>
                                            </div>
                                        </div>

                                        <div class="table-pise">
                                            <div class="table-pise-title">
                                                Փաստացի տոկոսադրույք
                                            </div>
                                            <div class="table-pise-text">
                                                {{round($currProduct["variations"][0]["factual_percentage"], 2) }}
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="listing-title">
                                    <div class="left">
                                        @php($firstProductVariation =    $currProduct["variations"][0])

                                        @php($unique_options    =   "bel_".$belonging_id."_prod_".$currProduct["id"]."_prov_" .$firstProductVariation["providing_type"]."_perc_".
                                        $firstProductVariation["percentage_type"]."_rep_" . $firstProductVariation["repayment_type"]."_rep_loan_" .
                                         intval($firstProductVariation["repayment_loan_interval_type_id"]) . "_rep_perc_" .intval($firstProductVariation["repayment_percent_interval_type_id"]))

                                        @php($unique_options_and_search_params  =   $unique_options."*"."_cost_".$car_cost."_prepayment_".$prepayment."_term_".$loan_term_search_in_days)

                                        @php($unique_options_and_search_params    =   str_rot13($unique_options_and_search_params))

                                        @php($unique_options    =   md5($unique_options))

                                        <button type="button" data-options="{{$unique_options}}"
                                                data-belongingId="{{$belonging_id}}"
                                                data-product-id='{{$currProduct["id"]}}'
                                                data-cost="{{$car_cost}}"
                                                data-prepayment="{{$prepayment}}"
                                                data-term="{{$loan_term_search_in_days}}"
                                                class="btn btn_compare btn-white @if(in_array($unique_options,$checked_variations)) compare_act_button_checked @endif">

                                            <i class="icon icon-left  icon-add"></i>
                                            <span>համեմատել</span>
                                        </button>
                                        <a href="{{url('/car-loan-product/'.$unique_options.'/'.$car_cost.'/'.$prepayment_final.'/'.$time_type.'/'.$loan_term)}}"
                                           class="btn btn-more">
                                            <span>ավելին</span>
                                            <i class="icon icon-right  icon-arrow-right"></i>
                                        </a>
                                    </div>
                                    <div class="right">
                                        <button type="button" class="btn btn-pink other_suggestions_open_close">
                                            <section>{{count($currProduct["variations"])-1}}</section>
                                            <span>այլ առաջարկ</span>
                                            <i class="icon icon-arrow-down"></i>
                                        </button>
                                    </div>
                                </div>

                                <section class="hide-show">

                                    @if(count($currProduct["variations"]) > 1)
                                        @php( $currProductVariations = $currProduct["variations"])

                                        @php( array_shift($currProductVariations))

                                        @foreach($currProductVariations as $currProductCurrVariation)
                                            <div class="add-result pading">
                                                <div class="listing-title">
                                                    <div class="left">
                                                        <div class="category-title">
                                                            {{$currProduct["name"]}}
                                                        </div>
                                                    </div>

                                                    <div class="right">
                                                        <a target="_blank" href="{{url('/company-branches-and-bankomats/'.$currProduct["company_id"])}}" class="category-logo">
                                                            <img style="max-width: 80px;"
                                                                 src="{{ backend_asset('savedImages/'.$currProduct["companyInfo"]->image )}}">
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="table">
                                                    <div class="table-pise-wrapper">

                                                        <div class="table-pise">
                                                            <div class="table-pise-title">
                                                                Կազմակերպություն
                                                            </div>
                                                            <div class="table-pise-text">
                                                                {{$currProduct["companyInfo"]->name}}
                                                            </div>
                                                        </div>

                                                        <div class="table-pise">
                                                            <div class="table-pise-title">
                                                                Անվանական տոկոսադրույք
                                                            </div>
                                                            <div class="table-pise-text">
                                                                {{$currProductCurrVariation["percentage"]}}
                                                            </div>
                                                        </div>

                                                        <div class="table-pise">
                                                            <div class="table-pise-title">
                                                                Ընդամենը պարտադիր վճարներ
                                                                <i class="icon icon-right  icon-question"></i>
                                                            </div>
                                                            <div class="table-pise-text">
                                                                {{$currProductCurrVariation["require_payments"] }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="table-pise-wrapper">
                                                        <div class="table-pise">
                                                            <div class="table-pise-title">
                                                                Ընդամենը հետ վճարվող գումար
                                                            </div>
                                                            <div class="table-pise-text">
                                                                {{$currProductCurrVariation["sum_payments"] }}
                                                                <i class="icons "></i>
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <div class="table-pise-wrapper">
                                                        <div class="table-pise">
                                                            <div class="table-pise-title">
                                                                Փաստացի տոկոսադրույք
                                                            </div>
                                                            <div class="table-pise-text">
                                                                {{round($currProductCurrVariation["factual_percentage"], 2) }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="listing-title">
                                                    <div class="left">
                                                        @php($unique_options    =   "bel_".$belonging_id."_prod_".$currProduct["id"]."_prov_" .$currProductCurrVariation["providing_type"]."_perc_".
                                                        $currProductCurrVariation["percentage_type"]."_rep_" . $currProductCurrVariation["repayment_type"]."_rep_loan_" .
                                                         intval($currProductCurrVariation["repayment_loan_interval_type_id"]) . "_rep_perc_" .intval($currProductCurrVariation["repayment_percent_interval_type_id"]))

                                                        @php($unique_options_and_search_params  =   $unique_options."*"."_cost_".$car_cost."_prepayment_".$prepayment."_term_".$loan_term_search_in_days)

                                                        @php($unique_options_and_search_params    =   str_rot13($unique_options_and_search_params))

                                                        @php($unique_options    =   md5($unique_options))

                                                        <button type="button" data-options="{{$unique_options}}"
                                                                data-belongingId="{{$belonging_id}}"
                                                                data-product-id='{{$currProduct["id"]}}'
                                                                data-cost="{{$car_cost}}"
                                                                data-prepayment="{{$prepayment}}"
                                                                data-term="{{$loan_term_search_in_days}}"
                                                                class="btn btn_compare btn-white @if(in_array($unique_options,$checked_variations)) compare_act_button_checked @endif">

                                                            <i class="icon icon-left icon-add"></i>
                                                            <span>համեմատել</span>
                                                        </button>
                                                        <a href="{{url('/car-loan-product/'.$unique_options.'/'.$car_cost.'/'.$prepayment_final.'/'.$time_type.'/'.$loan_term)}}"
                                                           class="btn btn-more">
                                                            <span>ավելին</span>
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
                        <div class="pagination_sexion product_variations_results_pagination">
                            {{ $productsWithVariations->appends([])->links('pagination::bootstrap-4') }}
                        </div>
                    </div>

                    <div class="change_item change_item_product_variations_grouped_by_company_results">

                        <div class="table-wrap head_grouped_by_company">
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
                        @foreach($productsWithVariationsGroupByCompany as $productsWithVariationsGroupByCompanyCurr)

                            @php( $currCompanyImg   =  $productsWithVariationsGroupByCompanyCurr[0]["companyInfo"]->image)

                            @php( $currCompanyId   =  $productsWithVariationsGroupByCompanyCurr[0]["company_id"])

                            <div class="wrapper min-pading">
                                <div class="table-wrapper">
                                    <div class="th">
                                        <a target="_blank"
                                           href="{{url('/company-branches-and-bankomats/'.$currCompanyId)}}">
                                            <img src="{{backend_asset('savedImages/'.$currCompanyImg )}}">
                                        </a>
                                    </div>

                                    <div class="th">
                                        <span>{{$productsWithVariationsGroupByCompanyCurr[0]["percentage"]}}</span>
                                    </div>

                                    <div class="th">
                                        <span>{{$productsWithVariationsGroupByCompanyCurr[0]["require_payments"]}}</span>
                                    </div>

                                    <div class="th">
                                        <span>{{$productsWithVariationsGroupByCompanyCurr[0]["sum_payments"]}}</span>
                                    </div>

                                    <div class="th">
                                        <span>{{round($productsWithVariationsGroupByCompanyCurr[0]["factual_percentage"], 2) }}</span>
                                    </div>

                                    <div class="th flex-wrapper">
                                        <button class="btn btn-pink other_suggestions_open_close">
                                            <section>{{count($productsWithVariationsGroupByCompanyCurr)-1}}</section>
                                            <i class="icon icon-arrow-down"></i>
                                        </button>

                                        @php($firstProductVariation =    $productsWithVariationsGroupByCompanyCurr[0])

                                        @php($unique_options    =   "bel_".$belonging_id."_prod_".$firstProductVariation["product_id"]."_prov_" .$firstProductVariation["providing_type"]."_perc_".
                                        $firstProductVariation["percentage_type"]."_rep_" . $firstProductVariation["repayment_type"]."_rep_loan_" .
                                         intval($firstProductVariation["repayment_loan_interval_type_id"]) . "_rep_perc_" .intval($firstProductVariation["repayment_percent_interval_type_id"]))

                                        @php($unique_options_and_search_params  =   $unique_options."*"."_cost_".$car_cost."_prepayment_".$prepayment."_term_".$loan_term_search_in_days)

                                        @php($unique_options_and_search_params    =   str_rot13($unique_options_and_search_params))

                                        @php($unique_options    =   md5($unique_options))

                                        <button type="button" data-options="{{$unique_options}}"
                                                data-belongingId="{{$belonging_id}}"
                                                data-product-id='{{$firstProductVariation["product_id"]}}'
                                                data-cost="{{$car_cost}}"
                                                data-prepayment="{{$prepayment}}"
                                                data-term="{{$loan_term_search_in_days}}"
                                                class="btn btn_compare btn-white @if(in_array($unique_options,$checked_variations)) compare_act_button_checked @endif">
                                            <i class="icon icon-add icon-add-mini"></i>
                                        </button>

                                        <a href="{{url('/car-loan-product/'.$unique_options.'/'.$car_cost.'/'.$prepayment_final.'/'.$time_type.'/'.$loan_term)}}"
                                           class="btn btn-more">
                                            <i class="icon icon-right icon-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>

                                <div class="hide-show">
                                    @if(count($productsWithVariationsGroupByCompanyCurr) > 1)
                                        @php( $productsWithVariationsGroupByCompanyCurr =   $productsWithVariationsGroupByCompanyCurr->toArray())

                                        @php( array_shift($productsWithVariationsGroupByCompanyCurr))

                                        @foreach($productsWithVariationsGroupByCompanyCurr as $productsWithVariationsGroupByCompanyCurrVariationCurr)
                                            <div class="table-wrapper">
                                                <div class="th">
                                                    <a target="_blank"
                                                       href="{{url('/company-branches-and-bankomats/'.$currCompanyId)}}">
                                                        <img src="{{backend_asset('savedImages/'.$currCompanyImg )}}">
                                                    </a>
                                                </div>

                                                <div class="th">
                                                    <span>{{$productsWithVariationsGroupByCompanyCurrVariationCurr["percentage"]}}</span>
                                                </div>

                                                <div class="th">
                                                    <span> {{$productsWithVariationsGroupByCompanyCurrVariationCurr["require_payments"]}}</span>
                                                </div>

                                                <div class="th">
                                                    <span> {{$productsWithVariationsGroupByCompanyCurrVariationCurr["sum_payments"]}}</span>
                                                </div>

                                                <div class="th">
                                                    <span>{{round($productsWithVariationsGroupByCompanyCurrVariationCurr["factual_percentage"], 4) }}</span>
                                                </div>

                                                <div class="th flex-wrapper ">

                                                    @php($unique_options    =   "bel_".$belonging_id."_prod_".$productsWithVariationsGroupByCompanyCurrVariationCurr["product_id"]."_prov_" .$productsWithVariationsGroupByCompanyCurrVariationCurr["providing_type"]."_perc_".
                                                    $productsWithVariationsGroupByCompanyCurrVariationCurr["percentage_type"]."_rep_" . $productsWithVariationsGroupByCompanyCurrVariationCurr["repayment_type"]."_rep_loan_" .
                                                     intval($productsWithVariationsGroupByCompanyCurrVariationCurr["repayment_loan_interval_type_id"]) . "_rep_perc_" .intval($productsWithVariationsGroupByCompanyCurrVariationCurr["repayment_percent_interval_type_id"]))

                                                    @php($unique_options_and_search_params  =   $unique_options."*"."_cost_".$car_cost."_prepayment_".$prepayment."_term_".$loan_term_search_in_days)

                                                    @php($unique_options_and_search_params    =   str_rot13($unique_options_and_search_params))

                                                    @php($unique_options    =   md5($unique_options))

                                                    <button type="button" data-options="{{$unique_options}}"
                                                            data-belongingId="{{$belonging_id}}"
                                                            data-product-id='{{$productsWithVariationsGroupByCompanyCurrVariationCurr["product_id"]}}'
                                                            data-cost="{{$car_cost}}"
                                                            data-prepayment="{{$prepayment}}"
                                                            data-term="{{$loan_term_search_in_days}}"
                                                            class="btn btn_compare btn-white @if(in_array($unique_options,$checked_variations)) compare_act_button_checked @endif">

                                                        <i class="icon icon-add icon-add-mini"></i>
                                                    </button>

                                                    <a href="{{url('/car-loan-product/'.$unique_options.'/'.$car_cost.'/'.$prepayment_final.'/'.$time_type.'/'.$loan_term)}}"
                                                       class="btn btn-more">
                                                        <i class="icon  icon-arrow-right"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        @endforeach
                        <div class="pagination_sexion product_variations_grouped_by_company_results_pagination">
                            {{ $productsWithVariationsGroupByCompany->appends([])->links('pagination::bootstrap-4') }}
                        </div>
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

            //filter_products("{{$belonging_id}}", "{{url('/car-loans-filters/')}}");
        });

        $(document).on('click', '.product_variations_results_pagination a', function (e) {
            e.preventDefault();

            page = $(this).attr('href').split('page=')[1];


            if ($(".product_variations_grouped_by_company_results_pagination .pagination").length == 0) {
                page_by_company = 1;
            }
            else {
                page_by_company = $(".product_variations_grouped_by_company_results_pagination .pagination .active span").text();
            }

            filter_products("{{url('/car-loans-filters/')}}", page, page_by_company);
        })
    });
</script>

@include('layouts.footer')