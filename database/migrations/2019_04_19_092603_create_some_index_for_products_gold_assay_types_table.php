<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSomeIndexForProductsGoldAssayTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products_gold_assay_types', function (Blueprint $table) {
            $table->index(['product_id', 'belonging_id', 'gold_assay_type_id'], 'index_product_id_belonging_id_gold_assay_type_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products_gold_assay_types', function (Blueprint $table) {
            $table->dropIndex('index_product_id_belonging_id_gold_assay_type_id');
        });
    }
}
