<?php
/**
 * Created by PhpStorm.
 * User: ahmetturk
 * Date: 20/03/2017
 * Time: 10:16
 */

namespace Fabs\CouchDB2\Response;


use Fabs\CouchDB2\Model\SerializableObject;

class DocumentResponseElement extends SerializableObject
{
    protected $id = null;
    protected $rev = null;
    protected $ok = null;

    public function getID()
    {
        return $this->id;
    }

    public function getRev()
    {
        return $this->rev;
    }
}