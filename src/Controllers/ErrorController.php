<?php

namespace SQLBuddy\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Debug\Exception\FlattenException;

class ErrorController
{
    public function exceptionAction(FlattenException $exception)
    {
        $msg = 'Something went wrong! (' . $exception->getMessage() . ')';

        return new Response($msg, $exception->getStatusCode());
    }
}