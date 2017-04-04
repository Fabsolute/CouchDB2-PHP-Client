<?php
/**
 * Created by PhpStorm.
 * User: ahmetturk
 * Date: 04/04/2017
 * Time: 11:19
 */

namespace Fabs\CouchDB2\Validation;

use Fabs\CouchDB2\Model\SerializableObject;

class ObjectValidation extends ValidationBase
{
    /** @var string */
    protected $type = null;

    /**
     * @param $type string
     * @return ObjectValidation
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    public function isValid($value)
    {
        if ($value == null) {
            if ($this->is_required) {
                return false;
            } else {
                return true;
            }
        }

        return is_a($value, $this->type);
    }

    public function getName()
    {
        return $this->type;
    }
}
