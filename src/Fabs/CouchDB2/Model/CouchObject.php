<?php
/**
 * Created by PhpStorm.
 * User: ahmetturk
 * Date: 19/03/2017
 * Time: 08:23
 */

namespace Fabs\CouchDB2\Model;

abstract class CouchObject extends SerializableObject
{
    /**
     * @var string
     */
    public $_id = null;

    /**
     * @var string
     */
    public $_rev = null;

    /**
     * @var array
     */
    private $cached_data = null;

    public function __construct()
    {
        parent::__construct();
        $this->nonSerialize('_id');
        $this->nonSerialize('_rev');
        $this->nonSerialize('cached_data');
    }

    public function serializeToArray()
    {
        foreach ($this as $key => $value) {
            if (strpos($key, '_') === 0 && ($key != '_id' && $key != '_rev')) {
                throw new \Exception('Variables cannot start with underscore');
            }
        }

        $output = parent::serializeToArray();

        if (strlen($this->_id) > 0) {
            $output['_id'] = $this->_id;
        }

        if (strlen($this->_rev) > 0) {
            $output['_rev'] = $this->_rev;
        }
        return $output;
    }

    public function deserializeFromArray($data)
    {
        parent::deserializeFromArray($data);
        $this->cached_data = $this->serializeToArray();
    }

    public function isChanged()
    {
        $output = $this->serializeToArray();
        if ($output != $this->cached_data) {
            return true;
        }
        return false;
    }
}