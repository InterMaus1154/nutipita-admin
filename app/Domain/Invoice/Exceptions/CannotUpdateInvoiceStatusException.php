<?php

namespace App\Domain\Invoice\Exceptions;

class CannotUpdateInvoiceStatusException extends \RuntimeException
{
    public function __construct(string $message, ?\Throwable $previous = null)
    {
        parent::__construct($message, previous: $previous);
    }
}
