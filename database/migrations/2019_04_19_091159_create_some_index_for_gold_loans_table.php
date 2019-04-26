<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSomeIndexForGoldLoansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('gold_loans', function (Blueprint $table) {
            $table->index(['status', 'company_id'], 'index_status_company_id');
            $table->index('gold_pledge_type', 'index_gold_pledge_type');
            $table->index(['loan_amount_from', 'loan_amount_to'], 'index_loan_amount_from_to');
            $table->index(['loan_term_to_in_days', 'loan_term_from_in_days'], 'index_loan_term_from_to_in_days');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('gold_loans', function (Blueprint $table) {
            $table->dropIndex('index_status_company_id');
            $table->dropIndex('index_gold_pledge_type');
            $table->dropIndex('index_loan_amount_from_to');
            $table->dropIndex('index_loan_term_from_to_in_days');
        });
    }
}
