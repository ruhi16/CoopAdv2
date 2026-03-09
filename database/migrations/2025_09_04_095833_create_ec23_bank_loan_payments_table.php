<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEc23BankLoanPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ec23_bank_loan_payments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();

            $table->integer('bank_loan_borrowed_id')->nullable();
            $table->double('bank_loan_borrowed_current_balance', 10, 2)->nullable();

            $table->double('installment_total_amount', 10, 2)->nullable();
            $table->integer('installment_no')->nullable();
            
            
            $table->date('payment_date')->nullable();


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
        Schema::dropIfExists('ec23_bank_loan_payments');
    }
}
