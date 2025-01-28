<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;

class FailToCreateException extends Exception
{
    public function __construct(string $message = "Fail to Create", int $code = Response::HTTP_INTERNAL_SERVER_ERROR, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
