<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEc15ThfundMasterDbsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ec15_thfund_master_dbs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();

            $table->integer('task_execution_id')->nullable();  // ****
            $table->integer('member_id');

            $table->double('thfund_operational_amount', 10, 2)->nullable();
            $table->enum('thfund_operational_type',['deposit', 'withdrawal'])->nullable();
            $table->date('thfund_operational_date')->useCurrent();

            $table->double('thfund_current_balance', 10, 2)->nullable();    // *****


            $table->integer('user_id')->nullable();
            $table->integer('financialyear_id')->nullable();
            
            $table->date('start_at')->nullable();
            $table->date('end_at')->nullable();
            $table->integer('no_of_months')->nullable();
            
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            
            $table->boolean('is_finalized')->nullable()->default(true);
            $table->integer('finalized_by')->nullable();
            $table->date('finalized_at')->nullable();
            
            
            $table->integer('school_id')->nullable();
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
        Schema::dropIfExists('ec15_thfund_master_dbs');
    }
}
