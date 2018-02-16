<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eureka\Component\Routing;

/**
 * Parameter Collection class.
 *
 * @author Romain Cottard
 */
class ParameterCollection
{
    /** @var Parameter[] $parametersByName Collection of Parameters (with name as key) */
    protected $parametersByName = array();

    /** @var Parameter[] $parameters Collection of Parameters (with position as key) */
    protected $parametersByPosition = array();

    /**
     * ParameterCollection constructor.
     *
     * @param  Parameter[] $parameters Parameter to add
     */
    public function __construct(Array $parameters = array())
    {
        foreach ($parameters as $parameter) {
            $this->parametersByName[$parameter->getName()]                  = $parameter;
            $this->parametersByPosition[count($this->parametersByPosition)] = $parameter;
        }
    }

    /**
     * Add parameter to list
     *
     * @param  Parameter $parameter
     * @return ParameterCollection
     */
    public function add(Parameter $parameter)
    {
        $this->parametersByName[$parameter->getName()]                  = $parameter;
        $this->parametersByPosition[count($this->parametersByPosition)] = $parameter;

        return $this;
    }

    /**
     * Check if has parameter by name.
     *
     * @param  string $name
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
     * @throws \Eureka\Component\Routing\Exception\ParameterException
     */
    public function getByName($name)
    {
        $name = (string) $name;

        if (substr($name, 0, 1) !== ':') {
            $name = ':' . $name;
        }

        if (!isset($this->parametersByName[$name])) {
            throw new Exception\ParameterException('Parameter is not defined !');
        }

        return $this->parametersByName[$name];
    }

    /**
     * Get parameter by his position.
     *
     * @param  integer $position
     * @return Parameter
     * @throws \Eureka\Component\Routing\Exception\ParameterException
     */
    public function getByPosition($position)
    {
        $position = (int) $position;

        if (!isset($this->parametersByPosition[$position])) {
            throw new Exception\ParameterException('Parameter is not defined !');
        }

        return $this->parametersByPosition[$position];
    }
}
