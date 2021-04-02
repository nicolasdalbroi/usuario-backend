<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class conexionConApiLoginTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testConexionConApiLogin()
    {
        $response = $this->post( 'api/login');
        $response->assertStatus(200);
        
    
    }
}
