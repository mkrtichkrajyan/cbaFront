@extends('layouts.default')

@include('layouts.head')

@include('layouts.header')


<main>
    <div class="back-fon" style="height:160px;">

    </div>
    <div class="row">
        <form id="branchesOrAtmsExporting" data-bankomat-action="{{ url('/export-bankomats-list/'.$company->id) }}"
              data-branch-action="{{ url('/export-branches-list/'.$company->id) }}" action="" method="post">
            {!! csrf_field() !!}

            <input type="hidden" id="branches_or_bankomats_for_user" value="branches">

            <input type="hidden" id="branch_or_bankomat_concret_one" value="0" data-type="" data-name="" data-address="">

        </form>
        <div class="columns large-12 medium-12 small-12">
            <div class="listing-title bank-listing">
                <div class="left">
                    <a href="" class="come-back come-back-from-company title-map">
                        <div class="come-back-icon">
                            <i class="icon icon-back-arrow"></i>
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
                            <a id="mapSwitcher" class="btn btn-text-blue">
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
                                            <a class="branch_bankomat_show_address" type="branch" lng="{{$companyBranchCurr->lng}}" lat="{{$companyBranchCurr->lat}}"
                                            data-name="{{$companyBranchCurr->name}}" data-address="{{$companyBranchCurr->address}}">
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
                                    <a class="branch_bankomat_show_address" type="bankomat">
                                        {{$companyBankomatCurr->address}}
                                    </a>
                                </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>


                <div id="changeItemMapBranchesAtms" class="change_item change_item_map">
                    <div class="map">
                        <div class="map" id="map">
                            {{--<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD5H-WvokqQ1ISSvnlMDT200DJcXTNSO7o&callback=initMap" async defer></script>--}}
                            <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBhD9iQdql7V51GX7K_qZdn8J9FAFozFbI&callback=initMap" async defer></script>

                            <script>
                                var map;
                                var markers = [];

                                function initMap() {
                                    markerIcon  =   "{{asset('img/marker.svg')}}";

                                    if($("#branch_or_bankomat_concret_one").val() == 0){

                                        if(localStorage.getItem('branches_or_bankomats_for_user') == "bankomat"){

                                            var markers = [
                                                    @foreach($company->companyBankomats as $currBankomat)
                                                {
                                                    lat: "{{@$currBankomat->lat}}",    // Широта
                                                    lng: "{{@$currBankomat->lng}}",    // Долгота
                                                    name: "{{@$currBankomat->name}}", // Произвольное название, которое будем выводить в информационном окне
                                                    address: "{{@$currBankomat->address}}"   // Адрес, который также будем выводить в информационном окне
                                                },
                                                @endforeach
                                            ];
                                        }

                                        else{
                                            var markers = [
                                                    @foreach($company->companyBranches as $currBranch)
                                                {
                                                    lat: "{{@$currBranch->lat}}",    // Широта
                                                    lng: "{{@$currBranch->lng}}",    // Долгота
                                                    name: "{{@$currBranch->name}}", // Произвольное название, которое будем выводить в информационном окне
                                                    address: "{{@$currBranch->address}}"   // Адрес, который также будем выводить в информационном окне
                                                },
                                                @endforeach
                                            ];
                                        }
                                    }
                                    else{
                                        var markers = [
                                            {
                                                lat: $("#branch_or_bankomat_concret_one").attr('lat'),    // Широта
                                                lng: $("#branch_or_bankomat_concret_one").attr('lng'),    // Долгота
                                                name: $("#branch_or_bankomat_concret_one").attr('data-name'), // Произвольное название, которое будем выводить в информационном окне
                                                address: $("#branch_or_bankomat_concret_one").attr('data-address'),   // Адрес, который также будем выводить в информационном окне
                                            }
                                        ];
                                    }

                                    var centerLatLng = new google.maps.LatLng(40.182094, 44.512711);

                                    var mapOptions = {
                                        center: centerLatLng,
                                        zoom: 12,
                                        mapTypeId: 'terrain',
                                    };

                                    map = new google.maps.Map(document.getElementById("map"), mapOptions);

                                    infoWindow = new google.maps.InfoWindow();
                                    // Отслеживаем клик в любом месте карты
                                    google.maps.event.addListener(map, "click", function() {
                                        // infoWindow.close - закрываем информационное окно.
                                        infoWindow.close();
                                    });
                                    clearMarkers();

                                    for (var i = 0; i < markers.length; i++){
                                        var latLng = new google.maps.LatLng(markers[i].lat, markers[i].lng);
                                        var name = markers[i].name;
                                        var address = markers[i].address;
                                        // Добавляем маркер с информационным окном
                                        addMarker(latLng, name, address);
                                    }
                                    // Перебираем в цикле все координата хранящиеся в markersData
                                }

                                // Adds a marker to the map and push to the array.
                                function addMarker(latLng, name, address) {
                                    var marker = new google.maps.Marker({
                                        position: latLng,
                                        map: map,
                                        title: name,
                                        icon: markerIcon
                                    });
                                    // Отслеживаем клик по нашему маркеру
                                    google.maps.event.addListener(marker, "click", function() {
                                        // contentString - это переменная в которой хранится содержимое информационного окна.
                                        var contentString = '<div class="infowindow">' +
                                            '<h3>' + name + '</h3>' +
                                            '<p>' + address + '</p>' +
                                            '</div>';
                                        // Меняем содержимое информационного окна
                                        infoWindow.setContent(contentString);
                                        // Показываем информационное окно
                                        infoWindow.open(map, marker);
                                    });
                                }

                                // Sets the map on all markers in the array.
                                function setMapOnAll(map) {
                                    for (var i = 0; i < markers.length; i++) {
                                        markers[i].setMap(map);
                                    }
                                }

                                // Removes the markers from the map, but keeps them in the array.
                                function clearMarkers() {
                                    setMapOnAll(null);
                                }

                                // Shows any markers currently in the array.
                                function showMarkers() {
                                    setMapOnAll(map);
                                }

                                // Deletes all markers in the array by removing references to them.
                                function deleteMarkers() {
                                    clearMarkers();
                                    markers = [];
                                }
                            </script>
                         </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@include('layouts.footer')