<?php

namespace App\Helpers;

trait QueryCustomerIdTrait
{
    public int $customerId;

    public function customerId(int $customerId): self
    {
        $this->customerId = $customerId;
        return $this;
    }
}
