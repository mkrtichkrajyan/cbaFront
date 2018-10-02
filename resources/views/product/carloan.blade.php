@extends('layouts.default')

@include('layouts.headProduct')

@include('layouts.header')

<input type="hidden" value="{{backend_asset_path()}}" id="backend_asset_path" name="backend_asset_path">

<main>
    <div class="row" style="position: relative;">
        <div class="back-fon position">

        </div>
        <div class="columns large-12 medium-12 small-12">
            <div class="listing-title product-listing">
                <div class="left">
                    <a href="" class="come-back title-map">
                        <div class="come-back-icon">
                            <i class="icon icon-back-arrow"></i>
                        </div>
                        <span>Պրոդուկտ</span>
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
                            <span>
                                    ավելին
                            </span>
                                <i class="icon icon-arrow-down"></i>
                            </button>
                        </div>
                    </section>
                </div>
                <div class="right">
                    <div class="add-functions">
                        <button class="btn btn-red">
                            <i class="icon icon-left  icon-add"></i>
                            <span>
                                համեմատել
                        </span>
                        </button>
                        <a href="?p=bank_list" class="btn btn-more">
                            <section class="right-border">
                                <i class="icon icon-location">

                                </i></section>
                            <span>
                            Մասնաճյուղեր և Բանկոմատներ
                        </span>
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
                        <div class="all-price-title">
                            Վարկի գումար
                        </div>
                        <div class="all-total-price">
                        <span>
                        20 000 000
                        </span>
                            <i class=" icon icon-dram"></i>
                        </div>
                    </div>
                    <div class="pircent-price">
                        <div class="all-price-title">
                            Ավել վճարվուղ գումար
                        </div>
                        <div class="all-total-price">
                        <span>
                        5 000 000
                        </span>
                            <i class=" icon icon-dram"></i>
                        </div>
                    </div>
                </div>
                <div class="end-price">
                    <div>
                        <div class="all-price-title">
                            Ընդամենը հետ վճարվող գումար
                        </div>
                        <div class="all-total-price">
                        <span>
                        25 000 000
                        </span>
                            <i class=" icon icon-dram"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="final-settlement">
                <div class="actual-percent">
                    <div class="all-price-title">
                        Փաստացի տոկոսադրույք
                    </div>
                    <div class="all-total-price">
                    <span class="chart-count-1">
                    25%
                    </span>
                    </div>
                </div>
                <div class="nominal-percent">
                    <div class="all-price-title">
                        Անվանական տոկոսադրույք
                    </div>
                    <div class="all-total-price">
                    <span class="chart-count-2">
                        0.5%
                    </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="columns large-12 medium-12 small-12">
            <div class="wrapper margins prise">
                <div class="listing-title">
                    <div class="left">
                        <div class="document-title">
                        <span>
                            Պարտադիր վճարներ
                        </span>
                        </div>
                    </div>
                    <div class="right">
                        <div class="chenge-wrap">
                            <button class="chenge">
                            <span>
                                    Տարեկան
                            </span>
                            </button>
                            <button class="chenge">
                            <span>
                                    Ընդհանուր
                            </span>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="prise-cont ">
                    <div class="product-prise-wrapper">
                        <div class="prise-title">
                            <div class="left product-prise">
                            <span>
                                Վարկային հայտի ուսումնասիրության վճար (միանվագ)
                            </span>
                            </div>
                            <div class="right product-prise">
                            <span>
                                1200
                            </span>
                                <i class="icon  icon-dram"></i>
                            </div>
                        </div>
                        <div class="prise-title">
                            <div class="left product-prise">
                            <span>
                                Վարկի սպասարկման վճար (տարեկան)
                            </span>
                            </div>
                            <div class="right product-prise">
                            <span>
                                1200
                            </span>
                                <i class="icon  icon-dram"></i>
                            </div>
                        </div>
                        <div class="prise-title">
                            <div class="left product-prise">
                            <span>
                                Գրավի գնահատման վճար (միանվագ)
                            </span>
                            </div>
                            <div class="right product-prise">
                            <span>
                                1200
                            </span>
                                <i class="icon  icon-dram"></i>
                            </div>
                        </div>
                        <div class="prise-title">
                            <div class="left product-prise">
                            <span>
                                Կանխիկացման վճար
                            </span>
                            </div>
                            <div class="right product-prise">
                            <span>
                                1200
                            </span>
                                <i class="icon  icon-dram"></i>
                            </div>
                        </div>
                        <div class="prise-title">
                            <div class="left product-prise">
                            <span>
                                Գրավի ապահովագրության վճար (տարեկան)
                            </span>
                            </div>
                            <div class="right product-prise">
                            <span>
                                1200
                            </span>
                                <i class="icon  icon-dram"></i>
                            </div>
                        </div>
                        <div class="prise-title">
                            <div class="left product-prise">
                            <span>
                                Նոտարական վավերացման վճար (միանվագ)
                            </span>
                            </div>
                            <div class="right product-prise">
                            <span>
                                1200
                            </span>
                                <i class="icon  icon-dram"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="total-prise-title">
                    <div class="left total-prise">
                    <span>
                            Ընդհանուր գումար
                    </span>
                    </div>
                    <div class="right total-prise">
                    <span>
                        1200
                    </span>
                        <i class="icon  icon-dram"></i>
                    </div>
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
                        <div class="map-chenge chenge">
                            Ավտոսրահներ
                        </div>
                    </div>
                </div>
                <div class="change_item item-scroll">
                    <div class="prise-cont other-info-wrapper">
                        <div class="product-prise-wrapper other-info">
                            <div class="prise-title">
                                <div class="left  other-info-title">
                                    <span>Ավտոմեքենա</span>
                                </div>
                                <div class="right other-info-text">
                                    <span>{{@$product->carInfo->name}}</span>
                                </div>
                            </div>
                            <div class="prise-title">
                                <div class="left  other-info-title">
                                    <span>Մարման եղանակ</span>
                                </div>
                                <div class="right other-info-text">
                                    <span>հավասարաչափ, ոչ հավասարաչափ՝ իր բոլոր ենթատարբերակներով</span>
                                </div>
                            </div>
                            <div class="prise-title">
                                <div class="left  other-info-title">
                                    <span>Տրամադրման եղանակ</span>
                                </div>
                                <div class="right other-info-text">
                                    <span>{{@$product->providingTypeInfo->name}}</span>
                                </div>
                            </div>
                        </div>
                        <div class="product-prise-wrapper other-info">
                            <div class="prise-title">
                                <div class="left  other-info-title">
                                    <span>Ապահովվածություն</span>
                                </div>
                                <div class="right other-info-text">
                                    <span>
                                          @foreach($product->securityTypes as $key=>$productSecurityType)
                                            {{ $productSecurityType->securityTypeInfo->name}} @if($product->securityTypes->count()>1 && $productSecurityType->keys()->first()), @endif
                                        @endforeach
                                    </span>
                                </div>
                            </div>
                            <div class="prise-title">
                                <div class="left  other-info-title">
                                    <span>Ծառայությունը ընկերության կայքէջում</span>
                                </div>
                                <div class="right other-info-text">
                                    <span>{{$product->loan_pledge_ratio}}</span>
                                </div>
                            </div>

                            <div class="prise-title">
                                <div class="left  other-info-title">
                                    <span>Տոկոսադրույք</span>
                                </div>
                                <div class="right other-info-text">
                                    <span>հաստատուն՝ 6</span>
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
                        <tr>
                            <td>
                                12
                            </td>
                            <td>
                                456
                            </td>
                            <td>
                                4567890
                            </td>
                            <td>
                                123456789
                            </td>
                            <td>
                                098765
                            </td>
                            <td>
                                8754365434
                            </td>
                        </tr>
                        <tr>
                            <td>
                                12
                            </td>
                            <td>
                                456
                            </td>
                            <td>
                                4567890
                            </td>
                            <td>
                                123456789
                            </td>
                            <td>
                                098765
                            </td>
                            <td>
                                8754365434
                            </td>
                        </tr>
                        <tr>
                            <td>
                                12
                            </td>
                            <td>
                                456
                            </td>
                            <td>
                                4567890
                            </td>
                            <td>
                                123456789
                            </td>
                            <td>
                                098765
                            </td>
                            <td>
                                8754365434
                            </td>
                        </tr>
                        <tr>
                            <td>
                                12
                            </td>
                            <td>
                                456
                            </td>
                            <td>
                                4567890
                            </td>
                            <td>
                                123456789
                            </td>
                            <td>
                                098765
                            </td>
                            <td>
                                8754365434
                            </td>
                        </tr>
                        <tr>
                            <td>
                                12
                            </td>
                            <td>
                                456
                            </td>
                            <td>
                                4567890
                            </td>
                            <td>
                                123456789
                            </td>
                            <td>
                                098765
                            </td>
                            <td>
                                8754365434
                            </td>
                        </tr>
                        <tr>
                            <td>
                                12
                            </td>
                            <td>
                                456
                            </td>
                            <td>
                                4567890
                            </td>
                            <td>
                                123456789
                            </td>
                            <td>
                                098765
                            </td>
                            <td>
                                8754365434
                            </td>
                        </tr>
                        <tr>
                            <td>
                                12
                            </td>
                            <td>
                                456
                            </td>
                            <td>
                                4567890
                            </td>
                            <td>
                                123456789
                            </td>
                            <td>
                                098765
                            </td>
                            <td>
                                8754365434
                            </td>
                        </tr>
                    </table>
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
                        <tr>
                            <td>
                                12
                            </td>
                            <td>
                                456
                            </td>
                            <td>
                                4567890
                            </td>
                            <td>
                                123456789
                            </td>
                            <td>
                                098765
                            </td>
                            <td>
                                8754365434
                            </td>
                        </tr>
                        <tr>
                            <td>
                                12
                            </td>
                            <td>
                                456
                            </td>
                            <td>
                                4567890
                            </td>
                            <td>
                                123456789
                            </td>
                            <td>
                                098765
                            </td>
                            <td>
                                8754365434
                            </td>
                        </tr>
                        <tr>
                            <td>
                                12
                            </td>
                            <td>
                                456
                            </td>
                            <td>
                                4567890
                            </td>
                            <td>
                                123456789
                            </td>
                            <td>
                                098765
                            </td>
                            <td>
                                8754365434
                            </td>
                        </tr>
                        <tr>
                            <td>
                                12
                            </td>
                            <td>
                                456
                            </td>
                            <td>
                                4567890
                            </td>
                            <td>
                                123456789
                            </td>
                            <td>
                                098765
                            </td>
                            <td>
                                8754365434
                            </td>
                        </tr>
                        <tr>
                            <td>
                                12
                            </td>
                            <td>
                                456
                            </td>
                            <td>
                                4567890
                            </td>
                            <td>
                                123456789
                            </td>
                            <td>
                                098765
                            </td>
                            <td>
                                8754365434
                            </td>
                        </tr>
                        <tr>
                            <td>
                                12
                            </td>
                            <td>
                                456
                            </td>
                            <td>
                                4567890
                            </td>
                            <td>
                                123456789
                            </td>
                            <td>
                                098765
                            </td>
                            <td>
                                8754365434
                            </td>
                        </tr>
                        <tr>
                            <td>
                                12
                            </td>
                            <td>
                                456
                            </td>
                            <td>
                                4567890
                            </td>
                            <td>
                                123456789
                            </td>
                            <td>
                                098765
                            </td>
                            <td>
                                8754365434
                            </td>
                        </tr>
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
                        <span>
                            Տույժ/տուգանք
                        </span>
                        </div>
                    </div>
                    {{--<div class="right">--}}
                        {{--<div class="chenge-wrap">--}}
                            {{--<button class="chenge">--}}
                            {{--<span>--}}
                                    {{--Տարեկան--}}
                            {{--</span>--}}
                            {{--</button>--}}
                            {{--<button class="chenge">--}}
                            {{--<span>--}}
                                    {{--Ընդհանուր--}}
                            {{--</span>--}}
                            {{--</button>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                </div>
                <div class="prise-cont ">
                    <div class="product-prise-wrapper">
                        <div class="prise-title">
                            <div class="left product-prise">
                            <span>
                               Վարկի մայր գումարը չվճարելու դեպքում
                            </span>
                            </div>
                            <div class="right product-prise">
                                <span>ֆգդֆգդֆգդֆ գֆգդֆգ դֆգֆգ</span>
                                {{--<i class="icon  icon-dram"></i>--}}
                            </div>
                        </div>
                        <div class="prise-title">
                            <div class="left product-prise">
                            <span>
                                Տոկոսագումարները չվճարելու դեպքում
                            </span>
                            </div>
                            <div class="right product-prise">
                            <span>
                                ֆդգդֆգդֆգդֆգդֆգ ֆդգդֆգդֆգ
                            </span>
                                {{--<i class="icon  icon-dram"></i>--}}
                            </div>
                        </div>
                        <div class="prise-title">
                            <div class="left product-prise">
                            <span>
                                Այլ վճարները չկատարելու դեպքում
                            </span>
                            </div>
                            <div class="right product-prise">
                            <span>
                                սյդյասլկյդայսդ ւհիըւի
                            </span>
                                {{--<i class="icon  icon-dram"></i>--}}
                            </div>
                        </div>

                        <div class="prise-title">
                            <div class="left product-prise">
                            <span>
                                Այլ
                            </span>
                            </div>
                            <div class="right product-prise">
                            <span>
                                սյդյասլկյդայսդ դֆգֆգֆգ
                            </span>
                                {{--<i class="icon  icon-dram"></i>--}}
                            </div>
                        </div>
                    </div>
                </div>

                {{--<div class="total-prise-title">--}}
                    {{--<div class="left total-prise">--}}
                    {{--<span>--}}
                            {{--Ընդհանուր գումար--}}
                    {{--</span>--}}
                    {{--</div>--}}
                    {{--<div class="right total-prise">--}}
                    {{--<span>--}}
                        {{--1200--}}
                    {{--</span>--}}
                        {{--<i class="icon  icon-dram"></i>--}}
                    {{--</div>--}}
                {{--</div>--}}
                {{--<div class="more-info">--}}
                    {{--<button class="btn-text-blue">--}}
                    {{--<span>--}}
                            {{--ավելին--}}
                    {{--</span>--}}
                        {{--<i class="icon icon-arrow-down"></i>--}}
                    {{--</button>--}}
                {{--</div>--}}
            </div>
        </div>

        <div class="columns large-12 medium-12 small-12">
            <div class="wrapper margins show-wrap">
                <div class="listing-title">
                    <div class="left">
                        <div class="document-title">
                        <span>
                            Այլ տվյալներ
                        </span>
                        </div>
                    </div>
                </div>
                <div class="prise-cont other-info-wrapper">
                    <div class="product-prise-wrapper other-info">
                        <div class="prise-title">
                            <div class="left  other-info-title">
                            <span>
                                    Ավտոմեքենա
                            </span>
                            </div>
                            <div class="right other-info-text">
                            <span>
                                    առաջնային շուկա, երկրորդային շուկա
                            </span>
                            </div>
                        </div>
                        <div class="prise-title">
                            <div class="left  other-info-title">
                            <span>
                                    Ավտոմեքենա
                            </span>
                            </div>
                            <div class="right other-info-text">
                            <span>
                                    հավասարաչափ, ոչ հավասարաչափ՝ իր բոլոր ենթատարբերակներով
                            </span>
                            </div>
                        </div>
                        <div class="prise-title">
                            <div class="left  other-info-title">
                            <span>
                                    Ավտոմեքենա
                            </span>
                            </div>
                            <div class="right other-info-text">
                            <span>
                                    առաջնային շուկա, երկրորդային շուկա
                            </span>
                            </div>
                        </div>
                        <div class="prise-title">
                            <div class="left  other-info-title">
                            <span>
                                    Տրամադրման
                                    եղանակ
                            </span>
                            </div>
                            <div class="right other-info-text">
                            <span>
                                    առաջնային շուկա, երկրորդային շուկա
                            </span>
                            </div>
                        </div>
                    </div>
                    <div class="product-prise-wrapper other-info">
                        <div class="prise-title">
                            <div class="left  other-info-title">
                            <span>
                                    Ավտոմեքենա
                            </span>
                            </div>
                            <div class="right other-info-text">
                            <span>
                                    առաջնային շուկա, երկրորդային շուկա
                            </span>
                            </div>
                        </div>
                        <div class="prise-title">
                            <div class="left  other-info-title">
                            <span>
                                    Ավտոմեքենա
                            </span>
                            </div>
                            <div class="right other-info-text">
                            <span>
                                    հավասարաչափ, ոչ հավասարաչափ՝ իր բոլոր ենթատարբերակներով
                            </span>
                            </div>
                        </div>
                        <div class="prise-title">
                            <div class="left  other-info-title">
                            <span>
                                    Ավտոմեքենա
                            </span>
                            </div>
                            <div class="right other-info-text">
                            <span>
                                    առաջնային շուկա, երկրորդային շուկա
                            </span>
                            </div>
                        </div>
                        <div class="prise-title">
                            <div class="left  other-info-title">
                            <span>
                                    Տրամադրման
                                    եղանակ
                            </span>
                            </div>
                            <div class="right other-info-text">
                            <span>
                                    առաջնային շուկա, երկրորդային շուկա
                            </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="blurring">
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


        <div class="columns large-12 medium-12 small-12">
            <div class="wrapper margins">
                <div class="document-title">
                    Փաստաթղթերի ցանկ
                </div>
                <div class="document-wrapper">

                    <div class="document">
                        <i class="icon  icon-doc"></i>
                        <span>
                            Դիմումի ձեւ
                        </span>
                    </div>
                    <div class="document">
                        <i class="icon  icon-doc"></i>
                        <span>
                            Դիմումի ձեւ
                        </span>
                    </div>
                    <div class="document">
                        <i class="icon  icon-doc"></i>
                        <span>
                            Դիմումի ձեւ
                        </span>
                    </div>
                    <div class="document">
                        <i class="icon  icon-doc"></i>
                        <span>
                            Դիմումի ձեւ
                        </span>
                    </div>

                    <div class="document">
                        <i class="icon  icon-doc"></i>
                        <span>
                            Դիմումի ձեւ
                        </span>
                    </div>
                    <div class="document">
                        <i class="icon  icon-doc"></i>
                        <span>
                            Դիմումի ձեւ
                        </span>
                    </div>
                    <div class="document">
                        <i class="icon  icon-doc"></i>
                        <span>
                            Դիմումի ձեւ
                        </span>
                    </div>
                    <div class="document">
                        <i class="icon  icon-doc"></i>
                        <span>
                            Դիմումի ձեւ
                        </span>
                    </div>

                    <div class="document">
                        <i class="icon  icon-doc"></i>
                        <span>
                            Դիմումի ձեւ
                        </span>
                    </div>
                    <div class="document">
                        <i class="icon  icon-doc"></i>
                        <span>
                            Դիմումի ձեւ
                        </span>
                    </div>
                    <div class="document">
                        <i class="icon  icon-doc"></i>
                        <span>
                            Դիմումի ձեւ
                        </span>
                    </div>
                    <div class="document">
                        <i class="icon  icon-doc"></i>
                        <span>
                            Դիմումի ձեւ
                        </span>
                    </div>

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