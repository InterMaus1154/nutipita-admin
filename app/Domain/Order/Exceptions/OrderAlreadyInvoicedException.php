<?php

namespace App\Domain\Order\Exceptions;

use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class OrderAlreadyInvoicedException extends ConflictHttpException
{
    public function __construct(int $orderId, string $invoiceNumber)
    {
        $message = sprintf('Order #%s is already invoiced as invoice: #%s', $orderId, $invoiceNumber);
        parent::__construct($message);
    }
}
