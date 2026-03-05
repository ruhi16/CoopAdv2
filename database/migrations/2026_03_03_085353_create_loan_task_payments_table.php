<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoanTaskPaymentsTable extends Migration{
    
    public function up()
    {
        Schema::create('loan_task_payments', function (Blueprint $table) {
            $table->id();
            $table->integer('loan_assign_id')->nullable(false);
            $table->foreignId('loan_task_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 15, 2)->default(0);

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
        Schema::dropIfExists('loan_task_payments');
    }
}
