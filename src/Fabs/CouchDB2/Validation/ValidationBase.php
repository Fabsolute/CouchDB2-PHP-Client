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

    public function isRequired()
    {
        $this->is_required = true;
        return $this;
    }

    public function isArray()
    {
        $this->is_array = true;
        return $this;
    }

    public function getIsRequired()
    {
        return $this->is_required;
    }

    public function getIsArray()
    {
        return $this->is_array;
    }

    public abstract function isValid($value);
    public abstract function getName();
}