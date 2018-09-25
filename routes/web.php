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
foreach(\App\Models\ProductByBelongingsView::all() as $products_by_belongings_views){

    \Illuminate\Support\Facades\Route::any($products_by_belongings_views->compare_url,'HomeController@'.$products_by_belongings_views->compare_action);
}
/* products compare */

Route::get('/company-branches-and-bankomats/{company_id}', 'CompaniesController@companyBranchesBankomats');

Route::post('/export-branches-list/{id}', 'CompaniesController@downloadBranches');

Route::post('/export-bankomats-list/{id}', 'CompaniesController@downloadBankomats');


Route::any('/car-loans-filters/', 'HomeController@carLoansFilters');

Route::get('/contacts/', 'ExtraController@contacts');

Route::get('/about-us/', 'ExtraController@aboutUs');

Route::get('/about-website/', 'ExtraController@aboutWebsite');

Route::get('/how-to-use/', 'ExtraController@howToUse');

Route::get('/site-map/', 'ExtraController@sitemap');

Route::get('/createSelectBox', 'HomeController@createSelectBox');
