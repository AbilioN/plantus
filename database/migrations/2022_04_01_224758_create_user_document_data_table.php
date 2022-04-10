<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserDocumentDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_document_data', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('gender');
            $table->string('marital_status');
            $table->string('mother');
            $table->string('father')->nullable();
            $table->string('bank_account');

            $table->unsignedBigInteger('user_id');                            
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
        Schema::dropIfExists('user_document_data');
    }
}
