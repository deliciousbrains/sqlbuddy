<?php

use Symfony\Component\DependencyInjection;
use Symfony\Component\DependencyInjection\Reference;

$container = new DependencyInjection\ContainerBuilder();
$container->register('context', 'Symfony\Component\Routing\RequestContext');
$container->register('matcher', 'Symfony\Component\Routing\Matcher\UrlMatcher')->setArguments([
    $router->routes(),
    new Reference('context'),
]);
$container->register('request_stack', 'Symfony\Component\HttpFoundation\RequestStack');
$container->register('resolver', 'Symfony\Component\HttpKernel\Controller\ControllerResolver');

$container->register('listener.router', 'Symfony\Component\HttpKernel\EventListener\RouterListener')
          ->setArguments([new Reference('matcher'), new Reference('request_stack')]);
$container->register('listener.response', 'Symfony\Component\HttpKernel\EventListener\ResponseListener')
          ->setArguments(['%charset%']);
$container->register('listener.exception', 'Symfony\Component\HttpKernel\EventListener\ExceptionListener')
          ->setArguments(['SQLBuddy\\Controllers\\ErrorController::exceptionAction']);

$container->register('dispatcher', 'Symfony\Component\EventDispatcher\EventDispatcher')
          ->addMethodCall('addSubscriber', [new Reference('listener.router')])
          ->addMethodCall('addSubscriber', [new Reference('listener.response')])
          ->addMethodCall('addSubscriber', [new Reference('listener.exception')]);

$container->register('framework', 'SQLBuddy\Framework')->setArguments([
    new Reference('dispatcher'),
    new Reference('resolver'),
]);

return $container;