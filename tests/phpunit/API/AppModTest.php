<?php

namespace Tests\PHPUnit\API;

class AppModTest extends TestCase
{
    /** @test */
    public function get_apps()
    {
        $this->callAPI('app', 'get_apps')
            ->assertStatus(200);
    }
}
