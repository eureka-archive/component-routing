<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eureka\Component\Routing;

/**
 * Route class.
 *
 * @author Romain Cottard
 */
class Route implements RouteInterface
{
    /** @var string $name Route name */
    protected $name = '';

    /** @var string $controller Controller to use by the route */
    protected $controller = '';

    /** @var string $action Action to call in controller */
    protected $action = 'index';

    /** @var string $route Route */
    protected $route = '';

    /** @var ParameterCollection $parameterCollection Route parameters */
    protected $parameterCollection = null;

    /** @var string $pattern Route pattern */
    protected $pattern = '';

    /**
     * Route constructor.
     *
     * @param  string      $name Route name
     * @param  string      $route Route string (ie: /news/view/{:id}
     * @param  string      $controllerAction Controller::action
     * @param  Parameter[] $parameters Parameter, if necessary.
     * @throws \Eureka\Component\Routing\Exception\RoutingException
     */
    public function __construct($name, $route, $controllerAction, array $parameters = [])
    {
        $this->name  = $name;
        $this->route = $route;

        $controllerAction = explode('::', $controllerAction);
        if (empty($controllerAction[0])) {
            throw new Exception\RoutingException('Controller must be defined !');
        }

        $this->controller = $controllerAction[0];
        $this->action     = (!empty($controllerAction[1]) ? $controllerAction[1] : 'index');

        $this->parameterCollection = new ParameterCollection();

        $this->build($parameters);
    }

    /**
     * Build pattern based on the route to match uri.
     *
     * @param  Parameter[] $parameters
     * @return void
     */
    protected function build(array $parameters)
    {
        $route    = preg_split('`([/.-])`', $this->route, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        $regex    = [];
        $position = 0;

        foreach ($route as $index => $element) {
            switch ($element) {
                case '.':
                    $regex[$element . '-' . $index] = '\\' . $element;
                    break;
                case '/':
                case '-':
                    $regex[$element . '-' . $index] = $element;
                    break;

                default:

                    if (':' === substr($element, 0, 1)) {
                        $elementName = substr($element, 1);
                        $position++;

                        if (!isset($parameters[$elementName])) {
                            $parameters[$elementName] = new Parameter($elementName);
                        }

                        $regex[$elementName] = '(' . $parameters[$elementName]->getType() . ')';

                        $this->parameterCollection->add($parameters[$elementName]);
                    } else {
                        $regex[$element] = $element;
                    }
            }
        }

        $this->pattern = '`^' . implode('', $regex) . '$`i';
    }

    /**
     * Return route's controller
     *
     * @return string
     */
    public function getControllerName()
    {
        return $this->controller;
    }

    /**
     * Return route's controller action name.
     *
     * @return string
     */
    public function getActionName()
    {
        return $this->action;
    }

    /**
     * Return route's name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get parameters collection
     *
     * @return ParameterCollection
     */
    public function getParameterCollection()
    {
        return $this->parameterCollection;
    }

    /**
     * Get uri.
     * Replace params by value in route.
     * Example, for route: /card/:id-:name
     *
     * $params = array(
     *     ':id' => 1,
     *     ':name' => 'black-lotus',
     * );
     *
     * @param  array $params
     * @return string
     */
    public function getUri($params = [])
    {
        $uri = $this->route;

        if (!empty($params)) {
            $uri = str_replace(array_keys($params), $params, $this->route);
        }

        return $uri;
    }

    /**
     * Verify if the url match with current route.
     *
     * @param  string $url
     * @return boolean
     * @throws \Eureka\Component\Routing\Exception\RoutingException
     * @throws \Eureka\Component\Routing\Exception\ParameterException
     */
    public function verify($url)
    {
        $url = parse_url($url);
        if ($url === false) {
            throw new Exception\RoutingException('Bad url to match (not an url ?)');
        }

        $path = isset($url['path']) ? $url['path'] : '';

        if ((bool) preg_match($this->pattern, $path, $matches)) {
            array_shift($matches);

            foreach ($matches as $position => $value) {
                $this->parameterCollection->getByPosition($position)
                    ->setValue($value);
            }

            return true;
        }

        return false;
    }
}
