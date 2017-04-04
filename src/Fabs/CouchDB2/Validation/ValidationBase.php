<?php

/**
 * Created by PhpStorm.
 * User: ahmetturk
 * Date: 04/04/2017
 * Time: 11:04
 */

namespace Fabs\CouchDB2\Validation;

abstract class ValidationBase
{
    protected $is_required = false;
    protected $is_array = false;

    /**
     * @return ValidationBase
     */
    public function isRequired()
    {
        $this->is_required = true;
        return $this;
    }

    /**
     * @return ValidationBase
     */
    public function isArray()
    {
        $this->is_array = true;
        return $this;
    }

    /**
     * @return bool
     */
    public function getIsRequired()
    {
        return $this->is_required;
    }

    /**
     * @return bool
     */
    public function getIsArray()
    {
        return $this->is_array;
    }

    /**
     * @param $value mixed
     * @return bool
     */
    public abstract function isValid($value);

    /**
     * @return string
     */
    public abstract function getName();
}