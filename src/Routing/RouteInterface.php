<?php

/**
 * Copyright (c) 2010-2017 Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eureka\Component\Routing;

/**
 * Route interface.
 *
 * @author Romain Cottard
 */
interface RouteInterface
{

    /**
     * Return route's controller
     *
     * @return string
     */
    public function getControllerName();

    /**
     * Return route's controller action name.
     *
     * @return string
     */
    public function getActionName();

    /**
     * Return route's name.
     *
     * @return string
     */
    public function getName();

    /**
     * Get parameters collection
     *
     * @return ParameterCollection
     */
    public function getParameterCollection();

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
    public function getUri($params = array());

    /**
     * Verify if the url match with current route.
     *
     * @param  string $url
     * @return boolean
     * @throws \Exception
     */
    public function verify($url);
}