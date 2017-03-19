<?php
/**
 * Created by PhpStorm.
 * User: ahmetturk
 * Date: 19/03/2017
 * Time: 08:29
 */

namespace Fabs\CouchDB2\Model;

abstract class SerializableObject implements \JsonSerializable
{
    private $registered_properties = [];
    private $non_serializable_properties = [];

    public function __construct()
    {
        $this->nonSerialize('registered_properties');
        $this->nonSerialize('non_serializable_properties');
    }

    public function jsonSerialize()
    {
        return $this->serializeToArray();
    }

    public function serializeToArray()
    {
        $output = [];

        foreach ($this as $key => $value) {

            if (in_array($key, $this->non_serializable_properties)) {
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
            if (property_exists($this, $key)) {
                if (array_key_exists($key, $this->registered_properties)) {
                    $registered_type = $this->registered_properties[$key];
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
    }

    protected function registerProperty($property_name, $class_name, $is_array = false)
    {
        if (!property_exists($this, $property_name)) {
            throw  new \InvalidArgumentException("Variable {$property_name} not found in class " . __CLASS__);
        }
        $this->registered_properties[$property_name] = ['class_name' => $class_name, 'is_array' => $is_array];
        return $this;
    }

    protected function nonSerialize($property_name)
    {
        $this->non_serializable_properties[] = $property_name;
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
     * @return self
     */
    public static function deserialize($data)
    {
        $class_name = static::class;
        return self::create($data, $class_name);
    }
}