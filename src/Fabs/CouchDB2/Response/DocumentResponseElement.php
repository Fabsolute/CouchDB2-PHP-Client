<?php
/**
 * Created by PhpStorm.
 * User: ahmetturk
 * Date: 20/03/2017
 * Time: 10:16
 */

namespace Fabs\CouchDB2\Response;

use Fabs\Serialize\SerializableObject;

class DocumentResponseElement extends SerializableObject
{
    protected $id = null;
    protected $rev = null;
    protected $ok = false;

    /**
     * @return string
     */
    public function getID()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getRev()
    {
        return $this->rev;
    }

    /**
     * @return bool
     * @author necipallef <necipallef@gmail.com>
     */
    public function getOK()
    {
        return $this->ok;
    }
}