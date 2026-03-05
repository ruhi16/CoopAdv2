<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRoiToEc05LoanAssignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ec05_loan_assigns', function (Blueprint $table) {
            $table->decimal('roi', 10, 2)->nullable()->after('loan_current_balance');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ec05_loan_assigns', function (Blueprint $table) {
            $table->dropColumn('roi');
        });
    }
}
