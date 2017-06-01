<?php
/**
 * Created by PhpStorm.
 * User: ahmetturk
 * Date: 01/06/2017
 * Time: 11:51
 */

namespace Fabs\CouchDB2\Model;


use Fabs\Serialize\SerializableObject;

class CouchError extends SerializableObject
{
    /** @var string */
    protected $error = null;
    /** @var string */
    protected $reason = null;

    /**
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }
}