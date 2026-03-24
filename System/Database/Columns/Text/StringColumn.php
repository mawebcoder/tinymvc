<?php

namespace System\Database\Columns\Text;

use System\Database\Enums\ColumnOnDeleteActionTypeEnum;

class StringColumn
{
    public int $length = 255;
    public ColumnOnDeleteActionTypeEnum $cascadeType = ColumnOnDeleteActionTypeEnum::RESTRICT;

    public bool $nullable = false;


    public function setLength(int $length): StringColumn
    {
        $this->length = $length;

        return $this;
    }

    public function onDeleteCascade(): StringColumn
    {
        $this->cascadeType = ColumnOnDeleteActionTypeEnum::ON_DELETE;

        return $this;
    }

    public function onDeleteNoAction(): StringColumn
    {
        $this->cascadeType = ColumnOnDeleteActionTypeEnum::NO_ACTION;
        return $this;
    }

    public function onDeleteRestricted(): StringColumn
    {
        $this->cascadeType = ColumnOnDeleteActionTypeEnum::RESTRICT;
        return $this;
    }

    public function onDeleteSetNull(): StringColumn
    {
        $this->cascadeType = ColumnOnDeleteActionTypeEnum::SET_NULL;
        return $this;
    }

    public function nullable(): StringColumn
    {
        $this->nullable = true;
        return $this;
    }
}