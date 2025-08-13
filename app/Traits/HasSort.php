<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasSort
{
    public string $sortDirection = "desc";
    public ?string $sortField = null;

    public ?string $resetPageMethod = null;


    /**
     * Initialise sorting field and direction (optional, desc default)
     * Provide reset method name as well, optional
     * @param string $sortField
     * @param string|null $sortDirection
     * @param string|null $resetPageMethod
     * @return void
     */
    public function initSort(string $sortField, ?string $sortDirection = "desc", ?string $resetPageMethod = null): void
    {
        $this->sortField = $sortField;
        $this->sortDirection = $sortDirection;
        if (!is_null($resetPageMethod)) {
            $this->resetPageMethod = $resetPageMethod;
        }
    }

    /**
     * Set the method that should happen after each new sort applied
     * @param string $method
     * @return void
     */
    public function setResetPageMethod(string $method): void
    {
        $this->resetPageMethod = $method;
    }

    public function setSort(string $sortField, ?string $sortDirection = null): void
    {
        // if direction is provided, set that manually - mainly for mobile sorting
        if (!is_null($sortDirection)) {
            $this->sortDirection = $sortDirection;
            $this->sortField = $sortField;
        } else {
            // do not reset if the field is the same
            if ($this->sortField !== $sortField) {
                $this->sortField = $sortField;
            }
            // toggle direction
            $this->sortDirection = $this->sortDirection === "asc" ? "desc" : "asc";
        }

        // call reset method if set and present
        if (!is_null($this->resetPageMethod) && method_exists($this, $this->resetPageMethod)) {
            $this->{$this->resetPageMethod}();
        }
    }

    public function applySort(Builder $builder, array $customSorts = []): Builder
    {
        if(isset($customSorts[$this->sortField])){
            // call custom sorting method if sort field key is set
            $customSorts[$this->sortField]($builder);
        }else{
            // default sorting
            $builder->orderBy($this->sortField, $this->sortDirection);
        }

        return $builder;
    }
}
