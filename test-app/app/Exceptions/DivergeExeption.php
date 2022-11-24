<?php

namespace App\Exceptions;

use Exception;

class DivergeException extends Exception
{
    /**
     * Report or log an exception.
     *
     * @return void
     */
    public function report(): void
    {
        \Log::debug('Insufficient data for check deviation. Call method diffPrice before getDeviation.');
    }
}
