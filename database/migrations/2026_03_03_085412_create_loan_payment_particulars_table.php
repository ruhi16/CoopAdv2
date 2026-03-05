<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoanPaymentParticularsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_payment_particulars', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('loan_task_payment_id')->nullable();
            
            $table->integer('loan_assign_id')->nullable();
            $table->integer('loan_scheme_detail_id')->nullable();
            $table->integer('loan_scheme_detail_feature_id')->nullable();
            $table->double('loan_scheme_detail_feature_value', 15, 2)->nullable();
            $table->string('loan_scheme_detail_feature_condition')->nullable();
            
            $table->integer('loan_emi_schedule_id')->nullable();
            $table->integer('loan_emi_schedule_index')->nullable();
            $table->double('emi_amount', 15, 2)->nullable();
            $table->double('emi_principal_amount', 15, 2)->nullable();
            $table->double('emi_interest_amount', 15, 2)->nullable();


            $table->string('name')->nullable();
            $table->string('description')->nullable();
            $table->integer('order_index')->default(0);

            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('created_by')->default(0);
            $table->integer('approved_by')->default(0);
            $table->integer('school_id')->default(0);
            $table->string('remarks')->nullable();
            $table->string('status')->nullable(); 
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
        Schema::dropIfExists('loan_payment_particulars');
    }
}
