<?php

namespace Tests\Feature;

use Tests\TestCase;

class SimpleTest extends TestCase
{
    public function test_basic_test()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }
}
