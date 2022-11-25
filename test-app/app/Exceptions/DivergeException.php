<?php

declare(strict_types=1);

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
        \Log::debug($this->getMessage());
    }
}
