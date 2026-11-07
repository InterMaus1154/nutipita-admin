<?php

namespace App\Domain\Invoice;

class DeliveryCharge
{
    public function __construct(private ?string $value = null, private ?string $modifier = null)
    {
    }

    public static function empty(): self
    {
        return new DeliveryCharge(null, null);
    }

    public static function fromString(?string $value): self
    {
        $trimmed = mb_trim($value);

        if (empty($trimmed)) {
            return self::empty();
        }

        if (!str_contains($trimmed, 'x')) {
            return new DeliveryCharge($trimmed);
        }

        $parts = explode('x', $trimmed);
        $modifier = array_shift($parts);
        $value = array_shift($parts);

        return new DeliveryCharge($value, $modifier);
    }

    public function toDbString(): string
    {
        $effectiveModifier = $this->getModifier() === 0 ? null : $this->getRawModifier();

        if (is_null($effectiveModifier)) {
            return $this->getRawValue() ?? '';
        }

        return sprintf('%sx%s', $this->getRawModifier(), $this->getRawValue());
    }

    public function toFloat(): float
    {
        return $this->value ? (float)$this->value : 0;
    }

    public function getRawValue(): ?string
    {
        return $this->value;
    }

    public function getRawModifier(): ?string
    {
        return $this->modifier;
    }

    public function getModifier(): int
    {
        return $this->modifier ? (int)$this->modifier : 1;
    }

    public function getCalculated(): float
    {
        if (is_null($this->getRawModifier())) {
            return $this->toFloat();
        }

        return $this->getModifier() * $this->toFloat();
    }

    public function isNull(): bool
    {
        return is_null($this->getRawValue());
    }
}
