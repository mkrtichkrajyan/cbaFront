@extends('layouts.default')

@include('layouts.head')

@include('layouts.header')

<main>
    <div class="gradient-panel pined-absolute pined-abolute--top-fluid">
    <div class="z-lines-picture" style="background: url({{asset('img/z-lines.png')}}) center/cover no-repeat"></div>
</div>

<div class="visible-index">
    <div class="row align-center">
        <div class="column small-12 medium-10 large-9">
            <div class="title text-center">
                <div class="h1">
                    Կայքի քարտեզ
                </div>
            </div>
        </div>
    </div>

    <div class="row align-center">
        <div class="column small-12 medium-6 large-5">
            <div class="custom-card box-spaced">
                <div class="title">
                    <div class="h4 uppercase no-spaces">
                        Title
                    </div>
                </div>

                <ul class="sitemap">
                    <li>
                        <a href="">
                            Title 1
                        </a>
                    </li>
                    <li>
                        <a href="">
                            Title 1
                        </a>
                        <ul>
                            <li>
                                <a href="">
                                    Title 2
                                </a>
                            </li>
                            <li>
                                <a href="">
                                    Title 2
                                </a>
                            </li>
                            <li>
                                <a href="">
                                    Title 2
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li>
                        <a href="">
                            Title 1
                        </a>
                        <ul>
                            <li>
                                <a href="">
                                    Title 2
                                </a>
                            </li>
                            <li>
                                <a href="">
                                    Title 2
                                </a>
                            </li>
                            <li>
                                <a href="">
                                    Title 2
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li>
                        <a href="">
                            Title 1
                        </a>
                        <ul>
                            <li>
                                <a href="">
                                    Title 2
                                </a>
                            </li>
                            <li>
                                <a href="">
                                    Title 2
                                </a>
                            </li>
                            <li>
                                <a href="">
                                    Title 2
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
        <div class="column small-12 medium-6 large-5">
            <div class="custom-card box-spaced">
                <div class="title">
                    <div class="h4 uppercase no-spaces">
                        Title
                    </div>
                </div>

                <ul class="sitemap">
                    <li>
                        <a href="">
                            Title 1
                        </a>
                    </li>
                    <li>
                        <a href="">
                            Title 1
                        </a>
                    </li>
                    <li>
                        <a href="">
                            Title 1
                        </a>
                        <ul>
                            <li>
                                <a href="">
                                    Title 2
                                </a>
                            </li>
                            <li>
                                <a href="">
                                    Title 2
                                </a>
                            </li>
                            <li>
                                <a href="">
                                    Title 2
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li>
                        <a href="">
                            Title 1
                        </a>
                        <ul>
                            <li>
                                <a href="">
                                    Title 2
                                </a>
                            </li>
                            <li>
                                <a href="">
                                    Title 2
                                </a>
                            </li>
                            <li>
                                <a href="">
                                    Title 2
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li>
                        <a href="">
                            Title 1
                        </a>
                        <ul>
                            <li>
                                <a href="">
                                    Title 2
                                </a>
                            </li>
                            <li>
                                <a href="">
                                    Title 2
                                </a>
                            </li>
                            <li>
                                <a href="">
                                    Title 2
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
</main>

@include('layouts.footer')