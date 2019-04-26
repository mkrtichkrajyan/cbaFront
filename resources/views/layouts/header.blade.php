<body>
    <header>
        <div class="popup-over">
            <div class="close-warning">
                <i class="icon icon-x"></i>
            </div>
            <div class="all-conditions">
                    Այլ պայմաններ
            </div>
        </div>
        <div class="popup-over-menu">
            <div class="close-warning">
                <i class="icon icon-x"></i>
            </div>
            <div class="menu-header">
                <i class="icon icon-left icon-scales"></i>
                Համեմատել
            </div>
        </div>
        <div class="colum-flex teleport">
            <div class="hamburger-wrap">
                <a id="hamburger-icon" href="#" title="Menu" >
                    <span class="line line-1"></span>
                    <span class="line line-2"></span>
                    <span class="lin line-3"></span>
                </a>
                <div class="mob-menu" style="display: none;">
                    <div class="listing-title">
                        <div class="left">
                            
                        </div>
                        <div class="right">
                            
                        </div>
                    </div>
                    <div class="open-menu-icon">
                        <div class="zoom-icon">
                            <a href="">
                                <img src="{{asset('/img/zoom1.svg')}}" alt="">
                            </a>
                            <a href="">
                                <img style="width: 60px;" src="{{asset('img/zoom2.svg')}}" alt="">
                            </a>
                        </div>
                    </div>
                </div>            
            </div>
            <div class="inform-link">
                {{--<a href="{{url('/how-to-use/')}}">--}}
                    {{--<i class="icon icon-left  icon-question"></i><span>Ինչպե՞ս օգտվել</span>--}}
                {{--</a>--}}
                <a></a>
                <a href="{{url('/about-website/')}}">Կայքի մասին</a>
            </div>
        </div>
        <div class="colum-flex">
                <a class="logo" href="{{url('/home')}}">
                    <div class="animet active">
                    </div>
                    <div class="logo-text">
                        <img src="{{asset('img/logo-text.png')}} ">
                    </div>
                </a>
            
        </div>
@php($belonging_id = 1)
        <div class="colum-flex">
            <div class="add-button">
                <button class="btn btn-white">
                    <i class="icon icon-left icon-scales"></i>
                    <span>Համեմատել</span>
                    {{--<div class="messeng-indicator self-messeng-indicator">--}}
                        {{--{{$getCompareInfo[$belonging_id]["count"]}}--}}
                    {{--</div>--}}
                    <div class="wrapper popup-menu compare-info-popup-menu">
                        <div class="piramid"></div>

                        @foreach($getCompareInfo as $getCompareInfoCurr)
                            @php($curr_belonging_id =   $getCompareInfoCurr["id"])

                            <a data-belonging-id="{{$curr_belonging_id}}" href="{{url($belongings_all->find($curr_belonging_id)->productsByBelongingInfo->first()->compare_inner_url)}}"
                               class="listing-title" style="display: {{$getCompareInfoCurr['display']}}">
                                <div class="left">
                                    <section>
                                        {{$getCompareInfoCurr["name"]}}
                                    </section>
                                </div>
                                <div class="right">
                                    <section>
                                        <div class="messeng-indicator @if($getCompareInfoCurr["id"] == $belonging_id) self-messeng-indicator @endif">
                                            {{$getCompareInfoCurr["count"]}}
                                        </div>
                                    </section>
                                </div>
                            </a>
                        @endforeach

                    </div>
                </button>
                {{--<a href="" class="btn btn-blue">--}}
                {{--<i class="icon icon-user"></i>--}}
                {{--<span>Մուտք</span>--}}
                {{--</a>--}}
            </div>
        </div>
        {{--<div class="colum-flex">--}}
            {{--<div class="add-button">--}}
                {{--<button class="btn btn-white">--}}
                    {{--<i class="icon icon-left icon-scales"></i>--}}
                    {{--<span>--}}
                        {{--Համեմատել--}}
                    {{--</span>--}}
                    {{--<div class="messeng-indicator">--}}
                        {{--6--}}
                    {{--</div>--}}
                    {{--<div class="wrapper popup-menu">--}}
                        {{--<div class="piramid"></div>--}}
                        {{--<a href="" class="listing-title">--}}
                            {{--<div class="left">--}}
                                {{--<section>--}}
                                    {{--Վարկեր--}}
                                {{--</section>--}}
                            {{--</div>--}}
                            {{--<div class="right">--}}
                                {{--<section>--}}
                                    {{--<div class="messeng-indicator">--}}
                                        {{--6--}}
                                    {{--</div>--}}
                                {{--</section>--}}
                            {{--</div>--}}
                        {{--</a>--}}
                        {{--<a href="" class="listing-title">--}}
                            {{--<div class="left">--}}
                                {{--<section>--}}
                                    {{--Ապահովագրություն--}}
                                {{--</section>--}}
                            {{--</div>--}}
                            {{--<div class="right">--}}
                                {{--<section>--}}
                                    {{--<div class="messeng-indicator">--}}
                                        {{--6--}}
                                    {{--</div>--}}
                                {{--</section>--}}
                            {{--</div>--}}
                        {{--</a>--}}
                        {{--<a href="" class="listing-title">--}}
                            {{--<div class="left">--}}
                                {{--<section>--}}
                                    {{--Ապահո--}}
                                {{--</section>--}}
                            {{--</div>--}}
                            {{--<div class="right">--}}
                                {{--<section>--}}
                                    {{--<div class="messeng-indicator">--}}
                                        {{--6--}}
                                    {{--</div>--}}
                                {{--</section>--}}
                            {{--</div>--}}
                        {{--</a>--}}
                        {{--<a href="" class="listing-title">--}}
                            {{--<div class="left">--}}
                                {{--<section>--}}
                                    {{--Արություն--}}
                                {{--</section>--}}
                            {{--</div>--}}
                            {{--<div class="right">--}}
                                {{--<section>--}}
                                    {{--<div class="messeng-indicator">--}}
                                        {{--6--}}
                                    {{--</div>--}}
                                {{--</section>--}}
                            {{--</div>--}}
                        {{--</a>--}}
                    {{--</div>--}}
                {{--</button>--}}
                {{--<a href="" class="btn btn-blue"> --}}
                    {{--<i class="icon icon-user"></i>--}}
                    {{--<span>--}}
                            {{--Մուտք--}}
                    {{--</span>            --}}
                {{--</a>--}}
            {{--</div>--}}
        {{--</div>  --}}
</header>