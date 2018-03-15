<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eureka\Component\Routing;

/**
 * Route Collection class.
 *
 * @author Romain Cottard
 */
class Router
{
    /** @var Route[] $routes Collection of Routes */
    protected $routes = [];

    /**
     * Router constructor.
     *
     * @throws \Eureka\Component\Routing\Exception\RoutingException
     */
    public function __construct()
    {
        //~ Each arguments must be a list of routes from configs files.
        foreach (func_get_args() as $routes) {
            $this->addFromConfig($routes);
        }
    }

    /**
     * Add routes data from configuration file.
     *
     * @param  array $config
     * @return $this
     * @throws \Eureka\Component\Routing\Exception\RoutingException
     */
    protected function addFromConfig(array $config)
    {
        foreach ($config as $name => $data) {
            $params     = isset($data['params']) ? $data['params'] : [];
            $parameters = [];
            foreach ($params as $nameParam => $param) {
                if (empty($params['type'])) {
                    $params['type'] = 'string';
                }

                switch ($param['type']) {
                    case 'int':
                        $param['type'] = Parameter::TYPE_INTEGER;
                        break;
                    case 'mixed':
                        $param['type'] = Parameter::TYPE_ANY;
                        break;
                    case 'string':
                        $param['type'] = Parameter::TYPE_STRING;
                        break;
                    default:
                        // Consider  current value as regexp
                }

                $parameters[$nameParam] = new Parameter($nameParam, $param['type'], (bool) $param['mandatory']);
            }
            $this->add(new Route($name, $data['route'], $data['controller'], $parameters));
        }

        return $this;
    }

    /**
     * Add route to list
     *
     * @param  Route $route
     * @return Router
     */
    public function add(Route $route)
    {
        $this->routes[$route->getName()] = $route;

        return $this;
    }

    /**
     * Get route by name
     *
     * @param  string $name
     * @return Route
     * @throws \Eureka\Component\Routing\Exception\RouteNotFoundException
     */
    public function get($name)
    {
        if (!isset($this->routes[$name])) {
            throw new Exception\RouteNotFoundException('Route does not exist! (name: ' . $name . ')');
        }

        return $this->routes[$name];
    }

    /**
     * Try to find a route that match the specified url.
     *
     * @param  string $url
     * @param  bool $redirect404
     * @return Route|null
     * @throws \Eureka\Component\Routing\Exception\RoutingException
     * @throws \Eureka\Component\Routing\Exception\ParameterException
     */
    public function match($url, $redirect404 = true)
    {
        $routeFound = null;

        foreach ($this->routes as $route) {
            if (!$route->verify($url)) {
                continue;
            }

            $routeFound = $route;
            break;
        }

        if (!($routeFound instanceof RouteInterface) && $redirect404 === true) {
            $routeFound = $this->get('error404');
        }

        return $routeFound;
    }
}
