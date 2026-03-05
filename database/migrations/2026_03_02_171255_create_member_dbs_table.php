<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMemberDbsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_dbs', function (Blueprint $table) {
            $table->increments('id');            
            $table->integer('member_type_id')->nullable();

            $table->string('name');
            $table->string('name_short')->nullable();
            $table->string('father_name')->nullable();
            $table->string('school_designation')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();
            $table->string('address')->nullable();
            $table->date('dob')->nullable();
            $table->date('doj')->nullable();
            $table->date('dor')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('nationality')->default('Indian');
            $table->string('religion')->nullable();
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed'])->nullable();
            $table->enum('blood_group', ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])->nullable();
            $table->string('pan_no', 10)->nullable();
            $table->string('aadhar_no', 12)->nullable();
            $table->string('voter_id_no', 20)->nullable();

            $table->string('account_bank')->nullable();
            $table->string('account_branch')->nullable();
            $table->string('account_no')->nullable();
            $table->string('account_ifsc')->nullable();
            $table->string('account_micr')->nullable();
            $table->string('account_customer_id')->nullable();
            $table->string('account_holder_name')->nullable();

            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('created_by')->default(0);
            $table->integer('approved_by')->default(0);   
            $table->integer('school_id')->nullable();
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
        Schema::dropIfExists('member_dbs');
    }
}
