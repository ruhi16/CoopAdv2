<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEc03LoanRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ec03_loan_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('member_id')->nullable();
            $table->integer('loan_scheme_id')->nullable();
            $table->double('loan_amount')->nullable();
            $table->integer('no_of_years')->nullable();
            $table->boolean('emi_active')->default(false);
            
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
        Schema::dropIfExists('ec03_loan_requests');
    }
}
