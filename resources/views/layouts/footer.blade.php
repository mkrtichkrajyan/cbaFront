<footer>
    <div class="row align-middle align-justify">
        <div class="address">
            <div class="address_item">
                <div class="address_icon"><img src="https://abcfinance.am/resources/abcfinance/images/136.png">
                </div>
                <div class="address_text"> ՀՀ, 0010, ք. Երևան, Վ.Սարգսյան 6</div>
                <div class="clearfix"></div>
            </div>
            <div class="address_item">
                <div class="address_icon"><img src="https://abcfinance.am/resources/abcfinance/images/139.png">
                </div>
                <div class="address_text"> +(37410) 592 697</div>
                <div class="clearfix"></div>
            </div>
            <div class="address_item">
                <div class="address_icon"><img src="https://abcfinance.am/resources/abcfinance/images/138.png">
                </div>
                <div class="address_text"><span class="email" data-user="ofniremusnoc" data-website="ma.abc"></span>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>

    <div class="row align-middle align-justify">


        <div class="columns  large-2 medium-2 small-12">
            <div class="footer-logo">
                <a href="">
                    <img src="{{asset('img/footer-logo.svg')}}" alt="">
                </a>
            </div>
        </div>
        <div class="columns  large-8 medium-7 small-12">
            <div class="footer-menu">
                <a href="{{url('/about-us')}}">Մեր մասին</a>

                <a href="{{url('/site-map')}}">Կայքի քարտեզ </a>

                <a href="{{url('/contacts')}}">Հետադարձ կապ</a>
            </div>
        </div>
        <div class="columns  large-2 medium-3 small-12">
            <div class="footer-soc-icon">
                <a href="{{$socials->facebook_link}}" target="_blank">
                    <i class="icon icon-fb"></i>
                </a>
                <a href="{{$socials->instagramm_link}}" target="_blank">
                    <i class="icon icon-in"></i>
                </a>
                <a href="{{$socials->twitter_link}}" target="_blank">
                    <i class="icon icon-tw"></i>
                </a>
                <a href="{{$socials->youtube_link}}" target="_blank">
                    <i class="icon icon-you"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="row align-middle align-justify bord">
        <div class="columns  large-2 medium-3 small-12">
            <div class="zoom-icon">
                <a href="">
                    <img src="{{asset('img/zoom1.svg')}}" alt="">
                </a>
                <a href="https://abcfinance.am/">
                    <img style="width: 60px;" src="{{asset('img/zoom2.svg')}}" alt="">
                </a>
            </div>
        </div>
        <div class="columns  large-8 medium-7 small-12">
            <div class="All-rights-reserved">
                © {{date("Y")}} Central Bank of Armenia.  Բոլոր իրավունքները պաշտպանված են
                {{--© 2018 Central Bank of Armenia. All rights reserved.--}}
            </div>
        </div>
        <div class="columns  large-2 medium-2 small-12">
            <div class="zoom-logo">
                <a href="http://www.zoom.am/">
                    <img src="{{asset('/img/zoom.svg')}}" alt="">
                </a>
            </div>
        </div>
    </div>
</footer>

</body>
</html>