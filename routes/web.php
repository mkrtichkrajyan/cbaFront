<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Route::get('/', function () {
//    return view('welcome');
//});

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/loans', 'HomeController@loans')->name('loans');

Route::get('', 'HomeController@index')->name('home');

/* products compare */
foreach (\App\Models\ProductByBelongingsView::all() as $products_by_belongings_views) {

    \Illuminate\Support\Facades\Route::any($products_by_belongings_views->compare_url, 'HomeController@' . $products_by_belongings_views->compare_action);
}
/* products compare */

/* compare inner page */
foreach (\App\Models\ProductByBelongingsView::all() as $products_by_belongings_views) {

    \Illuminate\Support\Facades\Route::any($products_by_belongings_views->compare_inner_url, 'HomeController@' . $products_by_belongings_views->compare_inner_action);
}
/* compare inner page */


Route::get('/company-branches-and-bankomats/{company_id}', 'CompaniesController@companyBranchesBankomats');

Route::post('/export-branches-list/{id}', 'CompaniesController@downloadBranches');

Route::post('/export-bankomats-list/{id}', 'CompaniesController@downloadBankomats');

/*filters*/
Route::any('/car-loans-filters/', 'HomeController@carLoansFilters');

Route::any('/gold-loans-filters/', 'HomeController@goldLoansFilters');

Route::any('/student-loans-filters/', 'HomeController@studentLoansFilters');

Route::any('/agric-loans-filters/', 'HomeController@agricLoansFilters');

Route::any('/consumer-loans-filters/', 'HomeController@consumerLoanFilters');

Route::any('/online-loans-filters/', 'HomeController@onlineLoansFilters');

Route::any('/loan-refinancing-filters/', 'HomeController@loanRefinancingFilters');

Route::any('/credit-loans-filters/', 'HomeController@creditLoanFilters');

Route::any('/mortgage-loans-filters/', 'HomeController@mortgageLoanFilters');


Route::any('/travel-insurances-filters/', 'HomeController@travelInsurancesFilters');



/*filters*/


Route::get('/contacts/', 'ExtraController@contacts');

Route::get('/about-us/', 'ExtraController@aboutUs');

Route::get('/about-website/', 'ExtraController@aboutWebsite');

Route::get('/how-to-use/', 'ExtraController@howToUse');

Route::get('/site-map/', 'ExtraController@sitemap');


Route::get('/car-loan-product/{unique_options}/{cost}/{prepayment}/{time_type}/{term}', 'HomeController@carLoanProduct');

Route::get('/credit-loan-product/{unique_options}/{cost}/{prepayment}/{time_type}/{term}', 'HomeController@creditLoanProduct');

Route::get('/gold-loan-product/{unique_options}/{loan_amount}/{time_type}/{term}', 'HomeController@goldLoanProduct');

Route::get('/student-loan-product/{unique_options}/{loan_amount}/{time_type}/{term}', 'HomeController@studentLoanProduct');

Route::get('/agric-loan-product/{unique_options}/{loan_amount}/{time_type}/{term}', 'HomeController@agricLoanProduct');

Route::get('/mortgage-loan-product/{unique_options}/{cost}/{prepayment}/{time_type}/{term}', 'HomeController@mortgageLoanProduct');

Route::get('/online-loan-product/{unique_options}/{loan_amount}/{time_type}/{term}', 'HomeController@onlineLoanProduct');

Route::get('/loan-refinancing-product/{unique_options}/{loan_amount}/{time_type}/{term}', 'HomeController@loanRefinancingProduct');

Route::get('/consumer-loan-product/{unique_options}/{loan_amount}/{time_type}/{term}', 'HomeController@consumerLoanProduct');


Route::get('/travel-insurance-product/{product_id}/{age}/{term}/{currency}/{country}', 'HomeController@travelInsuranceLoanProduct');

