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
                    <a href="" class="come-back">
                        <div class="come-back-icon">
                            <i class="icon icon-back-arrrow"></i>
                        </div>
                        <span>Ոսկու վարկ</span>
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
                        <label class="label" for="amount">Ավտոմեքենայի արժեք</label>
                        <div class="rel">
                            <input type="text" id="minimym" class="input">
                            <div id="slider-range-min"></div>
                            <i class="icon icon-right icon-dram"></i>
                        </div>
                    </div>
                    <div class="columns large-3 medium-6 small-12">
                        <label class="label" for="amount">Կանխավճար</label>
                        <div class="rel">
                            <input type="text" id="maximym"  class="input">
                            <div id="slider-range-max"></div>
                            <i class="icon icon-right icon-dram"></i>
                        </div>
                    </div>
                    <div class="columns large-3 medium-6 small-12">
                        <label class="label" for="amount">Ժամկետ</label>
                        <div class="rel">
                            <input type="text" id="time" class="input">
                            <div class="chenge-time">
                            <span class="chenge-time-active active">
                                ամիս
                            </span>
                                <span class="chenge-time-active">
                                տարի
                            </span>
                            </div>
                        </div>
                    </div>
                    <div class="columns large-3 medium-6 small-12">
                        <label class="label" for="amount">Վարկի գումար</label>
                        <div class="rel">
                            <input type="text" id="price" class="input">
                            <i class="icon icon-right icon-dram"></i>
                        </div>
                    </div>
                    <div class="columns large-12 medium-12 small-12" >
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
                        <span>
                                Ավտոմեքենա
                        </span>
                            <i></i>
                        </div>
                        <div class="check-drop-down">
                            <label class="container">One
                                <input type="checkbox" checked="checked">
                                <span class="checkmark"></span>
                            </label>
                            <label class="container">Two
                                <input type="checkbox">
                                <span class="checkmark"></span>
                            </label>
                            <label class="container">Three
                                <input type="checkbox">
                                <span class="checkmark"></span>
                            </label>
                        </div>
                    </div>
                    <div class="check-drop-down-wrapper">
                        <div class="check-drop-title">
                        <span>
                                Մարման եղանակ
                        </span>
                            <i></i>
                        </div>
                        <div class="check-drop-down">
                            <label class="container">Բոլորը
                                <input type="checkbox" checked="checked">
                                <span class="checkmark"></span>
                            </label>
                            <label class="container"> Առաջնային շուկա
                                <input type="checkbox">
                                <span class="checkmark"></span>
                            </label>
                            <label class="container">Երկրորդային շուկա
                                <input type="checkbox">
                                <span class="checkmark"></span>
                            </label>
                        </div>
                        <div class="check-box check-drop-down">
                            <div class="custom-select wrapper">
                                <select>
                                    <option value="0">Բոլորը</option>
                                    <option value="1">Առաջնային շուկա</option>
                                    <option value="2">Երկրորդային շուկա</option>
                                    <option value="3">Երկրորդային շուկա</option>
                                </select>
                            </div>
                            <div class="custom-select wrapper">
                                <select>
                                    <option value="0">Բոլորը</option>
                                    <option value="1">Առաջնային շուկա</option>
                                    <option value="2">Երկրորդային շուկա</option>
                                    <option value="3">Երկրորդային շուկա</option>
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
                            <label class="container">One
                                <input type="checkbox" checked="checked">
                                <span class="checkmark"></span>
                            </label>
                            <label class="container">Two
                                <input type="checkbox">
                                <span class="checkmark"></span>
                            </label>
                            <label class="container">Three
                                <input type="checkbox">
                                <span class="checkmark"></span>
                            </label>
                        </div>
                    </div>
                    <div class="check-drop-down-wrapper">
                        <div class="check-drop-title">
                        <span>
                                Տոկոսադրույք
                        </span>
                            <i></i>
                        </div>
                        <div class="check-drop-down">
                            <label class="container">One
                                <input type="checkbox" checked="checked">
                                <span class="checkmark"></span>
                            </label>
                            <label class="container">Two
                                <input type="checkbox">
                                <span class="checkmark"></span>
                            </label>
                            <label class="container">Three
                                <input type="checkbox">
                                <span class="checkmark"></span>
                            </label>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="margin-top columns large-9 medium-auto">
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
                                    <img style="max-width: 80px;" src="{{ backend_asset('savedImages/'.$companyProducts->first()->companyInfo->image )}}">
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
                                        2 000 000  <i class="icons "></i>
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
                                                2 000 000  <i class="icons "></i>
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

        </div>
    </div>

</main>



@include('layouts.footer')