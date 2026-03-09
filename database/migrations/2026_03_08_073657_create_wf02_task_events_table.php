<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWf02TaskEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wf02_task_events', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('task_category_id')->unsigned();

            $table->string('name');
            $table->string('description')->nullable();

            $table->boolean('is_active')->default(true);
            $table->integer('school_id')->nullable();
            $table->string('remarks')->nullable();
            // $table->foreign('task_category_id')->references('id')->on('wf01_task_categories')->onDelete('cascade');
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
        Schema::dropIfExists('wf02_task_events');
    }
}
