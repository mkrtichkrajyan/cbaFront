@extends('layouts.default')

@include('layouts.head')

@include('layouts.header')

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBhD9iQdql7V51GX7K_qZdn8J9FAFozFbI&callback=initMap" async defer></script>

<main>
    <div class="back-fon" style="background-image: url({{asset('img/blue-fon.png')}});height:10px;">

    </div>
    <div class="row">
        <form id="branchesOrAtmsExporting" data-bankomat-action="{{ url('/export-bankomats-list/'.$company->id) }}"
              data-branch-action="{{ url('/export-branches-list/'.$company->id) }}" action="" method="post">

            {!! csrf_field() !!}
        </form>
        <div class="columns large-12 medium-12 small-12">
            <div class="listing-title bank-listing">
                <div class="left">
                    <a href="" class="come-back title-map">
                        <div class="come-back-icon">
                            <i class="icon icon-back-arrrow"></i>
                        </div>
                        <span>{{$company->name}}</span>
                    </a>
                </div>
                <div class="right">
                    <div class="listing-icon">
                        <img class="company_icon" src="{{ backend_asset('savedImages/'.$company->image )}}" alt="">
                    </div>
                </div>
            </div>
        </div>
        <div class="columns large-3 medium-12 small-12">
            <div class="wrapper bank-informetion-wrapper">
                <a  class="bank-informetion">
                    <i class="icon icon-pin"></i>
                    <span>
                        {{$company->address}}
                    </span>
                </a>
                <div class="bank-informetion">
                    <i class="icon  icon-phone"></i>
                    <span>
                        {{$company->phone_number}}

                        @if(strlen($company->phone_number_2) > 0)
                            ,{{$company->phone_number_2}}
                        @endif
                    </span>
                </div>
                <div class="bank-informetion">
                    <i class="icon icon-time"></i>
                    <div class="left hide-info">

                        @if($company->around_the_clock_working == 1)
                            Շուրջօրյա աշխատանք
                        @else
                            @if(count(array_unique([$company->mondayWorkStartTime, $company->tuesdayWorkStartTime,$company->wednesdayWorkStartTime,
                             $company->thursdayWorkStartTime,$company->fridayWorkStartTime])) == 1 && strlen(trim($company->mondayWorkStartTime)) > 0)

                                Երկ. - Ուրբ.  {{$company->mondayWorkStartTime}} -{{$company->mondayWorkEndTime}}
                            @else
                                @if($company->mondayWorkStartTime != $company->mondayWorkEndTime && strlen(trim($company->mondayWorkStartTime)) > 0)
                                    Երկուշաբթի - {{$company->mondayWorkStartTime}}
                                    – {{$company->mondayWorkEndTime}} </br>
                                @endif

                                @if($company->tuesdayWorkStartTime != $company->tuesdayWorkEndTime && strlen(trim($company->tuesdayWorkStartTime)) > 0)
                                    Երեքշաբթի - {{$company->tuesdayWorkStartTime}}
                                    – {{$company->tuesdayWorkEndTime}} </br>
                                @endif

                                @if($company->wednesdayWorkStartTime != $company->wednesdayWorkEndTime && strlen(trim($company->wednesdayWorkStartTime)) > 0)
                                    Չորեքշաբթի - {{$company->wednesdayWorkStartTime}}
                                    – {{$company->wednesdayWorkEndTime}}  </br>
                                @endif

                                @if($company->thursdayWorkStartTime != $company->thursdayWorkEndTime && strlen(trim($company->thursdayWorkStartTime)) > 0)
                                    Հինգշաբթի -{{$company->thursdayWorkStartTime}}
                                    – {{$company->thursdayWorkEndTime}}  </br>
                                @endif

                                @if($company->fridayWorkStartTime != $company->fridayWorkEndTime && strlen(trim($company->fridayWorkStartTime)) > 0)
                                    Ուրբաթ  -  {{$company->fridayWorkStartTime}}
                                    – {{$company->fridayWorkEndTime}} </br>
                                    @endif
                                    @endif

                                    @if($company->saturdayWorkStartTime != $company->saturdayWorkEndTime && strlen(trim($company->saturdayWorkStartTime)) > 0)
                                    </br>Շաբ.  {{$company->saturdayWorkStartTime}}- {{$company->saturdayWorkEndTime}}
                                    @endif

                                    @if($company->sundayWorkStartTime != $company->sundayWorkEndTime && strlen(trim($company->sundayWorkStartTime)) > 0)
                                    </br>Կիր.  {{$company->sundayWorkStartTime}} - {{$company->sundayWorkEndTime}}
                                @endif
                            @endif
                    </div>

                    {{--<span>--}}
                    {{--Բացում երկուշաբթի, 09:00--}}
                    {{--</span>--}}
                </div>
            </div>
        </div>
        <div class="columns large-9 medium-12 small-12">
            <div class="wrapper">
                <div class="listing-title bank-listing-map">
                    <div class="gradient">
                    </div>
                    <div class="left">
                        <div class="map-chenge chenge map-chenge-branches">
                            Մասնաճյուղեր
                        </div>
                        <div class="map-chenge chenge  map-chenge-bankomats">
                            Բանկոմատներ
                        </div>
                    </div>
                    <div class="right">
                        <div class="add-function">
                            <a id="exportBranchesAtmsList" >
                                <i class="icon icon-right  icon-download"></i>
                            </a>
                        </div>
                        <div class="listing-icon">
                            <a  href="?p=map" class="btn btn-text-blue">
                                <i class="chenge icon icon-pin">
                                </i>
                                <span>
                                    Ցուցակ
                                </span>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="change_item">
                    @if($company->companyBranches->count() > 0)
                        @foreach($company->companyBranches as $companyBranchCurr)
                            <div class="listing-title ATM_location_info">
                                <div class="left">
                                    <div class="adres">
                                        <div class="ATM_name">
                                            {{@$companyBranchCurr->cityInfo->name}}
                                        </div>
                                        <div class="ATM_location">
                                            <a href="">
                                                <i class="icon icon-pin"></i>   {{$companyBranchCurr->address}}
                                            </a>
                                        </div>
                                    </div>
                                    <div class="open-more-button">
                                        <i class="icon icon-arrow-down"></i>
                                    </div>
                                </div>
                                <div class="left hide-info">
                                    <div class="phone_number">
                                        <span>
                                            {{@$companyBranchCurr->phone_number}}
                                        </span>

                                        @if(strlen($company->phone_number_2) > 0)
                                            <span> {{@$companyBranchCurr->phone_number_2}}</span>
                                        @endif

                                    </div>
                                </div>
                                <div class="left hide-info">

                                    @if($companyBranchCurr->around_the_clock_working == 1)
                                        Շուրջօրյա աշխատանք
                                    @else
                                        @if(count(array_unique([$companyBranchCurr->mondayWorkStartTime, $companyBranchCurr->tuesdayWorkStartTime,
                                        $companyBranchCurr->wednesdayWorkStartTime, $companyBranchCurr->thursdayWorkStartTime,$companyBranchCurr->fridayWorkStartTime])) == 1 )

                                            Երկ. - Ուրբ.  {{$companyBranchCurr->mondayWorkStartTime}} -{{$companyBranchCurr->mondayWorkEndTime}}
                                        @else
                                            @if($companyBranchCurr->mondayWorkStartTime != $companyBranchCurr->mondayWorkEndTime && strlen(trim($companyBranchCurr->mondayWorkStartTime)) > 0)
                                                Երկուշաբթի - {{$companyBranchCurr->mondayWorkStartTime}} – {{$companyBranchCurr->mondayWorkEndTime}} </br>
                                            @endif

                                            @if($companyBranchCurr->tuesdayWorkStartTime != $companyBranchCurr->tuesdayWorkEndTime && strlen(trim($companyBranchCurr->tuesdayWorkStartTime)) > 0)
                                                Երեքշաբթի - {{$companyBranchCurr->tuesdayWorkStartTime}} – {{$companyBranchCurr->tuesdayWorkEndTime}} </br>
                                            @endif

                                            @if($companyBranchCurr->wednesdayWorkStartTime != $companyBranchCurr->wednesdayWorkEndTime && strlen(trim($companyBranchCurr->wednesdayWorkStartTime)) > 0)
                                                Չորեքշաբթի - {{$companyBranchCurr->wednesdayWorkStartTime}} – {{$companyBranchCurr->wednesdayWorkEndTime}}  </br>
                                            @endif

                                            @if($companyBranchCurr->thursdayWorkStartTime != $companyBranchCurr->thursdayWorkEndTime && strlen(trim($companyBranchCurr->thursdayWorkStartTime)) > 0)
                                                Հինգշաբթի -{{$companyBranchCurr->thursdayWorkStartTime}} – {{$companyBranchCurr->thursdayWorkEndTime}}  </br>
                                            @endif

                                            @if($companyBranchCurr->fridayWorkStartTime != $companyBranchCurr->fridayWorkEndTime && strlen(trim($companyBranchCurr->fridayWorkStartTime)) > 0)
                                                Ուրբաթ  -  {{$companyBranchCurr->fridayWorkStartTime}} – {{$companyBranchCurr->fridayWorkEndTime}} </br>
                                                @endif
                                                @endif

                                                @if($companyBranchCurr->saturdayWorkStartTime != $companyBranchCurr->saturdayWorkEndTime && strlen(trim($companyBranchCurr->saturdayWorkStartTime)) > 0)
                                                </br>Շաբ.  {{$companyBranchCurr->saturdayWorkStartTime}} - {{$companyBranchCurr->saturdayWorkEndTime}}
                                                @endif

                                                @if($companyBranchCurr->sundayWorkStartTime != $companyBranchCurr->sundayWorkEndTime && strlen(trim($companyBranchCurr->sundayWorkStartTime)) > 0)
                                                </br>Կիր.  {{$companyBranchCurr->sundayWorkStartTime}} - {{$companyBranchCurr->sundayWorkEndTime}}
                                            @endif
                                        @endif
                                    {{--<div class="work_day">--}}
                                        {{--<span>--}}
                                            {{--Երկ - Ուրբ--}}
                                        {{--</span>--}}
                                        {{--<span>--}}
                                            {{--Շաբ - Կիր--}}
                                        {{--</span>--}}
                                    {{--</div>--}}


                                    {{--<div class="work_time">--}}
                                        {{--<span>--}}
                                            {{--09:30-16:30--}}
                                        {{--</span>--}}
                                            {{--<span>--}}
                                            {{--10:00-3:00--}}
                                        {{--</span>--}}
                                    {{--</div>--}}
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
                <div class="change_item">
                    @if($company->companyBankomats->count() > 0)
                        @foreach($company->companyBankomats as $companyBankomatCurr)
                            <div class="listing-title ATM_location_info">
                                <div class="left adres">
                                    <div class="ATM_name">
                                        {{@$companyBankomatCurr->cityInfo->name}}
                                    </div>
                                </div>
                                <div class="left adres">
                                    <div class="ATM_location">
                                    <i class="icon icon-pin"></i>
                                    <a href="">
                                        {{$companyBankomatCurr->address}}
                                    </a>
                                </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</main>

@include('layouts.footer')