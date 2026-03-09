<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEc11LoanPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ec11_loan_payments', function (Blueprint $table) {
            $table->id();
            $table->integer('loan_assign_id');
            $table->integer('member_id');

                $table->integer('task_execution_id')->nullable();
                $table->integer('task_execution_detail_id')->nullable();

            // $table->integer('loan_emi_schedule_id');

            // $table->double('payment_principal_amount', 10, 2)->nullable();
            // $table->double('payment_interest_amount', 10, 2)->nullable();
            // $table->double('payment_total_amount', 10, 2)->nullable();

            // $table->double('regular_amount_total', 10, 2)->nullable();
            // $table->double('scheduled_amount_total', 10, 2)->nullable();
            
            $table->double('payment_total_amount', 10, 2)->nullable();

            $table->date('payment_date')->nullable();
            $table->enum('payment_method', ['cash', 'bank', 'upi', 'other'])->nullable();
            $table->boolean('is_paid')->default(false);


            $table->double('principal_balance_amount_after_payment')->nullable();
            $table->double('principal_balance_amount_before_payment')->nullable();

            $table->integer('financial_year_id')->nullable();


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
        Schema::dropIfExists('ec11_loan_payments');
    }
}
