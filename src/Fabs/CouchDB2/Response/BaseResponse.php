<?php

/**
 * Created by PhpStorm.
 * User: ahmetturk
 * Date: 19/03/2017
 * Time: 08:58
 */
namespace Fabs\CouchDB2\Response;

use Fabs\CouchDB2\Model\SerializableObject;

class BaseResponse extends SerializableObject
{
    private $data = null;

    public function __construct()
    {
        parent::__construct();
        $this->nonSerialize('data');
    }

    public function deserializeFromArray($data)
    {
        parent::deserializeFromArray($data);
        $this->data = $data;
    }

    public function getRawData()
    {
        return $this->data;
    }

    public function toObject($type)
    {
        return SerializableObject::create($this->getRawData(), $type);
    }
}