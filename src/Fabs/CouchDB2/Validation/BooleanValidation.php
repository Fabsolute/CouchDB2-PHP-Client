<?php
/**
 * Created by PhpStorm.
 * User: ahmetturk
 * Date: 04/04/2017
 * Time: 12:21
 */

namespace Fabs\CouchDB2\Validation;


class BooleanValidation extends ValidationBase
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
        return is_bool($value);
    }

    public function getName()
    {
        return 'boolean';
    }
}