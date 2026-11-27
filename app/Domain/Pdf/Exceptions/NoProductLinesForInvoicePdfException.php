<?php

namespace App\Domain\Pdf\Exceptions;

class NoProductLinesForInvoicePdfException extends \RuntimeException
{
    public function __construct(string $number)
    {
        $message = sprintf('The invoice %s has no product lines so it cannot be reliably generated', $number);
        parent::__construct($message);
    }
}
