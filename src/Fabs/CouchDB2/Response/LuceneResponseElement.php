<?php
/**
 * Created by PhpStorm.
 * User: ahmetturk
 * Date: 30/06/2017
 * Time: 12:19
 */

namespace Fabs\CouchDB2\Response;

use Fabs\Serialize\SerializableObject;

class LuceneResponseElement extends SerializableObject
{
    /** @var  float */
    protected $score = 0.0;
    /** @var  string */
    protected $id = null;
    /** @var mixed */
    protected $doc = null;

    /**
     * @return float
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getDoc()
    {
        return $this->doc;
    }

    /**
     * @param $type string
     * @return SerializableObject
     */
    public function getDocWithType($type)
    {
        return SerializableObject::create($this->getDoc(), $type);
    }
}