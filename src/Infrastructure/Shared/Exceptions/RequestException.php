<?php

namespace App\Infrastructure\Shared\Exceptions;

use Exception;

class RequestException extends Exception
{
    public function __construct(array $errorMessages = [])
    {
        $message = \implode(' | ', $errorMessages);

        parent::__construct($message);
    }
}
