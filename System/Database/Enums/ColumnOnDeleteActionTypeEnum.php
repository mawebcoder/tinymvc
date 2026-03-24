<?php

namespace System\Database\Enums;

enum ColumnOnDeleteActionTypeEnum: int
{
    case ON_DELETE = 1;
    case SET_NULL = 2;
    case RESTRICT = 3;
    case NO_ACTION = 4;
}