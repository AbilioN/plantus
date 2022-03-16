<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserHealthsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_healths', function (Blueprint $table) {


            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->boolean('is_allergy')->default(false);
            $table->longText('allergy_description')->nullable();
            $table->boolean('use_medicine')->default(false);
            $table->longText('medicine_description')->nullable();
            $table->string('blood_type');
            $table->string('sus_card');
            $table->string('emergency_phone_number_a')->nullable();
            $table->string('emergency_contact_name_a')->nullable();
            $table->string('emergency_kinship_a')->nullable();
            $table->string('emergency_phone_number_b')->nullable();
            $table->string('emergency_contact_name_b')->nullable();
            $table->string('emergency_kinship_b')->nullable();



            $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('user_healths');
    }
}
