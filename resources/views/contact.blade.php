@extends('layouts.default')

@include('layouts.head')

@include('layouts.header')

<main>

    <div class="gradient-panel pined-absolute pined-abolute--top-fluid">
        <div class="z-lines-picture" style="background: url({{asset('img/z-lines.png')}}) center/cover no-repeat"></div>
    </div>

    <div class="visible-index">
        <div class="row align-center">
            <div class="column small-12">
                <div class="title text-center">
                    <div class="h1">
                        Կապ մեզ հետ
                    </div>
                </div>
            </div>

            <div class="column small-12 large-3">
                <div class="custom-card inherit-height">
                    <ul class="contacts">
                        <li>
                            <i class="icon icon-location"></i>
                            <span>
                            ՀՀ Կենտրոնական բանկ  Վազգեն Սարգսյան.6
                        </span>
                        </li>
                        <li>
                            <i class="icon icon-phone"></i>
                            <span>
                            +374 10 592-697
                        </span>
                        </li>
                        <li>
                            <i class="icon icon-time"></i>
                            <span>
                            Բացում երկուշաբթի, 09:30
                        </span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="column small-12 large-9">
                <div class="map_wrapper">
                    <div id="contact_map" class="fluid-both"></div>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCJe2qrOv4a7WXyhj4-0KjRzv2WpMKAmH8&callback=initContactMap"
        async defer></script>

<script>
    var map;

    function initContactMap() {
        map = new google.maps.Map(document.getElementById('contact_map'), {
            center: {lat: 40.176013, lng: 44.510348},
            zoom: 17,
            disableDefaultUI: true,
            styles: mapStyles
        });

        var mapStyles = [{"elementType": "geometry", "stylers": [{"color": "#f5f5f5"}]}, {
            "elementType": "labels.icon",
            "stylers": [{"visibility": "off"}]
        }, {"elementType": "labels.text.fill", "stylers": [{"color": "#616161"}]}, {
            "elementType": "labels.text.stroke",
            "stylers": [{"color": "#f5f5f5"}]
        }, {
            "featureType": "administrative.land_parcel",
            "elementType": "labels.text.fill",
            "stylers": [{"color": "#bdbdbd"}]
        }, {"featureType": "poi", "elementType": "geometry", "stylers": [{"color": "#eeeeee"}]}, {
            "featureType": "poi",
            "elementType": "labels.text.fill",
            "stylers": [{"color": "#757575"}]
        }, {
            "featureType": "poi.park",
            "elementType": "geometry",
            "stylers": [{"color": "#e5e5e5"}]
        }, {
            "featureType": "poi.park",
            "elementType": "labels.text.fill",
            "stylers": [{"color": "#9e9e9e"}]
        }, {
            "featureType": "road",
            "elementType": "geometry",
            "stylers": [{"color": "#ffffff"}]
        }, {
            "featureType": "road.arterial",
            "elementType": "labels.text.fill",
            "stylers": [{"color": "#757575"}]
        }, {
            "featureType": "road.highway",
            "elementType": "geometry",
            "stylers": [{"color": "#dadada"}]
        }, {
            "featureType": "road.highway",
            "elementType": "labels.text.fill",
            "stylers": [{"color": "#616161"}]
        }, {
            "featureType": "road.local",
            "elementType": "labels.text.fill",
            "stylers": [{"color": "#9e9e9e"}]
        }, {
            "featureType": "transit.line",
            "elementType": "geometry",
            "stylers": [{"color": "#e5e5e5"}]
        }, {
            "featureType": "transit.station",
            "elementType": "geometry",
            "stylers": [{"color": "#eeeeee"}]
        }, {
            "featureType": "water",
            "elementType": "geometry",
            "stylers": [{"color": "#c9c9c9"}]
        }, {"featureType": "water", "elementType": "labels.text.fill", "stylers": [{"color": "#9e9e9e"}]}];

        var markers = [
            ['', 40.176013, 44.510348]
        ];

        var pinIcon = '{{asset("img/pin.png")}}';

        for (i = 0; i < markers.length; i++) {
            var position = new google.maps.LatLng(markers[i][1], markers[i][2]);

            marker = new google.maps.Marker({
                position: position,
                map: map,
                title: markers[i][0],
                optimized: false,
                icon: pinIcon
            });
        }
    }
</script>

@include('layouts.footer')