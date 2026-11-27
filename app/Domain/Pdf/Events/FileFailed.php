<?php

namespace App\Domain\Pdf\Events;

use App\Domain\Pdf\Exceptions\CannotGeneratePdfException;
use App\Helpers\Event;

class FileFailed extends Event
{
    public function __construct(public CannotGeneratePdfException $exception)
    {
    }
}
