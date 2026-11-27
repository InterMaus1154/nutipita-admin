<?php

namespace App\Domain\Invoice\Exceptions;

use App\Helpers\UserDisplayable;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class InvoiceNumberAlreadyTakenException extends UnprocessableEntityHttpException implements UserDisplayable
{
    public function __construct(string $number)
    {
        $message = sprintf('Invoice number %s is already taken. Select a unique number!', $number);
        parent::__construct($message);
    }
}
