@extends('layouts.default')

@include('layouts.head')

@include('layouts.header')

<main>
    <div class="back-fon" style="background-image: url({{asset('img/blue-fon.png')}});height: 150px;">
    </div>

    <input type="hidden" value="{{backend_asset_path()}}" id="backend_asset_path" name="backend_asset_path">

    <input type="hidden" value="{{$request_results_count}}" id="request_results_count" name="request_results_count">

    <form id="seachProductForm" enctype="multipart/form-data" class="form-horizontal" name="carLoanForm"
          action="{{ url($currProductByBelongingsView->compare_url) }}" method="get">

        <div class="row">
            <div class="columns large-12 medium-12 small-12">
                <div class="listing-title">
                    <div class="left">
                        <a href="" class="come-back">
                            <div class="come-back-icon">
                                <i class="icon icon-back-arrrow"></i>
                            </div>
                            <span>Ավտովարկ</span>
                        </a>
                    </div>
                    <div class="right">
                        <div class="listing-icon">
                            <i class="icon   icon-car"></i>
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
                                       class="input no_negative_value" id="car_cost" value="{{$car_cost}}">

                                <input type="hidden" name="car_cost_search" id="car_cost_search" value="{{$car_cost}}">
                                {{--<input type="hidden" id="car_cost_min" class="car_cost_min" value="{{$car_cost_min}}"/>--}}

                                {{--<input type="hidden" id="car_cost_max" class="car_cost_max" value="{{$car_cost_max}}"/>--}}

                                {{--<div id="slider-range-min"></div>--}}
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

                                <input type="hidden" id="" name="prepayment_search" value="{{$prepayment}}">

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

            <div class="margin-top none columns large-3 medium-3 small-12">

                <div class="wrapper check-box-panel-wrapper  popup">
                    <form>
                        <div class="check-drop-down-wrapper">
                            <div class="check-drop-title">
                                <span>Ավտոմեքենա</span>
                                <i></i>
                            </div>
                            <div class="check-drop-down">
                                @foreach($car_types as $car_type)
                                    @if($car_type->id   !=  3)
                                        <label class="container">{{$car_type->name}}
                                            <input id="car_type_{{$car_type->id}}" data-id="{{$car_type->id}}"
                                                   name="car_type_{{$car_type->id}}"
                                                   type="checkbox" value="1"
                                                   class="filter_product filter_checkbox filter_car_type">
                                            <span class="checkmark"></span>
                                        </label>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        <div class="check-drop-down-wrapper">
                            <div class="check-drop-title">
                                <span>Տոկոսադրույք</span>
                                <i></i>
                            </div>
                            <div class="check-drop-down">

                                @foreach($percentage_types as $percentage_type)
                                    @if($percentage_type->id   !=  1)
                                        <label class="container">{{$percentage_type->name}}
                                            <input type="checkbox" id="percentage_type_{{$percentage_type->id}}"
                                                   data-id="{{$percentage_type->id}}"
                                                   name="percentage_type_{{$percentage_type->id}}"
                                                   value="1"
                                                   class="filter_product filter_checkbox filter_percentage_type">
                                            <span class="checkmark"></span>
                                        </label>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        <div class="check-drop-down-wrapper">
                            <div class="check-drop-title">
                                <span>Մարման եղանակ </span>
                                <i></i>
                            </div>
                            <div class="check-drop-down">
                                @foreach($repayment_types as $repayment_type)
                                    @if($repayment_type->id   !=  1)
                                        <label class="container">{{$repayment_type->name}}
                                            <input id="repayment_type_{{$repayment_type->id}}"
                                                   name="repayment_type_{{$repayment_type->id}}"
                                                   type="checkbox" value="1" data-id="{{$repayment_type->id}}"
                                                   class="filter_product filter_checkbox filter_repayment_type" ">
                                            <span class="checkmark"></span>
                                        </label>
                                    @endif
                                @endforeach
                            </div>

                            <div class="check-box check-drop-down">
                                <div class="custom-select wrapper">
                                    <select id="repayment_loan_interval_type" name="repayment_loan_interval_type"
                                            class="filter_product filter_selectbox filter_repayment_loan_interval_type">
                                        <option value="">Վարկ</option>
                                        @foreach($repayment_loan_interval_types as $repayment_loan_interval_type)
                                            <option value="{{$repayment_loan_interval_type->id}}">{{$repayment_loan_interval_type->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="custom-select wrapper">
                                    <select id="repayment_percent_interval_type" name="repayment_percent_interval_type"
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
                            <span>
                                    Տրամադրման եղանակ
                            </span>
                                <i></i>
                            </div>
                            <div class="check-drop-down">

                                @foreach($providing_types as $providing_type)
                                    @if($providing_type->id   !=  3)
                                        <label class="container">{{$providing_type->name}}
                                            <input type="checkbox" id="providing_type_{{$providing_type->id}}"
                                                   data-id="{{$providing_type->id}}"
                                                   name="providing_type_{{$providing_type->id}}"
                                                   value="1"
                                                   class="filter_product filter_checkbox filter_providing_type">
                                            <span class="checkmark"></span>
                                        </label>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        <div class="check-drop-down-wrapper">
                            <div class="check-drop-title">
                                <span>Ապահովվածություն </span>
                                <i></i>
                            </div>
                            <div class="check-drop-down">
                                <label class="container">բոլորը
                                    <input type="checkbox" id="security_type_0"
                                           name="security_type_0" value="1"
                                           class="curr_all_checkboxes_check_uncheck filter_product">
                                    <span class="checkmark"></span>
                                </label>

                                @foreach($security_types as $security_type)

                                    <label class="container">{{$security_type->name}}
                                        <input type="checkbox" id="security_type_{{$security_type->id}}"
                                               data-id="{{$security_type->id}}"
                                               class="filter_product filter_checkbox filter_security_type"
                                               name="security_type_{{$security_type->id}}" value="1" class="">
                                        <span class="checkmark"></span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        @if($special_projects_having_products_count > 0)
                            <div class="check-drop-down-wrapper">
                                <div class="check-drop-title">
                                    <span>Հատուկ Ծրագիր  </span>
                                    <i></i>
                                </div>
                                <div class="check-drop-down">

                                    @foreach($yes_no_answers as $yes_no_answer)

                                        <label class="container">{{$yes_no_answer->name}}
                                            <input type="checkbox" id="special_project_answer_{{$yes_no_answer->id}}"
                                                   name="special_project_answer_{{$yes_no_answer->id}}"
                                                   value="1" data-id="{{$yes_no_answer->id}}"
                                                   class="filter_product filter_checkbox filter_special_project">
                                            <span class="checkmark"></span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if($privileged_term_having_products_count > 0)
                            <div class="check-drop-down-wrapper">
                                <div class="check-drop-title">
                                    <span>Արտոնյալ ժամկետ</span>
                                    <i></i>
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
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </form>
                </div>
            </div>

            <div class="margin-top columns large-9 medium-auto product_results">
                {{--{{dd(!is_null($productsGroupByCompany))}}--}}
                @if(!is_null($productsGroupByCompany))

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
                                        <button type="button" class="btn btn-red">
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
                                           $companyProductsFiltered = $companyProducts->filter(function ($value, $key) {
                                               return $key > 1;
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
                                                        <button type="button" class="btn btn-red">
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

                                        <button class="btn btn-red"><i class="icon  icon-add"></i></button>

                                        <a href="" class="btn btn-more"><i
                                                    class="icon icon-right  icon-arrow-right"></i></a>
                                    </div>
                                </div>
                                <div class="hide-show">
                                    @if($productsGroupByCompanyCurr->count() > 1)
                                            @php(
                                               $productsGroupByCompanyCurrFiltered = $productsGroupByCompanyCurr->filter(function ($value, $key) {
                                                   return $key > 1;
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

                                                    <button class="btn btn-red"><i class="icon  icon-add"></i></button>

                                                    <a href="" class="btn btn-more"><i class="icon  icon-arrow-right"></i></a>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        @endforeach

                    </div>
                @endif

            </div>
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