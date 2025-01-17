<?php

namespace App\Exceptions;

use Throwable;

class BadRequestException extends \Exception
{

    public function __construct($message = "Bad request", $code = 400, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}
