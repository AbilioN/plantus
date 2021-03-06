<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('document_categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('category');
            $table->timestamps();
        });

        DB::table('document_categories')->insert([
            ['category' => 'avatar'],
            ['category' => 'address'],
            ['category' => 'health'],
            ['category' => 'professional'],
            ['category' => 'documents'],
            ['category' => 'work_card'],
            ['category' => 'vote_card'],
            ['category' => 'american_visas']

        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('document_categories');
    }
}
