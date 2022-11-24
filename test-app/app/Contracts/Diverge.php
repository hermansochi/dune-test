<?php
declare(strict_types=1);

namespace App\Contracts;

interface Diverge
{
    /**
    *
    * Отклонение не должно быть больше допустимого значения (%)
    *
    * @param float $new новая цена которую будем проверять
    * @param float $out текущая цена
    * @return bool
    */

    public function diffPrice(float $new, float $out): bool;

    /**
    *
    * Результат отклонения в %
    *
    * @return float
    */

    public function getDeviation(): float;
}