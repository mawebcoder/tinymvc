<?php

namespace System\Database;

abstract class Migration
{

    public function runMigration(): void
    {
        $this->up();
    }

    abstract public function up();
}