<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEc12LoanPaymentDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ec12_loan_payment_details', function (Blueprint $table) {
            $table->id()->autoIncrement()->unsigned()->comment('Primary Key');
            $table->integer('loan_payment_id')->nullable();
            $table->integer('loan_emi_schedule_id')->nullable();
            $table->double('loan_assign_detail_amount', 15, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ec12_loan_payment_details');
    }
}
