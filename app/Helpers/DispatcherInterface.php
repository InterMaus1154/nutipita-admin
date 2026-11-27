<?php

namespace App\Helpers;

interface DispatcherInterface extends \Illuminate\Contracts\Events\Dispatcher
{
    function logDispatchedEvent(Event $event): void;
}
