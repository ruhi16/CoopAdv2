<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEc17ShfundBankMasterDbsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ec17_shfund_bank_master_dbs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();

            $table->integer('bank_id');
            $table->integer('loan_assign_id');

            $table->double('bank_loan_previous_balnce')->nullable(); //****/
            $table->double('bank_share_previous_balnce')->nullable(); //****/

            $table->double('bank_share_operational_amount', 10, 2)->nullable();
            $table->enum('bank_share_operational_type',['deposit', 'withdrawal'])->nullable()->default(null);
            $table->timestamp('bank_share_operational_date')->useCurrent();


            $table->double('bank_share_current_balnce')->nullable(); //****/
            
            $table->integer('task_execution_id')->nullable();   //****/            

            $table->integer('user_id')->nullable();
            $table->integer('school_id')->nullable();
            $table->integer('financial_year_id')->nullable();
            
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');

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
        Schema::dropIfExists('ec17_shfund_bank_master_dbs');
    }
}
