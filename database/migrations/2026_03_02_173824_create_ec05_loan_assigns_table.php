<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEc05LoanAssignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ec05_loan_assigns', function (Blueprint $table) {
            $table->id();
            $table->integer('member_id')->nullable();
            $table->integer('loan_request_id')->nullable();
            $table->integer('loan_scheme_id')->nullable();

            $table->string('name')->nullable();
            $table->string('description')->nullable();
            $table->integer('order_index')->default(0);

            $table->date('loan_assigned_date')->nullable();
            $table->date('loan_released_date')->nullable();
            $table->date('loan_closed_date')->nullable();
            
            $table->decimal('loan_amount', 15, 2)->nullable();
            $table->decimal('loan_current_balance', 15, 2)->nullable();
            $table->decimal('roi', 10, 2)->nullable();

            $table->boolean('is_emi_enabled')->default(false);
            $table->integer('no_of_emi')->default(0);
            $table->decimal('emi_amount', 15, 2)->default(0.00);
            $table->date('first_emi_due_date')->nullable();
            $table->date('next_emi_due_date')->nullable();

            
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
        Schema::dropIfExists('ec05_loan_assigns');
    }
}
