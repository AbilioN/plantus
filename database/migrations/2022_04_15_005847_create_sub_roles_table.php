<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sub_roles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('sub_role');
            $table->unsignedBigInteger('role_id')->nullable()->default(null);
            $table->foreign('role_id')->references('id')->on('roles');
            $table->timestamps();
        });

        DB::table('sub_roles')->insert([
            [
                'sub_role' => 'RH',
                'role_id' => 1
            ],
            [
                'sub_role' => 'Recepção',
                'role_id' => 1
            ],    
            [
                'sub_role' => 'Bem estar',
                'role_id' => 1
            ],    
            [
                'sub_role' => 'Manutenção',
                'role_id' => 1
            ],    
            [
                'sub_role' => 'Jardim',
                'role_id' => 1
            ],    
            [
                'sub_role' => 'Guarita',
                'role_id' => 1
            ],    
            [
                'sub_role' => 'Compras',
                'role_id' => 1
            ],    
            [
                'sub_role' => 'Logistica Interna',
                'role_id' => 1
            ],    
            [
                'sub_role' => 'Contas a pagar',
                'role_id' => 4
            ],    
            [
                'sub_role' => 'Contas a receber',
                'role_id' => 4
            ],    
            [
                'sub_role' => 'Contab. Int./Ext.',
                'role_id' => 4
            ],    
            [
                'sub_role' => 'Garant. de Qual',
                'role_id' => 5
            ],
            [
                'sub_role' => 'Cont. Qualidade',
                'role_id' => 5
            ],
            [
                'sub_role' => 'Produção',
                'role_id' => 5
            ],
            [
                'sub_role' => 'Regulatório',
                'role_id' => 5
            ],
            [
                'sub_role' => 'Estagiário',
                'role_id' => 5
            ],

            [
                'sub_role' => 'Marketing',
                'role_id' => 6
            ],

            [
                'sub_role' => 'Logística',
                'role_id' => 6
            ],

            [
                'sub_role' => 'P&D',
                'role_id' => 7
            ],

            [
                'sub_role' => 'Inovação',
                'role_id' => 7
            ],

            [
                'sub_role' => 'Gerenc. Fazendas',
                'role_id' => 8
            ],

            [
                'sub_role' => 'Gerenc. Cert. Org.',
                'role_id' => 8
            ],
   
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sub_roles');
    }
}
