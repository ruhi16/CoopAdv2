<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWf09TaskExecutionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wf09_task_executions', function (Blueprint $table) {
            $table->increments('id');

            // $table->integer('task_category_id')->unsigned();
            // $table->integer('task_event_id')->unsigned();

            $table->integer('task_defination_id')->unsigned();


            $table->string('name');
            $table->string('description')->nullable();

            $table->integer('task_initiated_by')->unsigned();            
            $table->datetime('task_initiated_on');

            $table->integer('task_approved_by')->unsigned();
            $table->datetime('task_approved_on')->nullable();

            $table->enum('status', ['initiated', 'suspended','pending', 'approved', 'persuing', 'rejected', 'completed'])->nullable();

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
        Schema::dropIfExists('wf09_task_executions');
    }
}
