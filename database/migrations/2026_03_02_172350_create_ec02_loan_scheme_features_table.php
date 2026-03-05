<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEc02LoanSchemeFeaturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ec02_loan_scheme_features', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('loan_scheme_id')->nullable();

            $table->string('name')->nullable();
            $table->string('description')->nullable();
            $table->integer('order_index')->default(0);

            $table->enum('feature_type', ['optional', 'conditional', 'mandatory'])->nullable();         
            $table->enum('feature_value_type', ['text', 'number', 'date', 'boolean', 'select', 'radio', 'checkbox', 'file', 'image'])->nullable();


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
        Schema::dropIfExists('ec02_loan_scheme_features');
    }
}
