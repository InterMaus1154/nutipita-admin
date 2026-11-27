<?php

namespace App\Domain\Pdf\Exceptions;

class NoItemsForInvoiceSummaryException extends \RuntimeException
{
    public function __construct(string $invoiceNumber)
    {
        $message = sprintf('Invoice #%s has no items to produce a summary', $invoiceNumber);
        parent::__construct($message);
    }
}
