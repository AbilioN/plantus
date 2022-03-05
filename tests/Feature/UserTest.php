<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    use WithFaker , RefreshDatabase, DatabaseMigrations;

    public function setUp() :void 
    {
        parent::setUp();

        $this->userData = [
            'name' =>  $this->faker->name,
            'email' => $this->faker->email,
            'cpf' => '10068673418',
            'password' => 'password'
        ];

        // $this->withExceptionHandling();
        $this->withoutExceptionHandling();

    }
    /** @test */ 
    public function user_can_be_created()
    {

        
        $response = $this->post('api/user/create' , $this->userData);
        $data = $response->decodeResponseJson();
        $response->assertStatus(200);

    }
}
