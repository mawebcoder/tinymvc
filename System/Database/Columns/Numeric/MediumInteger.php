<?php

namespace System\Database\Columns\Numeric;

use System\Database\Columns\ColumnTrait;

class MediumInteger
{
    use ColumnTrait;
    public bool $unsigned = false;

    public ?int $length =null;
}