<?php

/**
 * Created by PhpStorm.
 * User: ahmetturk
 * Date: 19/03/2017
 * Time: 08:58
 */
namespace Fabs\CouchDB2\Response;

use Fabs\Serialize\SerializableObject;

class BaseResponse extends SerializableObject
{
    private $data = null;

    public function __construct()
    {
        parent::__construct();
        $this->makeTransient('data');
    }

    public function deserializeFromArray($data)
    {
        parent::deserializeFromArray($data);
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getRawData()
    {
        return $this->data;
    }

    /**
     * @param $type
     * @return SerializableObject
     */
    public function toObject($type)
    {
        return SerializableObject::create($this->getRawData(), $type);
    }
}