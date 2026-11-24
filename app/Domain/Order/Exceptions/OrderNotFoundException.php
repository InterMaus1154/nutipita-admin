<?php

namespace App\Domain\Order\Exceptions;

use App\Helpers\UserDisplayable;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OrderNotFoundException extends NotFoundHttpException implements UserDisplayable
{
    public function __construct(int $orderId)
    {
        $message = sprintf('Order with id #%s not found', $orderId);
        parent::__construct($message);
    }
}
