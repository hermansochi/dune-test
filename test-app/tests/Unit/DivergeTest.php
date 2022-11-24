<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\DivergeChecker;

class DivergeCheckerTest extends TestCase
{
    /**
     * 
     * @var DivergeCheck $object
     * 
     */
    private DivergeChecker $object;

    protected function setUp(): void
    {
        $this->object = new DivergeChecker();
    }

    /**
     * Так как в задании просили валидатор, то буду считать что в сервис приходят уже валидные данные
     * и не особо дублировать функционал feature тестов.
     *
     * @return void
     */
    public function test_set_valid_threshold(): void
    {
        $result = $this->object->setThreshold(15.5);
        $this->assertInstanceOf(DivergeChecker::class, $result);
    }
    public function test_set_invalid_threshold()
    {
        $result = $this->object->setThreshold('1f5.5');
        $this->assertInstanceOf(DivergeChecker::class, $result);
      
    }
}
