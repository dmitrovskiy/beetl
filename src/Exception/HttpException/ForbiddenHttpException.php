<?php

namespace Exception\HttpException;

use Exception;

class ForbiddenHttpException extends \Exception
{
    public function __construct($message, $code = 403, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}