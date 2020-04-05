<?php
/**
 * Useless test
 * only to require mock.
 */
require_once './tests/CommandMock.php';
commandmock_loadzbp();

class Class_Test extends PHPUnit\Framework\TestCase
{
    public function testUseless()
    {
        $this->assertEquals(1, 1);
    }
}
