<?php

namespace App\Helpers;

interface QueryCustomerId
{
    public function customerId(int $customerId): static;
}
