<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWf09TaskExecutionDetailStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wf09_task_execution_detail_statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('description')->nullable();

            $table->integer('task_initiated_by')->unsigned()->nullable();            
            $table->datetime('task_initiated_on')->unsigned()->nullable();

            $table->integer('task_approved_by')->unsigned()->nullable();
            $table->datetime('task_approved_on')->nullable()->unsigned();

            // $table->enum('status', ['initiated', 'suspended','pending', 'approved', 'persuing', 'rejected', 'completed'])->nullable();

            $table->boolean('is_active')->default(true);
            $table->integer('school_id')->unsigned()->nullable();
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
        Schema::dropIfExists('wf09_task_execution_detail_statuses');
    }
}
