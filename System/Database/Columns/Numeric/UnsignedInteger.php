<?php

namespace System\Database\Columns\Numeric;

use System\Database\Columns\ColumnTrait;

class UnsignedInteger
{
    use ColumnTrait;

    public ?int $length = null;
}