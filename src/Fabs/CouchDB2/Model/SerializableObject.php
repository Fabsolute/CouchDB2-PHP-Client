<?php
/**
 * Created by PhpStorm.
 * User: ahmetturk
 * Date: 19/03/2017
 * Time: 08:29
 */

namespace Fabs\CouchDB2\Model;

use Fabs\CouchDB2\Validation\BooleanValidation;
use Fabs\CouchDB2\Validation\FloatValidation;
use Fabs\CouchDB2\Validation\IntegerValidation;
use Fabs\CouchDB2\Validation\ObjectValidation;
use Fabs\CouchDB2\Validation\StringValidation;
use Fabs\CouchDB2\Validation\ValidationBase;
use Fabs\CouchDB2\Validation\ValidationException;

abstract class SerializableObject implements \JsonSerializable
{
    private $serializable_object_registered_properties = [];
    private $serializable_object_non_serializable_properties = [];
    /**
     * @var ValidationBase[]
     */
    private $serializable_object_validations = [];

    public function __construct()
    {
        $this->nonSerialize('serializable_object_non_serializable_properties');
        $this->nonSerialize('serializable_object_registered_properties');
        $this->nonSerialize('serializable_object_validations');
    }

    public function jsonSerialize()
    {
        return $this->serializeToArray();
    }

    public function serializeToArray()
    {
        foreach ($this as $key => $value) {
            if (array_key_exists($key, $this->serializable_object_validations)) {
                $validation_failed = false;
                $validation = $this->serializable_object_validations[$key];
                if ($validation->isArray()) {
                    if (!is_array($value)) {
                        $validation_failed = true;
                    } else {
                        foreach ($value as $value2) {
                            if (!$validation->isValid($value2)) {
                                $validation_failed = true;
                            }
                        }
                    }
                } else if (!$validation->isValid($value)) {
                    $validation_failed = true;
                }

                if ($validation_failed) {
                    throw new ValidationException(get_class($this), $key, $validation->getName());
                }
            }
        }

        $output = [];

        foreach ($this as $key => $value) {

            if (in_array($key, $this->serializable_object_non_serializable_properties)) {
                continue;
            }

            if ($value instanceof SerializableObject) {
                $output[$key] = $value->serializeToArray();
            } else {
                $output[$key] = $value;
            }
        }

        return $output;
    }

    public function deserializeFromArray($data)
    {
        foreach ($data as $key => $value) {
            if (in_array($key, $this->serializable_object_non_serializable_properties)) {
                continue;
            }

            if (property_exists($this, $key)) {
                if (array_key_exists($key, $this->serializable_object_registered_properties)) {
                    $registered_type = $this->serializable_object_registered_properties[$key];
                    if ($registered_type['is_array'] === false) {
                        $this->$key = self::create($value, $registered_type['class_name']);
                    } else {
                        $this->$key = [];
                        foreach ($value as $object_key => $object_value) {
                            $this->$key[] = self::create($object_value, $registered_type['class_name']);
                        }
                    }
                } else {
                    $this->$key = $value;
                }
            }
        }

        foreach ($this as $key => $value) {
            if (array_key_exists($key, $this->serializable_object_validations)) {

                $validation_failed = false;
                $validation = $this->serializable_object_validations[$key];

                if ($validation->isArray()) {
                    if (!is_array($value)) {
                        $validation_failed = true;
                    } else {
                        foreach ($value as $value2) {
                            if (!$validation->isValid($value2)) {
                                $validation_failed = true;
                            }
                        }
                    }
                } else if (!$validation->isValid($value)) {
                    $validation_failed = true;
                }

                if ($validation_failed) {
                    throw new ValidationException(get_class($this), $key, $validation->getName());
                }
            }
        }
    }

    protected function registerProperty($property_name, $class_name, $is_array = false)
    {
        if (!property_exists($this, $property_name)) {
            throw  new \InvalidArgumentException("Variable {$property_name} not found in class " . __CLASS__);
        }
        $this->serializable_object_registered_properties[$property_name] = ['class_name' => $class_name, 'is_array' => $is_array];

        $validation = $this->addObjectValidation($property_name);
        if ($is_array) {
            $validation->isArray();
        }

        return $validation;
    }

    protected function nonSerialize($property_name)
    {
        $this->serializable_object_non_serializable_properties[] = $property_name;
        return $this;
    }

    public static function create($data, $class_name)
    {
        if ($data == null) {
            return null;
        }
        if (!is_array($data)) {
            throw new \InvalidArgumentException('data must be an array');
        }

        if (!class_exists($class_name)) {
            throw new \InvalidArgumentException("class_name {$class_name} cannot found on namespace");
        }

        try {
            $output = new $class_name();
            if ($output instanceof SerializableObject) {
                $output->deserializeFromArray($data);
            }
            return $output;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @param $data
     * @return SerializableObject
     */
    public static function deserialize($data)
    {
        $class_name = static::class;
        return self::create($data, $class_name);
    }

    protected function addIntegerValidation($property_name)
    {
        $validation = new IntegerValidation();
        $this->serializable_object_validations[$property_name] = $validation;
        return $validation;
    }

    protected function addStringValidation($property_name)
    {
        $validation = new StringValidation();
        $this->serializable_object_validations[$property_name] = $validation;
        return $validation;
    }

    protected function addFloatValidation($property_name)
    {
        $validation = new FloatValidation();
        $this->serializable_object_validations[$property_name] = $validation;
        return $validation;
    }

    protected function addObjectValidation($property_name)
    {
        $validation = new ObjectValidation();
        $this->serializable_object_validations[$property_name] = $validation;
        return $validation;
    }

    protected function addBooleanValidation($property_name)
    {
        $validation = new BooleanValidation();
        $this->serializable_object_validations[$property_name] = $validation;
        return $validation;
    }
}