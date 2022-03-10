<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('path');
            $table->string('extension');
            $table->unsignedBigInteger('document_category_id');
            $table->unsignedBigInteger('user_id');

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('document_category_id')->references('id')->on('document_categories');

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
        Schema::dropIfExists('documents');
    }
}
