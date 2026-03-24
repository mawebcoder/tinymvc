<?php

namespace System\Database\Columns\Numeric;

use System\Database\Columns\ColumnTrait;

class UnsignedDecimalColumn
{
    use ColumnTrait;
    public int $digits = 20;
    public int $precision = 2;
}