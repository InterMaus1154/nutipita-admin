<?php

namespace App\Domain\Invoice\Exceptions;

use App\Helpers\UserDisplayable;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class InvoiceHasPricelessProductException extends UnprocessableEntityHttpException implements UserDisplayable
{
    public function __construct(int $productId, int $customerId)
    {
        $message = sprintf('The product #%d has no price for #%d, not invoiceable', $productId, $customerId);
        parent::__construct($message);
    }
}
