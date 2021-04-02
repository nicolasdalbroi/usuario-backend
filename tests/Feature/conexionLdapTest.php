<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;


class conexionLdapTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testConexionConLdap()
    {
        
        $response = $this->post('api/login', [
            "usuario" => "123456",
            "password" => "q1w2e3r4$"
        ]);
        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => 'ok',
            ]);


    }
}
