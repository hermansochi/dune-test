<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\Diverge;
use App\Exceptions\DivergeException;

/**
 * Сервис проверки превышения цены.
 * Какой то странный гибрид fluent interface и обычного объекта.
 * Возможно порог логичней было бы в конструктор передавать.
 */
class DivergeChecker implements Diverge
{
    /**
     * @var float допустимое отклонение
     */
    private float $devThreshold;

    /**
     * @var float вычисленное отклонение
     */
    private float $deviation;

    /**
     * Установить порог в % до которого изменение цены считается в пределах допустимого.
     * Возможно было бы логичней при нулевом пороге считать все отклонения допустимыми,
     * но так как в т.з. не было указаний об этом, вызываю исключение.
     *
     * @param  float  $devThreshold порог допустимого отклонения
     * @throw DivergeException
     *
     * @return DivergeChecker
     */
    public function setThreshold(float $devThreshold): DivergeChecker
    {
        if ($devThreshold === 0.0) {
            throw new DivergeException('DivergeException: The threshold cannot be zero.');
        }

        if ($devThreshold <= 0) {
            throw new DivergeException('DivergeException: The threshold cannot be negative');
        }

        $this->devThreshold = $devThreshold;

        return $this;
    }

    /**
     * Отклонение не должно быть больше допустимого значения (%)
     * Вычисляет отклонение для геттера getDeviation.
     * В случае если новая цена больше старой отклонение будет с положительным знаком,
     * в противном случае с отрицательным.
     * Сравнение с порогом отклонения производится по модулю.
     *
     * @param  float  $new новая цена которую будем проверять
     * @param  float  $out текущая цена
     * @throw DivergeException
     *
     * @return bool
     */
    public function diffPrice(float $new, float $out): bool
    {
        if ($new <= 0) {
            throw new DivergeException('DivergeException: Incorrect new price.');
        }
        if ($out <= 0) {
            throw new DivergeException('DivergeException: Incorrect out price.');
        }

        if ($new === $out) {
            $this->deviation = 0;
        } elseif ($new > $out) {
            $this->deviation = ($new - $out) / $out * 100;
        } else {
            $this->deviation = -($out - $new) / $out * 100;
        }

        return (abs($this->deviation) > $this->devThreshold) ? true : false;
    }

    /**
     * Результат отклонения в %
     *
     * @throw DivergeException
     *
     * @return float
     */
    public function getDeviation(): float
    {
        if (! isset($this->deviation)) {
            throw new DivergeException('DivergeException: Deviation not calculated.');
        }

        return $this->deviation;
    }
}
