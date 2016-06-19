<?php

/**
 * Copyright (c) 2010-2016 Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eureka\Component\Routing;

/**
 * Route interface.
 *
 * @author Romain Cottard
 * @version 2.1.0
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
     * Verify if the url match with current route.
     *
     * @param  string $url
     * @return boolean
     * @throws \Exception
     */
    public function verify($url);
}