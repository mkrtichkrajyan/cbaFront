<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSomeIndexForTravelInsurancesVariationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::table('travel_insurances_variations', function (Blueprint $table) {
             $table->index(['product_id', 'belonging_id'], 'index_product_id_belonging_id');
             $table->index(['travel_insurance_term_from', 'travel_insurance_term_to'], 'index_travel_insurance_term_from_to');
             $table->index(['travel_age_from', 'travel_age_to'], 'index_travel_age_from_to');
         });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('travel_insurances_variations', function (Blueprint $table) {
            $table->dropIndex('index_product_id_belonging_id');
            $table->dropIndex('index_travel_insurance_term_from_to');
            $table->dropIndex('index_travel_age_from_to');
        });
    }
}
