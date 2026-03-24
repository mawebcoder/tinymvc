<?php

namespace System\Database\Columns;

trait ColumnTrait
{

    public bool $autoincrement = false;

    public bool $unique = false {
        get => $this->unique;
    }

    public bool $primary = false {
        get {
            return $this->primary;
        }
    }
    public bool $nullable = false;

    public bool $index = false;

    public ?string $indexName = null;

    public ?string $uniqueName = null;


    public function index(?string $index = null): static
    {
        $this->index = true;

        $this->indexName = $index;

        return $this;
    }

    public function unique(?string $uniqueName = null): static
    {
        $this->unique = true;

        $this->uniqueName = $uniqueName;

        return $this;
    }

    public function nullable(): static
    {
        $this->nullable = true;

        return $this;
    }

    public function primary(): static
    {
        $this->primary = true;

        return $this;
    }

    public function autoincrement(): static
    {
        $this->autoincrement = true;
        return $this;
    }

    public function isUnsigned(): bool
    {
        return $this->unsigned;
    }

}