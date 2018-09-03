<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExtraController extends Controller
{
    /**
     * Show the application contacts page.
     *
     * @return \Illuminate\Http\Response
     */
    public function contacts()
    {
        return view('contact');
    }

    /**
     * Show the application about-us page.
     *
     * @return \Illuminate\Http\Response
     */
    public function aboutUs()
    {
        return view('about');
    }

    /**
     * Show the application sitemap page.
     *
     * @return \Illuminate\Http\Response
     */
    public function sitemap()
    {
        return view('sitemap');
    }
}
