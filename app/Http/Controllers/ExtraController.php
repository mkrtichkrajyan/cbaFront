<?php

namespace App\Http\Controllers;

use App\Models\AboutUs;
use App\Models\AboutWebsite;
use App\Models\ContactUs;
use App\Models\HowToUse;
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
        $contact_us =   ContactUs::first();

        return view('contact', ["contact_us" => $contact_us]);
    }

    /**
     * Show the application about-us page.
     *
     * @return \Illuminate\Http\Response
     */
    public function aboutUs()
    {
        $about_us = AboutUs::first();

        return view('about', ["about_us" => $about_us]);
    }

    /**
     * Show the application about-website page.
     *
     * @return \Illuminate\Http\Response
     */
    public function aboutWebsite()
    {
        $about_website = AboutWebsite::first();

        return view('aboutWebsite', ["about_website" => $about_website]);
    }

    /**
     * Show the application how-to-use page.
     *
     * @return \Illuminate\Http\Response
     */
    public function howToUse()
    {
        $how_to_use = HowToUse::first();

        return view('howToUse', ["how_to_use" => $how_to_use]);
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
