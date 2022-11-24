<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\DivergeCheck;

class DivergeCheckTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_create_valid_object_with_valid_constructor_args()
    {
        $mock = $this->getMockBuilder(DivergeCheck::class)
            ->setConstructorArgs([6.66])
            ->getMock();
        $this->assertInstanceOf(DivergeCheck::class, $mock);
        $this->assertEquals('6.66', $mock);
    }
    public function test_create_invalid_object()
    {
        $valid = new DivergeCheck('6.66');
      
    }
}
