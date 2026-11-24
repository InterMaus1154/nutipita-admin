<?php

namespace App\Domain\Invoice\Exceptions;

use App\Helpers\UserDisplayable;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class NoProductLineException extends UnprocessableEntityHttpException implements UserDisplayable
{
    public function __construct()
    {
        parent::__construct(message: "Invoice cannot be created without products");
    }
}
