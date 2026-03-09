<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEc21BankLoanBorrowedSpecsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ec21_bank_loan_borrowed_specs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();

            $table->integer('bank_loan_borrowed_id')->nullable();

            // $table->integer('bank_loanscheme_specification_id')->nullable();
            // $table->string('bank_loanscheme_specification_detail')->nullable();

            $table->string('bank_loan_schema_particular_id')->nullable();   
            $table->double('bank_loan_schema_particular_value', 10, 2)->nullable();
            $table->boolean('is_percent_on_current_balance')->default(true);        // otherwise: fixed_value

            $table->boolean('is_regular')->default(false);  // for each month, otherwise: special or for one month only
            
            
            
            $table->date('effected_on')->nullable();

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
        Schema::dropIfExists('ec21_bank_loan_borrowed_specs');
    }
}
