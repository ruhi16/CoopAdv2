<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLoanSchemeFeatureValueTypeToEc02LoanSchemeDetailsTable extends Migration
{
    
    public function up()
    {
        Schema::table('ec02_loan_scheme_details', function (Blueprint $table) {
            $table->string('loan_scheme_feature_value_type')->nullable()->after('loan_scheme_feature_type');
        });
    }

    
    public function down()
    {
        Schema::table('ec02_loan_scheme_details', function (Blueprint $table) {
            $table->dropColumn('loan_scheme_feature_value_type');
        });
    }
}
