<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSomeIndexForProductsVariationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products_variations', function (Blueprint $table) {
            $table->index(['product_id', 'belonging_id'], 'index_product_id_belonging_id');
            $table->index(['percentage_type', 'providing_type', 'repayment_type'], 'index_percentage_type_providing_type_repayment_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products_variations', function (Blueprint $table) {
            $table->dropIndex('index_product_id_belonging_id');
            $table->dropIndex('index_percentage_type_providing_type_repayment_type');
        });
    }
}
