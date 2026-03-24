<?php

namespace System\Database;

use System\Helper\Helper;
use System\Database\Columns\Text\StringColumn;
use System\Database\Columns\Numeric\IntegerColumn;
use System\Database\Columns\Numeric\UnsignedInteger;

class Blueprint
{
    private array $columns = [];

    public string $primaryKey = 'id';

    public function integer(string $column, ?int $length = null): IntegerColumn
    {
        $integerColumn = new IntegerColumn();

        $this->columns[$column] = $integerColumn;

        $this->columns[$column]->length = $length;

        return $this->columns[$column];
    }

    public function unsignedInteger(string $column, ?int $length = null): UnsignedInteger
    {
        $integerColumn = new UnsignedInteger();

        $this->columns[$column] = $integerColumn;

        $this->columns[$column]->length = $length;

        return $this->columns[$column];
    }

    public function id(string $id = 'id'): static
    {
        $this->primaryKey = $id;

        return $this;
    }

    public function getColumns(): array
    {
        return $this->columns;
    }

}