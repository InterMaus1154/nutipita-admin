<?php

namespace App\Domain\Invoice\Exceptions;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CannotDeleteInvoiceFileException extends \RuntimeException
{
    public function __construct(int $id)
    {
        $message = sprintf('Couldnt delete file for invoice %s', $id);
        parent::__construct($message);
    }
}
