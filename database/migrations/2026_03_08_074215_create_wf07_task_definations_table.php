<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWf07TaskDefinationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wf07_task_definations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('description')->nullable();

            $table->integer('task_category_id')->unsigned();
            $table->integer('task_event_id')->unsigned();



            // $table->foreign('task_category_id')->references('id')->on('wf01_task_categories')->onDelete('cascade');
            // $table->foreign('task_event_id')->references('id')->on('wf02_task_events')->onDelete('cascade');



            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('approved_by')->unsigned()->nullable();
            
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
        Schema::dropIfExists('wf07_task_definations');
    }
}
