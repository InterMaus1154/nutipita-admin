<?php

namespace App\Helpers;

trait QueryCustomerIdTrait
{
    public int $customerId;

    public function customerId(int $customerId): static
    {
        $this->customerId = $customerId;
        return $this;
    }
}
