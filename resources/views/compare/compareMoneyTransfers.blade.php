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
                            <label class="label">Ստացող երկիր</label>
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

                        <div class="columns large-3 medium-6 small-12 dropDownSemantic">
                            <label class="label">Արժույթ</label>

                            <div class="select_inputs">
                                <div class="">
                                    <div class="ui fluid search selection dropdown multiple_select semantic_dropdown_simple">
                                        <input type="hidden" name="currency" value="{{$currency}}">
                                        <i class="dropdown icon"></i>
                                        <div class="default text">Ընտրել ցանկից</div>
                                        <div class="menu">
                                            @foreach($money_transfer_currencies_all_types as $money_transfer_currencies_all_type)
                                                <div class="item"
                                                     data-value="{{$money_transfer_currencies_all_type->id}}">{{$money_transfer_currencies_all_type->name}}</div>
                                            @endforeach

                                        </div>
                                    </div>
                                </div>
                                @if ($errors->has('currency'))
                                    <span class="help-block err-field">
                                        <strong>{{ $errors->first('currency') }}</strong>
                                    </span>
                                @endif
                            </div>

                        </div>

                        <div class="columns large-3 medium-6 small-12">
                            <label class="label" for="amount">Գումար</label>
                            <div class="rel">
                                <input type="number" id="transfer_amount" name="transfer_amount"
                                       value="{{$transfer_amount}}"
                                       class="input no_negative_value">

                                <input type="hidden" id="money_transfer_amount_min"
                                       value="0">

                                <input type="hidden" id="money_transfer_amount_max"
                                       value="10000000">


                                <div id="slider-range-money-transfer"></div>
                                <i class="icon icon-right icon-dram"></i>
                            </div>

                            @if ($errors->has('transfer_amount'))
                                <span class="help-block err-field">
                                    <strong>{{ $errors->first('transfer_amount') }}</strong>
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
                                    <span>Փոխանցման ձև</span>
                                </div>
                                <div class="check-drop-down transfer-type-check-drop-down">
                                    <div class="custom-select-second wrapper transfer_type_custom_select">
                                        <select id="transfer_type" name="transfer_type"
                                                class="filter_product filter_selectbox filter_transfer_type">
                                            <option value="">Փոխանցման ձև</option>
                                            @foreach($transfer_types as $transfer_type)
                                                <option value="{{$transfer_type->id}}">{{$transfer_type->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="check-drop-down-wrapper">
                                <div class="check-drop-title">
                                    <span>Համակարգի անվանում</span><i></i>
                                </div>
                                <div class="check-drop-down">
                                    @foreach($transfer_systems as $transfer_system)
                                        <label class="container">{{$transfer_system["info"]->name}}
                                            <input type="checkbox" id="transfer_system_{{$transfer_system['id']}}"
                                                   data-id="{{$transfer_system['id']}}"
                                                   name="transfer_system_{{$transfer_system['id']}}"
                                                   value="1"
                                                   class="filter_product filter_checkbox filter_transfer_system">
                                            <span class="checkmark"></span>
                                            <span class="single_filter_count transfer_system_filter_count">{{$transfer_system["count"]}}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div class="check-drop-down-wrapper">
                                <div class="check-drop-title">
                                    <span>Ստացող բանկ</span><i></i>
                                </div>
                                <div class="check-drop-down">
                                    @foreach($transfer_banks as $transfer_bank)
                                        <label class="container">{{$transfer_bank["info"]->name}}
                                            <input type="checkbox" id="transfer_bank_{{$transfer_bank['id']}}"
                                                   data-id="{{$transfer_bank['id']}}"
                                                   name="transfer_bank_{{$transfer_bank['id']}}"
                                                   value="1"
                                                   class="filter_product filter_checkbox filter_transfer_bank">
                                            <span class="checkmark"></span>
                                            <span class="single_filter_count transfer_bank_filter_count">{{$transfer_bank["count"]}}</span>
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