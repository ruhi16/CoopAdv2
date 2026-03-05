<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEc07LoanEmiSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ec07_loan_emi_schedules', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('loan_assign_id')->nullable();

            $table->string('name')->nullable();
            $table->string('description')->nullable();
            $table->integer('order_index')->default(0);

            $table->integer('emi_schedule_index')->nullable();
            $table->date('emi_due_date')->nullable();
            $table->date('emi_paid_date')->nullable();
            $table->decimal('total_emi_amount', 15, 2)->default(0);
            $table->decimal('principal_emi_amount', 15, 2)->default(0);
            $table->decimal('interest_emi_amount', 15, 2)->default(0);

            $table->decimal('principal_balance_amount_before_emi', 15, 2)->default(0);
            $table->decimal('principal_balance_amount_after_emi', 15, 2)->default(0);
          
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
        Schema::dropIfExists('ec07_loan_emi_schedules');
    }
}
