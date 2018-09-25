@extends('layouts.default')

@include('layouts.head')

@include('layouts.header')

<main>
    <div class="back-fon" style="background-image: url({{asset('img/blue-fon.png')}});height: 150px;">

    </div>
    <div class="row">
        <div class="columns large-12 medium-12 small-12">
            <div class="listing-title">
                <div class="left">
                    <a href="{{$previousUrl}}" class="come-back">
                        <div class="come-back-icon">
                            <i class="icon icon-back-arrow"></i>
                        </div>
                        <span>Վճարային քարտ</span>
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
                        <label class="label">Արժույթ</label>

                        <div class="select_inputs">
                            <div class="">
                                <div class="ui fluid search selection dropdown multiple_select semantic_dropdown_simple">
                                    <input type="hidden" name="currency" value="{{$currency}}">
                                    <i class="dropdown icon"></i>
                                    <div class="default text">Ընտրել ցանկից</div>
                                    <div class="menu">
                                        @foreach($payment_card_currencies_types as $payment_card_currencies_type)
                                            <div class="item"
                                                 data-value="{{$payment_card_currencies_type->id}}">{{$payment_card_currencies_type->name}}</div>
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
                            <span>Քարտի տեսակ</span><i></i>
                        </div>
                        <div class="check-drop-down">
                            @foreach($payment_card_product_types as $payment_card_product_type)
                                <label class="container">{{$payment_card_product_type->name}}
                                    <input type="checkbox" id="payment_card_product_type_{{$payment_card_product_type->id}}"
                                           data-id="{{$payment_card_product_type->id}}"
                                           name="payment_card_product_type_{{$payment_card_product_type->id}}"
                                           value="1"
                                           class="filter_product filter_checkbox filter_payment_card_product_type">
                                    <span class="checkmark"></span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="check-drop-down-wrapper">
                        <div class="check-drop-title">
                            <span>Ռեգիոն</span><i></i>
                        </div>
                        <div class="check-drop-down">
                            @foreach($payment_card_regions as $payment_card_region)
                                <label class="container">{{$payment_card_region->name}}
                                    <input type="checkbox" id="payment_card_region_{{$payment_card_region->id}}"
                                           data-id="{{$payment_card_region->id}}"
                                           name="payment_card_region_{{$payment_card_region->id}}"
                                           value="1"
                                           class="filter_product filter_checkbox filter_payment_card_region">
                                    <span class="checkmark"></span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="check-drop-down-wrapper">
                        <div class="check-drop-title">
                            <span>Հատուկ քարտեր</span><i></i>
                        </div>
                        <div class="check-drop-down">
                            @foreach ($payment_specials_cards as $payment_specials_card)
                                <label class="container">{{$payment_specials_card->name}}
                                    <input type="checkbox" id="payment_specials_card_{{$payment_specials_card->id}}"
                                           data-id="{{$payment_specials_card->id}}"
                                           name="payment_specials_card_{{$payment_specials_card->id}}"
                                           value="1"
                                           class="filter_product filter_checkbox filter_payment_specials_card">
                                    <span class="checkmark"></span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="check-drop-down-wrapper">
                        <div class="check-drop-title">
                            <span>Լրացուցիչ</span><i></i>
                        </div>
                        <div class="check-drop-down">
                            @foreach ($payment_extra_cards as $payment_extra_card)
                                <label class="container">{{$payment_extra_card->name}}
                                    <input type="checkbox" id="payment_extra_card_{{$payment_extra_card->id}}"
                                           data-id="{{$payment_extra_card->id}}"
                                           name="payment_extra_card_{{$payment_extra_card->id}}"
                                           value="1"
                                           class="filter_product filter_checkbox filter_payment_extra_card">
                                    <span class="checkmark"></span>
                                </label>
                            @endforeach
                        </div>
                    </div>


                </form>
            </div>
        </div>
        <div class="margin-top columns large-9 medium-auto">
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
                @foreach($productsGroupByCompany as $companyProducts)

                <div class="change_item">

                    <div class="wrapper pading">
                        <div class="listing-title">
                            <div class="left">
                                <div class="category-title">
                                    {{$companyProducts->first()->name}}
                                </div>
                            </div>
                            <div class="right">
                                <div class="category-logo">
                                    {{--<img src="{{ env('BACKEND_URL') . 'savedImages/'.$companyProducts->first()->companyInfo->image }}">--}}
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
                                <button class="btn btn-red">
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
                                <button class="btn btn-pink">
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
                                                    <img src="{{$companyProductCurr->companyInfo->image}}">
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
                                                <button class="btn btn-red">
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
                </div>

            @endforeach
            @endif
        </div>
    </div>

</main>


@include('layouts.footer')