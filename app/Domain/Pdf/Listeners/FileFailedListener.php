<?php

namespace App\Domain\Pdf\Listeners;

use App\Domain\Pdf\Events\FileFailed;
use Psr\Log\LoggerInterface;

class FileFailedListener
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function handle(FileFailed $event): void
    {
        $this->logger->error($event->exception->getMessage(), ['exception' => $event->exception]);
    }
}
