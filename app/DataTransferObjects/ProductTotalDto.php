<?php

namespace App\DataTransferObjects;

/**
 * Contains product name, id, and total number
 */
final readonly class ProductTotalDto
{
    private function  __construct(private int $id, private string $name, private int $total)
    {

    }

    public static function from(int $id, string $name, int $total): ProductTotalDto
    {
        return new self($id, $name, $total);
    }

    public function id(): int
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function total(): int
    {
        return $this->total;
    }
}
