<?php

namespace System\Database;

class Schema
{

    public static function create(string $table, callable $callback): void
    {
        $blueprint = new Blueprint();

        $callback($blueprint);

        static::generateCreateScript($table,$blueprint);

        /**
         * create table if not exists users (
         * id bigint unsigned
         *
         * )
         */
    }


    private static function generateCreateScript(string $table,Blueprint $blueprint): void
    {
        $script='CREATE TABLE IF NOT EXISTS `'.$table.'` ( ';


        foreach ($blueprint->getColumns() as $column=>$definition) {

            $script.=static::getConstraints($definition);

        }
    }

    public function getConstraints($definition): string
    {

    }
}