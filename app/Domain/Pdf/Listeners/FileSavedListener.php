<?php

namespace App\Domain\Pdf\Listeners;

use App\Domain\Pdf\Events\FileSaved;
use Psr\Log\LoggerInterface;

class FileSavedListener
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function handle(FileSaved $event): void
    {
        $this->logger->info('New file saved to ' . $event->savedPath);
    }
}
