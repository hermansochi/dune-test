<?php
declare(strict_types=1);

namespace App\Services;

use App\Exceptions\InsufficientDataForDev;
use App\Contracts\Diverge;

/**
 * 
 * Сервис проверки превышения цены
 * 
 */

class DivergeCheck implements Diverge 
{

    /**
     * 
     * @var float $devTreshold допустимое отклонение
     * 
     */
    private float $devTreshold;

    /**
     * 
     * @var float $deviation отклонение
     * 
     */
    private float $deviaton;

    /**
     * 
     * @var float $devTreshold порог допустимого отклонения
     * @throw NotValidDevTreshold
     */
    public function __construct(float $devTreshold)
    {
        $this->devTreshold = $devTreshold;
    }

    /**
    *
    * Отклонение не должно быть больше допустимого значения (%)
    *
    * @param float $new новая цена которую будем пароверять
    * @param float $out текущая цена
    * @return bool
    */

    public function diffPrice(float $new, float $out): bool
    {
        $this->deviaton = ($new - $out)/$out;
        return ($this->deviation > $this->devTreshold) ? true : false;
    }

    /**
    *
    * Результат отклонения в %
    *
    * @return float
    */

    public function getDeviation(): float
    {
        if (!isset($this->deviation)) {
            throw new InsufficientDataForDev();
        }

        return $this->deviaton;
    }

}