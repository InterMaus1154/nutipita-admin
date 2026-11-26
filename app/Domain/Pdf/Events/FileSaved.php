<?php

namespace App\Domain\Pdf\Events;

use App\Helpers\Event;

class FileSaved extends Event
{
    public function __construct(public string $savedPath)
    {
    }
}
