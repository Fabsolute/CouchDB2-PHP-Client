<?php
/**
 * Created by PhpStorm.
 * User: ahmetturk
 * Date: 04/04/2017
 * Time: 11:08
 */

namespace Fabs\CouchDB2\Validation;


class IntegerValidation extends ValidationBase
{
    public function isValid($value)
    {
        if ($value == null) {
            if ($this->is_required) {
                return false;
            } else {
                return true;
            }
        }

        return is_int($value);
    }

    public function getName()
    {
        return 'integer';
    }
}