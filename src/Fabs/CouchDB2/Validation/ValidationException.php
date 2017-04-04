<?php
/**
 * Created by PhpStorm.
 * User: ahmetturk
 * Date: 04/04/2017
 * Time: 12:52
 */

namespace Fabs\CouchDB2\Validation;


class ValidationException extends \Exception
{
    protected $class_name;
    protected $property_name;
    protected $validator_name;

    public function __construct($class_name, $property_name, $validator_name)
    {
        $this->class_name = $class_name;
        $this->property_name = $property_name;
        $this->validator_name = $validator_name;

        parent::__construct('Validation failed', 0, null);
    }

    public function getClassName()
    {
        return $this->class_name;
    }

    public function getPropertyName()
    {
        return $this->property_name;
    }

    public function getValidatorName()
    {
        return $this->validator_name;
    }
}