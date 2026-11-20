<?php

namespace App\Domain\Order\Exceptions;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OrderNotFoundException extends NotFoundHttpException
{
    public function __construct(int $orderId)
    {
        $message = sprintf('Order with id #%s not found', $orderId);
        parent::__construct($message);
    }
}
