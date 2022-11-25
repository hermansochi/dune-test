<?php

namespace Tests\Unit;

use App\Exceptions\DivergeException;
use App\Services\DivergeChecker;
use PHPUnit\Framework\TestCase;

class DivergeCheckerTest extends TestCase
{
    /**
     * Тестируемый объект
     *
     * @var DivergeCheck
     */
    private DivergeChecker $object;

    protected function setUp(): void
    {
        $this->object = new DivergeChecker();
    }

    /**
     * Создание объекта. Тест не имеет особого смысла )
     *
     * @return void
     */
    public function test_set_valid_threshold(): void
    {
        $result = $this->object->setThreshold(15.5);
        $this->assertInstanceOf(DivergeChecker::class, $result);
        $this->assertNotEmpty($result);
    }

    /**
     * Пытаемся установить нулевой порог
     *
     * @return void
     */
    public function test_set_zero_threshold(): void
    {
        $this->expectException(DivergeException::class);
        $this->object->setThreshold(0);
    }

    /**
     * Пытаемся установить отрицательный порог
     *
     * @return void
     */
    public function test_set_negative_threshold(): void
    {
        $this->expectException(DivergeException::class);
        $this->object->setThreshold(0);
    }

    /**
     * Пытаемся установить отрицательную новую цену
     *
     * @return void
     */
    public function test_set_negative_new_price(): void
    {
        $this->expectException(DivergeException::class);
        $result = $this->object->setThreshold(10)->diffPrice(-100, 10);
    }

    /**
     * Пытаемся установить отрицательную старую цену
     *
     * @return void
     */
    public function test_set_negative_old_price(): void
    {
        $this->expectException(DivergeException::class);
        $result = $this->object->setThreshold(10)->diffPrice(100, -10);
    }

    /**
     * Порог превышен позитивно
     *
     * @return void
     */
    public function test_threshold_is_positive_over(): void
    {
        $result = $this->object->setThreshold(10)->diffPrice(100, 10);
        $this->assertTrue($result);
        $dev = $this->object->getDeviation();
        $this->assertEquals(900, $dev);
    }

    /**
     * Порог превышен негативно
     * Возможно тест не пройдет в другом окружении из за разницы в округлении
     *
     * @return void
     */
    public function test_threshold_is_negative_over(): void
    {
        $result = $this->object->setThreshold(10)->diffPrice(6.66, 11);
        $this->assertTrue($result);
        $dev = $this->object->getDeviation();
        $this->assertEquals(-39.45, round($dev, 2));
    }
}
