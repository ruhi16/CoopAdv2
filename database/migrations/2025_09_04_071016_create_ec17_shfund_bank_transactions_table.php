<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEc17ShfundBankTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ec17_shfund_bank_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();

            $table->integer('member_id')->nullable();

            $table->string('transaction_id');   
            $table->enum('transaction_type',['deposit', 'withdrawal'])->nullable()->default(null);
            $table->double('transaction_amount', 10, 2)->nullable();
            $table->timestamp('transaction_date')->useCurrent();
            $table->string('transaction_reasons')->nullable();   

            $table->integer('task_execution_id')->nullable();   //****/  
            

            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            
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
        Schema::dropIfExists('ec17_shfund_bank_transactions');
    }
}
