<?php
/**
 * Created by PhpStorm.
 * User: ahmetturk
 * Date: 20/03/2017
 * Time: 10:29
 */

namespace Fabs\CouchDB2\Response;


use Fabs\CouchDB2\Model\SerializableObject;

class ChangesResponseElement extends SerializableObject
{
    protected $id = null;
    protected $changes = null;
    protected $seq = null;

    public function getChanges()
    {
        return $this->changes;
    }

    public function getID()
    {
        return $this->id;
    }

    public function getSeq()
    {
        return $this->seq;
    }
}