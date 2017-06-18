<?php
/**
 * Created by PhpStorm.
 * User: ahmetturk
 * Date: 19/03/2017
 * Time: 09:04
 */

namespace Fabs\CouchDB2\Response;

use Fabs\Serialize\SerializableObject;

class ViewResponseElement extends SerializableObject
{
    protected $id = null;
    protected $key = null;
    protected $value = null;
    protected $doc = null;

    /**
     * @param $type string
     * @return SerializableObject
     */
    public function getValueWithType($type)
    {
        return SerializableObject::create($this->getValue(), $type);
    }

    /**
     * @param $type string
     * @return SerializableObject
     */
    public function getDocWithType($type)
    {
        return SerializableObject::create($this->getDoc(), $type);
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return array
     */
    public function getDoc()
    {
        return $this->doc;
    }

    /**
     * @return string
     */
    public function getID()
    {
        return $this->id;
    }

    /**
     * @return array|string
     */
    public function getKey()
    {
        return $this->key;
    }
}