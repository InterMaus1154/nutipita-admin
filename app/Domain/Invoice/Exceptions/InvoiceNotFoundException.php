<?php

namespace App\Domain\Invoice\Exceptions;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class InvoiceNotFoundException extends NotFoundHttpException
{

    public function __construct(int $invoiceId)
    {
        $message = sprintf('Invoice with id %s not found', $invoiceId);
        parent::__construct($message);
    }
}
