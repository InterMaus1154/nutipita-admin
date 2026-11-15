<?php

namespace App\Helpers;

abstract class AbstractQuery
{
    public int $id;

    /**
     * @var string[]
     */
    public array $relationships = [];

    /**
     * @var string[]
     */
    public array $columns = [];

    public function id(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @param string[] $relationships
     * @return self
     */
    public function relationships(array $relationships): self
    {
        $this->relationships = $relationships;
        return $this;
    }

    /**
     * @param string[] $columns
     * @return $this
     */
    public function columns(array $columns = []): self
    {
        $this->columns = $columns;
        return $this;
    }
}
