<?php
declare(strict_types=1);

namespace App\Services;

use App\Exceptions\InsufficientDataForDev;
use App\Contracts\Diverge;

/**
 * 
 * Сервис проверки превышения цены.
 * Какой то странный гибрид fluent interface и обычного объекта.
 * 
 */

class DivergeChecker implements Diverge 
{

    /**
     * 
     * @var float $devThreshold допустимое отклонение
     * 
     */
    private float $devThreshold;

    /**
     * 
     * @var float $deviation отклонение
     * 
     */
    private float $deviation;

    /**
     * 
     * @var float $devThreshold порог допустимого отклонения
     * @throw DivergeException
     */
    public function setThreshold(float $devThreshold)
    {
        if (!is_numeric($devThreshold)) {
            throw new DivergeException('Incorrect price deviation threshold.');
        }
        $this->devThreshold = $devThreshold;
        return $this;
    }

    /**
    *
    * Отклонение не должно быть больше допустимого значения (%)
    * Вычисляет отклонение для геттера getDeviation.
    * В случае если новая цена больше старой отклонение будет с положительным знаком,
    * в противном случае с отрицательным.
    * Сравнение с порогом отклонения производится по модулю.
    *
    * @param float $new новая цена которую будем проверять
    * @param float $out текущая цена
    * @throw DivergeException
    * @return bool
    */

    public function diffPrice(float $new, float $out): bool
    {
        if (is_numeric($new) and $new <=0 ) {
            throw new DivergeException('Incorrect new price.');
        }
        if (is_numeric($out) and $out <=0 ) {
            throw new DivergeException('Incorrect out price.');
        }
        
        if ($new === $out) {
            $this->deviation = 0;
        } else if ($new > $out) {
            $this->deviation = ($new - $out)/$out * 100;
        } else {
            $this->deviation = -($out - $new)/$out * 100;
        }
        
        return (abs($this->deviation) > $this->devThreshold) ? true : false;
    }

    /**
    *
    * Результат отклонения в %
    * @throw DivergeException
    * @return float
    *
    */
    public function getDeviation(): float
    {
        if (!isset($this->deviation)) {
            throw new DivergeException('Deviation no calculated.');
        }

        return $this->deviation;
    }

}