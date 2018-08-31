@extends('layouts.default')

@include('layouts.head')

@include('layouts.header')

<main>
    <div class="back-fon" style="background-image: url({{asset('img/blue-fon.png')}});height: 340px;">

    </div>
    <div class="row align-center">
        <div class="columns  large-6 medium-6 small-9">
            <div class="filter-title">
                Համեմատեք ֆինանսական առաջարկները
            </div>
        </div>
        <div class="columns  large-12 medium-12 small-12">
            <form>
                <div class="wrapper filter-home one">
                    Ես փնտրում եմ
                    <span class="click-ther">
                                <p>
                                    vark
                                </p>
                                <i class="icon icon-arrow-down"></i>
                            </span>

                    <div class="drop-select-menu wrapper">
                        <div class="drop-menu-section">
                            <div class="drop-menu-title">Առաջարկներ</div>
                            <div class="drop-menu-punkt-section">
                                <div class="drop-menu-punkt">Ավանդ</div>
                                <div class="drop-menu-punkt">Ավանդ</div>
                                <div class="drop-menu-punkt">Ավանդ</div>
                                <div class="drop-menu-punkt">Ավանդ</div>
                                <div class="drop-menu-punkt">Ավանդ</div>
                            </div>
                        </div>
                        <div class="drop-menu-section">
                            <div class="drop-menu-title">վարկային Առաջարկներ</div>
                            <div class="drop-section">
                                <div class="drop-menu-punkt">Ավտովարկ</div>
                                <div class="drop-menu-punkt">Ավտովարկ</div>
                                <div class="drop-menu-punkt">Ավտովարկ</div>
                                <div class="drop-menu-punkt">Ավտովարկ</div>
                                <div class="drop-menu-punkt">Ավտովարկ</div>
                                <div class="drop-menu-punkt">Ավտովարկ</div>
                                <div class="drop-menu-punkt">Ավտովարկ</div>
                                <div class="drop-menu-punkt">Ավտովարկ</div>
                                <div class="drop-menu-punkt">Ավտովարկ</div>
                                <div class="drop-menu-punkt">Ավտովարկ</div>
                                <div class="drop-menu-punkt">Ավտովարկ</div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <span class="filter-home-atr">
                                <input type="number" pattern="^[ 0-9]+$" size="40" placeholder="100 000" required="*"><i class="icon icon-right icon-dram"></i>
                            </span>
                    գումարով և
                    <span class="filter-home-atr">
                                <div class="custom-select">
                                    <select>
                                        <option value="0">24 ամիս <i class="icon icon-arrow-down"></i></option>
                                        <option value="1">48 ամիս</option>
                                        <option value="2">4 ամիս</option>
                                        <option value="3">2 ամիս</option>
                                        <option value="1">48 ամիս</option>
                                        <option value="2">4 ամիս</option>
                                        <option value="3">2 ամիս</option>
                                        <option value="1">48 ամիս</option>
                                        <option value="2">4 ամիս</option>
                                        <option value="3">2 ամիս</option>
                                        <option value="1">48 ամիս</option>
                                        <option value="2">4 ամիս</option>
                                        <option value="3">2 ամիս</option>
                                    </select>
                                </div>
                            </span>
                    ժամկետով
                </div>
                <button class="btn btn-serch">
                    <i class="icon icon-left  icon-search"></i>
                    <span>
                                    Փնտրել առաջարկներ
                            </span>
                </button>
            </form>
        </div>
        <div class="columns  large-12 medium-12 small-12">
            <div class="best_offer_title">
                Լավագույն Առաջարկներ
            </div>
        </div>
        @foreach($belongings as $belonging)
            <div class="columns  large-4 medium-6 small-12">
                <a href="{{url($belonging->productsByBelongingInfo->first()->compare_url)}}" class="pading_center offer" >
                    <i>
                        <img class="belonging_icon" src="{{asset($belonging->icon)}}" />
                    </i>
                    <div class="offert-title">
                        {{$belonging->name}}
                    </div>
                </a>
            </div>
        @endforeach



    </div>
</main>



@include('layouts.footer')