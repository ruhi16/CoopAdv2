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
            // $table->integer('loan_assign_id')->nullable();

            $table->integer('task_execution_id')->nullable();
            $table->integer('task_execution_detail_id')->nullable();

            // $table->double('loan_assign_current_balance_before_payment', 10, 2)->nullable()->default(0);
            $table->integer('loan_assign_detail_id')->nullable();
            $table->integer('loan_emi_schedule_id')->nullable();
            $table->double('loan_assign_detail_amount', 10, 2)->nullable();

            // $table->double('loan_assign_current_balance_after_payment', 10, 2)->nullable()->default(0);



            $table->boolean('is_scheduled')->default(false);  // scheduled = true or regular = null or false
            $table->boolean('is_fixed_amount')->default(false);  
            
            $table->enum('status', ['initial', 'processed', 'cancelled', 'pending', 'completed'])->nullable();
            
            $table->boolean('is_paid')->default(false);
            $table->integer('school_id')->nullable();
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
