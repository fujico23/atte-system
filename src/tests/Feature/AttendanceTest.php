<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

require_once 'AuthController.php';

class AttendanceTest extends TestCase
{
    public function setUp() :void {}
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->get('/');
        $response = $this->post('/store');

        $response->assertStatus(200);
    }
}
