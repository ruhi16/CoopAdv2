<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEc16ShfundMemberMasterDbsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ec16_shfund_member_master_dbs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();

            $table->integer('member_id');
            $table->integer('loan_assign_id');

            $table->double('share_operational_amount', 10, 2)->nullable();
            $table->enum('share_operational_type',['deposit', 'withdrawal'])->nullable()->default(null);
            $table->timestamp('share_operational_date')->useCurrent();


            $table->double('share_current_balnce')->nullable(); //****/
            
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
        Schema::dropIfExists('ec16_shfund_member_master_dbs');
    }
}
