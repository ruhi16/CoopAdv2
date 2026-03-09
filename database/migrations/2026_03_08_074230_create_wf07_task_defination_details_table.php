<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWf07TaskDefinationDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wf07_task_defination_details', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('description')->nullable();

            $table->integer('task_defination_id')->unsigned();

            $table->integer('task_event_sequence_no')->unsigned();
            $table->integer('task_event_phase_id')->unsigned();
            $table->integer('task_event_phase_table_id')->unsigned();
            $table->integer('task_event_phase_table_operation_id')->unsigned();



            // $table->foreign('task_defination_id')->references('id')->on('wf07_task_definations')->onDelete('cascade');


            $table->boolean('is_active')->default(true);
            $table->integer('school_id')->unsigned()->nullable();
            $table->string('remarks')->nullable();
            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('approved_by')->unsigned()->nullable();
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
        Schema::dropIfExists('wf07_task_defination_details');
    }
}
