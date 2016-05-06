<?php

namespace SQLBuddy;

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

class Router
{
    /**
     * @var RouteCollection
     */
    protected $routes;

    public function __construct()
    {
        $this->routes = new RouteCollection();
    }

    /**
     * Add a new route
     *
     * @param string $path
     * @param string $controller
     * @param array $defaults
     */
    public function add($path, $controller, array $defaults = [])
    {
        if (empty($defaults)) {
            $defaults['_controller'] = 'SQLBuddy\Controllers\\' . $controller;
        }

        $this->routes->add($path, new Route($path, $defaults));
    }

    /**
     * Return the RouteCollection
     *
     * @return RouteCollection
     */
    public function routes()
    {
        return $this->routes;
    }
}