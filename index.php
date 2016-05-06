<?php

require_once __DIR__ . '/vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;

include __DIR__ . '/bootstrap/paths.php';

$router = new SQLBuddy\Router();
include __DIR__ . '/bootstrap/routes.php';

$container = include __DIR__ . '/bootstrap/container.php';

$container->setParameter('debug', true);
$container->setParameter('charset', 'UTF-8');

$request  = Request::createFromGlobals();
$response = $container->get('framework')->handle($request);
$response->send();