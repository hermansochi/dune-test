<?php

declare(strict_types=1);

namespace App\Rules;

use App\Services\DivergeChecker;
use Illuminate\Contracts\Validation\Rule;

/**
 * Старую цену так же беру из запроса для упрощения.
 * Порог берется так же из запроса для упрощения или если его нет в запросе
 * берется дефолтный из .env
 */
class Diverg implements Rule
{
    /**
     * @var array
     */
    private array $errorMessages;

    /**
     * Determine if the validation rule passes.
     *
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
                $threshold = (float) $value['threshold'];
                if ($threshold <= 0) {
                    $this->errorMessages[] = 'Negative or zero threshold.';
                }
            } else {
                $this->errorMessages[] = 'Not valid threshold.';
            }
        } else {
            $threshold = (float) config('app.default_threshold');
        }

        if (array_key_exists('new', $value) and is_numeric($value['new'])) {
            $new = (float) $value['new'];
            if ($new <= 0) {
                $this->errorMessages[] = 'Negative or zero new price.';
            }
        } else {
            $this->errorMessages[] = 'Invalid or absent new price.';
        }

        if (array_key_exists('out', $value) and is_numeric($value['out'])) {
            $out = (float) $value['out'];
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
                $this->errorMessages[] = 'Price change is very significant. Amount: '.$deviationValue.'%';
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
