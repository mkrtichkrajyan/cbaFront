@extends('layouts.default')

@include('layouts.head')

@include('layouts.headerCompare')

@php($checked_variations =  $getCompareInfo[$belonging_id]["checked_variations"] )


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

                        <div class="columns large-3 medium-6 small-12 dropDownSemantic">
                            <label class="label">երկիր</label>
                            <div class="select_inputs">
                                <div class="">
                                    <div class="ui fluid search selection dropdown multiple_select semantic_dropdown_simple">
                                        <input type="hidden" name="country" value="{{$country}}">
                                        <i class="dropdown icon"></i>
                                        <div class="default text">Ընտրել ցանկից</div>
                                        <div class="menu">
                                            @foreach($countries as $countryCurr)
                                                <div class="item" data-value="{{$countryCurr->id}}"><i
                                                            class="{{$countryCurr->code}} flag"></i>{{$countryCurr->name_am}}
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @if ($errors->has('country'))
                                    <span class="help-block err-field">
                                        <strong>{{ $errors->first('country') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="columns large-3 medium-6 small-12">
                            <label class="label" for="amount">Տարիք</label>
                            <div class="rel">
                                <input type="number" class="no_negative_value number_not_more_than no_plus_allow"
                                       max="{{$max_age}}" id="age" name="age" value="{{$age}}"
                                       class="input">

                                <input type="hidden" name="age_search" id="age_search"
                                       value="{{$age}}">


                                <input type="hidden" id="min_age" name="min_age"
                                       value="{{$min_age}}">

                                <input type="hidden" id="max_age" name="max_age"
                                       value="{{$max_age}}">

                                <div id="slider-range-insurance-ages"></div>
                            </div>

                            @if ($errors->has('loan_amount'))
                                <span class="help-block err-field">
                                    <strong>{{ $errors->first('loan_amount') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="columns large-3 medium-6 small-12">
                            <label class="label" for="amount">Օրերի քանակ</label>
                            <div class="rel">

                                <input type="number" min="0" id="loan_term" name="loan_term"
                                       value="{{$loan_term}}" class="input no_negative_value">

                                <input type="hidden" name="loan_term_search" id="loan_term_search"
                                       value="{{$loan_term}}">

                                
                                <input type="hidden" name="time_type" id="time_type" value="{{$time_type}}">

                                <input type="hidden" name="time_type_search" id="time_type_search"
                                       value="{{$time_type}}">

                                <div class="chenge-time" style="display: none;">
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

                        <div class="right">
                            <button id="seachProductFormSubmitCheck" type="button" class="btn btn-red">
                                <i class="icon icon-left icon-search"></i>
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
                            @if($non_recoverable_expenses_answers[3]["count"] > 0)
                                <div class="check-drop-down-wrapper">
                                    <div class="check-drop-title">
                                        <span>Չհատուցվող գումար</span>
                                    </div>
                                    <div class="check-drop-down">

                                        @foreach($non_recoverable_expenses_answers as $non_recoverable_expenses_answer)

                                            <label class="container">{{$non_recoverable_expenses_answer["info"]->name}}
                                                <input type="checkbox"
                                                       id="non_recoverable_expense_answer_{{$non_recoverable_expenses_answer["id"]}}"
                                                       name="non_recoverable_expense_answer_{{$non_recoverable_expenses_answer["id"]}}"
                                                       value="1"
                                                       data-id="{{$non_recoverable_expenses_answer["id"]}}"
                                                       class="filter_product filter_checkbox filter_non_recoverable_expense">
                                                <span class="checkmark"></span>
                                                <span class="single_filter_count non_recoverable_amount_having_products_filter_count">{{$non_recoverable_expenses_answer["count"]}}</span>
                                            </label>

                                        @endforeach

                                    </div>
                                </div>
                            @endif

                            <div class="check-drop-down-wrapper">
                                <div class="check-drop-title">
                                    <span>Մուտքերի քանակ</span>
                                </div>
                                <div class="check-drop-down">

                                    @foreach($term_inputs_quantities as  $key => $term_inputs_quantity_curr)

                                        <label class="container">{{$term_inputs_quantity_curr["info"]}}
                                            <input type="checkbox"
                                                   id="term_inputs_quantity_{{$key}}"
                                                   name="term_inputs_quantity_{{$key}}"
                                                   value="1"
                                                   data-id="{{$key}}"
                                                   class="filter_product filter_checkbox filter_term_inputs_quantity">
                                            <span class="checkmark"></span>
                                            <span class="single_filter_count term_inputs_quantity_filter_count">{{$term_inputs_quantity_curr["count"]}}</span>
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
                                    <div class="table-pise-wrapper fill-available-width">

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
                                                Ապահովագրավճար
                                            </div>
                                            <div class="table-pise-text">
                                                {{$currProduct["variations"][0]["insurance_fee"]}}
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <div class="listing-title">
                                    <div class="left">

                                        @php($unique_options    =   "product_".$currProduct["id"]."_age_".$age."_loan_term_".$loan_term_search_in_days."_currency_".$currProduct["variations"][0]["currency"])

                                        <button data-options="{{$unique_options}}"
                                                data-product-id='{{$currProduct["id"]}}'
                                                data-variation-id='{{$currProduct["variations"][0]["id"]}}'
                                                type="button" class="btn btn_compare btn-white">
                                            <i class="icon icon-left  icon-add"></i>
                                            <span>համեմատել</span>
                                        </button>
                                        <a href="{{url('loan'.$currProduct["id"])}}"
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
                                                        <div class="category-logo">
                                                            <img style="max-width: 80px;"
                                                                 src="{{ backend_asset('savedImages/'.$currProduct["companyInfo"]->image )}}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="table">
                                                    <div class="table-pise-wrapper fill-available-width">

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
                                                                Ապահովագրավճար
                                                            </div>
                                                            <div class="table-pise-text">
                                                                {{$currProductCurrVariation["insurance_fee"]}}
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                                <div class="listing-title">
                                                    <div class="left">
                                                        <button type="button" class="btn btn_compare btn-white">
                                                            <i class="icon icon-left icon-add"></i>
                                                            <span>համեմատել</span>
                                                        </button>
                                                        <a href="{{url('/car-loan-product/'.$currProduct["id"])}}"
                                                           class="btn btn-more">
                                                            <span>ավելին</span>
                                                            <i class="icon icon-right icon-arrow-right"></i>
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

                    <div class="change_item change_item_product_variations_grouped_by_company_results">

                        <div class="table-wrap head_grouped_by_company">
                            <div class="td fill-available-width">
                                <span>Կազմակերպության անվանում</span>
                            </div>

                            <div class="td fill-available-width">
                                <span> Ապահովագրավճար </span>
                            </div>
                            <div class="td fill-available-width"></div>
                        </div>
                        @foreach($productsWithVariationsGroupByCompany as $productsWithVariationsGroupByCompanyCurr)

                            @php( $currCompanyImg   =  $productsWithVariationsGroupByCompanyCurr[0]["companyInfo"]->image)

                            @php( $currCompanyId   =  $productsWithVariationsGroupByCompanyCurr[0]["company_id"])

                            <div class="wrapper min-pading">
                                <div class="table-wrapper">
                                    <div class="th fill-available-width">
                                        <a target="_blank"
                                           href="{{url('/company-branches-and-bankomats/'.$currCompanyId)}}">
                                            <img src="{{backend_asset('savedImages/'.$currCompanyImg )}}">
                                        </a>
                                    </div>

                                    <div class="th fill-available-width">
                                        <span>{{round($productsWithVariationsGroupByCompanyCurr[0]["insurance_fee"], 4) }}</span>
                                    </div>

                                    <div class="th flex-wrapper fill-available-width">
                                        <button class="btn btn-pink other_suggestions_open_close">
                                            <section>{{count($productsWithVariationsGroupByCompanyCurr)-1}}</section>
                                            <i class="icon icon-arrow-down"></i>
                                        </button>

                                        <button class="btn btn-red"><i class="icon icon-add"></i></button>

                                        <a href="{{url('/car-loan-product/'.$currProduct["id"])}}" class="btn btn-more">
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
                                                <div class="th fill-available-width">
                                                    <a target="_blank"
                                                       href="{{url('/company-branches-and-bankomats/'.$currCompanyId)}}">
                                                        <img src="{{backend_asset('savedImages/'.$currCompanyImg )}}">
                                                    </a>
                                                </div>

                                                <div class="th fill-available-width">
                                                    <span>{{$productsWithVariationsGroupByCompanyCurrVariationCurr["insurance_fee"]}}</span>
                                                </div>

                                                <div class="th flex-wrapper fill-available-width">

                                                    <button class="btn btn-red"><i class="icon  icon-add"></i></button>

                                                    <a href="" class="btn btn-more">
                                                        <i class="icon  icon-arrow-right"></i>
                                                    </a>
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