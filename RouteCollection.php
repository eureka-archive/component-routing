<?php

/**
 * Copyright (c) 2010-2016 Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eureka\Component\Routing;

/**
 * Route Collection class.
 *
 * @author Romain Cottard
 * @version 2.1.0
 */
class RouteCollection
{
    /**
     * Current class instance.
     *
     * @var RouteCollection $instance
     */
    protected static $instance = null;

    /**
     * Collection of Routes
     *
     * @var Route[] $routes
     */
    protected $routes = array();

    /**
     * RouteCollection constructor.
     *
     * @param  Route[] $routes Route to add
     * @throws \Exception
     */
    protected function __construct($routes = array())
    {
        $this->routes = $routes;
    }

    /**
     * Get current class instance.
     *
     * @param  Route[] $routes Route to add
     * @return RouteCollection
     */
    public static function getInstance($routes = array())
    {
        if (null === static::$instance) {
            static::$instance = new RouteCollection($routes);
        }

        return static::$instance;
    }

    /**
     * Create new instance of RouteCollection.
     *
     * @param  array $routes
     * @return RouteCollection
     */
    public static function newInstance($routes = array())
    {
        return new RouteCollection($routes);
    }

    /**
     * Add routes data from configuration file.
     *
     * @param  array $config
     * @return RouteCollection
     */
    public function addFromConfig(array $config)
    {
        foreach ($config as $name => $data) {
            $params = isset($data['params']) ? $data['params'] : array();
            $parameters = array();
            foreach ($params as $nameParam => $param) {
                if (empty($params['type'])) {
                    $params['type'] = 'string';
                }

                switch ($param['type']) {

                    case 'int':
                        $param['type'] = Parameter::TYPE_INTEGER;
                        break;

                    case 'mixed':
                        $param['type'] = Parameter::TYPE_MIXED;
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
    }

    /**
     * Add route to list
     *
     * @param  Route $route
     * @return RouteCollection
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
     * @throws \DomainException
     */
    public function get($name)
    {
        if (!isset($this->routes[$name])) {
            throw new \DomainException('Route does not exist!');
        }

        return $this->routes[$name];
    }

    /**
     * Try to find a route that match the specified url.
     *
     * @param  string $url
     * @param  bool   $redirect404
     * @return Route|null
     * @throws \Exception
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
            $routeFound = $this->match('/page-not-found', false);
        }

        return $routeFound;
    }
}