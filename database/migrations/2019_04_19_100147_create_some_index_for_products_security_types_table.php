<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSomeIndexForProductsSecurityTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products_security_types', function (Blueprint $table) {
            $table->index(['product_id', 'belonging_id', 'security_type'], 'index_product_id_belonging_id_security_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products_security_types', function (Blueprint $table) {
            $table->dropIndex('index_product_id_belonging_id_security_type');
        });
    }
}
