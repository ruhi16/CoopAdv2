<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEc23BankLoanPaymentDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ec23_bank_loan_payment_details', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();

            $table->integer('bank_loan_payment_id')->nullable();
            $table->integer('bank_loan_schedule_id')->nullable();


            $table->integer('bank_loan_borrowed_spec_id')->nullable();
            $table->string('bank_loan_borrowed_spec_detail')->nullable();
            
            $table->integer('bank_loan_schema_particular_id')->nullable();   
            $table->double('bank_loan_schema_particular_amount', 10, 2)->nullable();
            $table->double('installment_amount', 10, 2)->nullable();


            $table->integer('task_execution_id')->nullable();   //****/  
            
            $table->enum('status', ['running', 'completed', 'upcoming', 'suspended', 'cancelled'])->default('suspended')->nullable();
            
            $table->integer('user_id')->nullable();
            $table->integer('school_id')->nullable();
            $table->integer('financial_year_id')->nullable();

            $table->boolean('is_finalized')->nullable()->default(true);
            $table->integer('finalized_by')->nullable();
            $table->date('finalized_at')->nullable();

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
        Schema::dropIfExists('ec23_bank_loan_payment_details');
    }
}
