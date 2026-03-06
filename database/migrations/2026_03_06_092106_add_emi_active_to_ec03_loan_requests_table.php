<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmiActiveToEc03LoanRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ec03_loan_requests', function (Blueprint $table) {
            $table->boolean('emi_active')->default(false)->after('no_of_years');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ec03_loan_requests', function (Blueprint $table) {
            //
        });
    }
}
