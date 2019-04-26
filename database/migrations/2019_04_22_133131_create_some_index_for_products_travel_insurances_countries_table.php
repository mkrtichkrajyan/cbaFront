<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSomeIndexForProductsTravelInsurancesCountriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products_travel_insurances_countries', function (Blueprint $table) {
            $table->index(['product_id', 'belonging_id','country_id'], 'index_product_id_belonging_id_country_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products_travel_insurances_countries', function (Blueprint $table) {
            $table->dropIndex('index_product_id_belonging_id_country_id');
        });
    }
}
