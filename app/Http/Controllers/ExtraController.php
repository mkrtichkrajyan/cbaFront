<?php

namespace App\Http\Controllers;

use App\Models\AboutUs;
use App\Models\AboutWebsite;
use App\Models\Belonging;
use App\Models\ContactUs;
use App\Models\HowToUse;
use App\Models\LoansInformMsg;
use App\Models\Social;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class ExtraController extends MainController
{

    public function __construct()
    {
        $baseline_person_img = "img/baseline-person.svg";

        $main_loans_inform_msg = LoansInformMsg::where('id', 1)->first()->content;

        $share_title = "Ֆինանսական տեղեկատու համակարգ";

        $socials = Social::first();

        View::share('baseline_person_img', $baseline_person_img);

        View::share('main_loans_inform_msg', $main_loans_inform_msg);

        View::share('share_title', $share_title);

        View::share('socials', $socials);
    }


    /**
     * Show the application contacts page.
     *
     * @return \Illuminate\Http\Response
     */
    public function contacts()
    {
        $contact_us =   ContactUs::first();

        $belongings_all = Belonging::where('id', '>', 0)->with('productsByBelongingInfo')->get();

        $getCompareInfo = $this->getCompareInfoGlobal();

        return view('contact', ["contact_us" => $contact_us,"belongings_all" => $belongings_all, "getCompareInfo" => $getCompareInfo]);
    }

    /**
     * Show the application about-us page.
     *
     * @return \Illuminate\Http\Response
     */
    public function aboutUs()
    {
        $about_us = AboutUs::first();

        $belongings_all = Belonging::where('id', '>', 0)->with('productsByBelongingInfo')->get();

        $getCompareInfo = $this->getCompareInfoGlobal();

        return view('about', ["about_us" => $about_us,"belongings_all" => $belongings_all, "getCompareInfo" => $getCompareInfo]);
    }

    /**
     * Show the application about-website page.
     *
     * @return \Illuminate\Http\Response
     */
    public function aboutWebsite()
    {
        $about_website = AboutWebsite::first();

        $belongings_all = Belonging::where('id', '>', 0)->with('productsByBelongingInfo')->get();

        $getCompareInfo = $this->getCompareInfoGlobal();

        return view('aboutWebsite', ["about_website" => $about_website,"belongings_all" => $belongings_all, "getCompareInfo" => $getCompareInfo]);
    }

    /**
     * Show the application how-to-use page.
     *
     * @return \Illuminate\Http\Response
     */
    public function howToUse()
    {
        $how_to_use = HowToUse::first();

        $belongings_all = Belonging::where('id', '>', 0)->with('productsByBelongingInfo')->get();

        $getCompareInfo = $this->getCompareInfoGlobal();

        return view('howToUse', ["how_to_use" => $how_to_use,"belongings_all" => $belongings_all, "getCompareInfo" => $getCompareInfo]);
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
