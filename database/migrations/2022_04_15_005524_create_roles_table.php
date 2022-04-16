<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('role');
            $table->timestamps();
        });

        DB::table('roles')->insert([
            ['role' => 'Admnistrativo'],
            ['role' => 'Direção'],
            ['role' => 'Conselho'],
            ['role' => 'Financeiro'],
            ['role' => 'Técnico'],
            ['role' => 'Comercial'],
            ['role' => 'PD&I'],
            ['role' => 'Exp. e Novos Neg']
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('roles');
    }
}
