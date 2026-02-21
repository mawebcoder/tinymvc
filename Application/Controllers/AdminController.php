<?php

namespace Application\Controllers;

use System\Helper\Helper;

class AdminController extends Controller
{

    public function index()
    {
        $cssFile = Helper::asset('css/style.css');

        $this->view('welcome', compact('cssFile'));
    }
}