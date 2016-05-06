<?php

namespace SQLBuddy\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends BaseController
{
    public function index(Request $request)
    {
        return $this->view('home');
    }
}