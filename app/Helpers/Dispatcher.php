<?php

namespace App\Helpers;

use \Illuminate\Events\Dispatcher as IlluminateDispatcher;
use Psr\Log\LoggerInterface;

class Dispatcher extends IlluminateDispatcher implements DispatcherInterface
{
    public function dispatch($event, $payload = [], $halt = false)
    {
        if($event instanceof Event) {
            $this->logDispatchedEvent($event);
        }
        return parent::dispatch($event, $payload, $halt);
    }

    public function logDispatchedEvent(Event $event): void
    {
        $logger = app(LoggerInterface::class);
        $class = get_class($event);
        $message = sprintf('[ %s Dispatched ]', $class);
        $logger->info($message);
    }
}
