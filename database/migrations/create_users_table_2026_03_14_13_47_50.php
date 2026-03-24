<?php

use System\Database\Blueprint;
use System\Database\Schema;
use System\Database\Migration;

return new class extends Migration {


    public function up(): void
    {
        Schema::create('users', static function (Blueprint $table) {
            $table->id();

            $table->integer('age', 10);
        });
    }

};