<?php

/**
 * Copyright (c) 2010-2017 Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eureka\Component\Routing;

/**
 * Route parameter class.
 *
 * @author Romain Cottard
 */
class Parameter
{
    /**
     * @var string TYPE_STRING Type string
     */
    const TYPE_STRING = '[\w_-]+';

    /**
     * @var string TYPE_INTEGER Type integer
     */
    const TYPE_INTEGER = '[0-9]+';

    /**
     * @var string TYPE_ANY Type any
     */
    const TYPE_ANY = '.+';

    /**
     * @var string $name Parameter name
     */
    protected $name = '';

    /**
     * @var string $type Parameter type
     */
    protected $type = '';

    /**
     * @var mixed $value Parameter value
     */
    protected $value = null;

    /**
     * @var bool $isMandatory
     */
    protected $isMandatory = true;

    /**
     * Parameter constructor.
     *
     * @param string $name
     * @param string $type
     * @param bool   $isMandatory
     */
    public function __construct($name, $type = Parameter::TYPE_STRING, $isMandatory = true)
    {
        $this->name        = (substr($name, 0, 1) !== ':' ? ':' : '') . $name;
        $this->type        = (string) $type;
        $this->isMandatory = $isMandatory;
    }

    /**
     * Get parameter name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get parameter type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get parameter value.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Get parameter is mandatory
     *
     * @return bool
     */
    public function isMandatory()
    {
        return $this->isMandatory;
    }

    /**
     * Set value.
     *
     * @param  string $value
     * @return Parameter
     */
    public function setValue($value)
    {
        switch ($this->type) {
            case Parameter::TYPE_INTEGER:
                $this->value = (int) $value;
                break;
            case Parameter::TYPE_STRING:
            case Parameter::TYPE_ANY:
            default:
                $this->value = (string) $value;
        }

        return $this;
    }
}