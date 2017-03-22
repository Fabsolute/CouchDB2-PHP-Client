<?php
/**
 * Created by PhpStorm.
 * User: ahmetturk
 * Date: 19/03/2017
 * Time: 09:04
 */

namespace Fabs\CouchDB2\Response;


use Fabs\CouchDB2\Model\SerializableObject;

class ViewResponseElement extends SerializableObject
{
    protected $id = null;
    protected $key = null;
    protected $value = null;
    protected $doc = null;

    public function getValueWithType($type)
    {
        return SerializableObject::create($this->getValue(), $type);
    }

    public function getDocWithType($type)
    {
        return SerializableObject::create($this->getDoc(), $type);
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getDoc()
    {
        return $this->doc;
    }

    public function getID()
    {
        return $this->id;
    }

    public function getKey()
    {
        return $this->key;
    }
}