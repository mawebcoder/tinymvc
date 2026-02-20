<?php

namespace Application\Controllers;

class AdminController
{

    public function __construct(public string $testClass='ali')
    {
    }

    public function index($id): void
    {
        echo $id;
    }
}