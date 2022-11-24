<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Services\DivergeChecker;

/**
 * 
 * Старую цену так же беру из запроса для простоты, в реальности скорей всего 
 * из БД будет. 
 * Порог берется так же из запроса для упрощения или если его нет в запросе
 * берется дефолтный из .env
 * 
 */

class Diverg implements Rule
{

    /**
     * 
     * @var array $errorMessages
     * 
     */
    private array $errorMessages;


    /**
     * Determine if the validation rule passes.
     * 
     * Возможно логичней было бы написать хендлер с Custom exception для этих целей,
     * но просили валидатор.
     * C синтернационализацией не заморачивался.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $divergeChecker = new DivergeChecker();
        if (array_key_exists('threshold', $value)) {
            if (is_numeric($value['threshold'])) {
                // Стоит предусмотреть проверку порога, изменения на 10000% не похоже на
                // реальный use case.
                $threshold = (float)$value['threshold'];
                if ($threshold <= 0) {
                    $this->errorMessages[] = 'Negative or zero threshold.';
                }
            } else {
                $this->errorMessages[] = 'Not valid threshold.';
            }
        } else {
            $threshold = config('app.default_threshold');
        }

        if (array_key_exists('new', $value) and is_numeric($value['new'])) {
            $new = (float)$value['new'];
            if ($new <= 0) {
                $this->errorMessages[] = 'Negative or zero new price.';
            }
        } else {
            $this->errorMessages[] = 'Invalid or absent new price.';
        }

        if (array_key_exists('out', $value) and is_numeric($value['out'])) {
            $out = (float)$value['out'];
            if ($out <= 0) {
                $this->errorMessages[] = 'Negative or zero out price.';
            }
        } else {
            $this->errorMessages[] = 'Invalid or absent out price.';
        }

        if (empty($this->errorMessages)) {
            $limitExceeded = $divergeChecker->setThreshold($threshold)->diffPrice($new, $out);
            $deviationValue = $divergeChecker->getDeviation();

            if ($limitExceeded) {
                $this->errorMessages[] = 'Price change is very significant. Amount: ' . $deviationValue . '%';
            }
        }

        return empty($this->errorMessages);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {

        return $this->errorMessages;
    }
}
