<?php

namespace System\Database\Columns\Numeric;

use System\Database\Columns\ColumnTrait;

class FloatColumn
{
    use ColumnTrait;
    public bool $unsigned = false;
    public int $digits = 20;
    public int $precision = 2;
}