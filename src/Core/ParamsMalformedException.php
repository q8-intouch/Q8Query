<?php

namespace Q8Intouch\Q8Query\Core;
use Exception;
use Throwable;

class ParamsMalformedException extends Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}