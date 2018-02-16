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
    
    protected $allowed_properties_start_with_underscore = [];

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
            if (strpos($key, '_') === 0 && in_array($key, $this->allowed_properties_start_with_underscore) === false) {
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
    
    private function setAllowedPropertiesStartWithUnderscore()
    {
        $this->allowed_properties_start_with_underscore =
            [
                '_id',
                '_rev',
                '_replication_state',
                '_replication_state_time',
                '_replication_id',
                '_replication_state_reason'
            ];
    }
}
