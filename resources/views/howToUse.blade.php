@extends('layouts.default')

@include('layouts.head')

@include('layouts.header')

<main>
    <div class="gradient-panel pined-absolute pined-abolute--top-fluid">
        <div class="z-lines-picture" style="background: url({{asset('img/z-lines.png')}}) center/cover no-repeat"></div>
    </div>

    <div class="visible-index">
        <div class="row align-center">
            <div class="column small-12 medium-10 large-8">
                <div class="title text-center">
                    <div class="h1">
                        {{$how_to_use->title}}
                    </div>
                </div>

                @if($how_to_use->image)
                    <div class="image-shadow-container">
                        <img src="{{backend_asset('savedImages/contentManagement/how_to_use/'.$how_to_use->image)}}"
                             alt="">

                    </div>
                @endif

                <div class="content_richtext about_website_content about_content">
                    {!! $how_to_use->content !!}
                </div>

            </div>
        </div>
    </div>
</main>

@include('layouts.footer')