<?php

namespace App\Domain\Invoice\Exceptions;

use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class NoProductLineException extends UnprocessableEntityHttpException
{
    public function __construct()
    {
        parent::__construct(message: "Invoice cannot be created without products");
    }
}
