<?php

/**
 * Copyright (c) 2010-2016 Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eureka\Component\Routing;

/**
 * Paramater Collection class.
 *
 * @author Romain Cottard
 * @version 2.1.0
 */
class ParameterCollection
{
    /**
     * Collection of Parameters (with name as key)
     *
     * @var Parameter[] $parametersByName
     */
    protected $parametersByName = array();

    /**
     * Collection of Parameters (with position as key)
     *
     * @var Parameter[] $parameters
     */
    protected $parametersByPosition = array();

    /**
     * ParameterCollection constructor.
     *
     * @param  Parameter[] $parameters Paramater to add
     * @throws \Exception
     */
    public function __construct(Array $parameters = array())
    {
        foreach ($parameters as $parameter) {
            $this->parametersByName[$parameter->getName()] = $parameter;
            $this->parametersByPosition[count($this->parametersByPosition)] = $parameter;
        }
    }

    /**
     * Add paramater to list
     *
     * @param  Parameter $parameter
     * @return ParameterCollection
     */
    public function add(Parameter $parameter)
    {
        $this->parametersByName[$parameter->getName()] = $parameter;
        $this->parametersByPosition[count($this->parametersByPosition)] = $parameter;

        return $this;
    }

    /**
     * Check if has parameter by name.
     *
     * @param $name
     * @return bool
     */
    public function hasByName($name)
    {
        try {
            $this->getByName($name);

            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * Get parameter by his name.
     *
     * @param  string $name
     * @return Parameter
     * @throws \Exception
     */
    public function getByName($name)
    {
        $name = (string) $name;

        if (substr($name, 0, 1) !== ':') {
            $name = ':' . $name;
        }

        if (!isset($this->parametersByName[$name])) {
            throw new \Exception('Parameter is not defined !');
        }

        return $this->parametersByName[$name];
    }

    /**
     * Get parameter by his position.
     *
     * @param  integer $position
     * @return Parameter
     * @throws \Exception
     */
    public function getByPosition($position)
    {
        $position = (int) $position;

        if (!isset($this->parametersByPosition[$position])) {
            throw new \Exception('Parameter is not defined !');
        }

        return $this->parametersByPosition[$position];
    }

}