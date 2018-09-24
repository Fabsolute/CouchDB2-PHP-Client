<?php
/**
 * Created by PhpStorm.
 * User: ahmetturk
 * Date: 19/03/2017
 * Time: 08:23
 */

namespace Fabs\CouchDB2\Model;

use Fabs\Serialize\SerializableObject;

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
        $this->makeTransient('_id');
        $this->makeTransient('_rev');
        $this->makeTransient('cached_data');
    }

    public function jsonSerialize()
    {
        foreach ($this as $key => $value) {
            if (strpos($key, '_') === 0 && in_array($key, $this->getAllowedPropertiesStatWithUnderscore(), true) === false) {
                throw new \Exception('Variables cannot start with underscore');
            }
        }

        $output = parent::jsonSerialize();

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

        if (isset($data['_id'])) {
            $this->_id = $data['_id'];
        }

        if (isset($data['_rev'])) {
            $this->_rev = $data['_rev'];
        }

        $this->cached_data = $this->jsonSerialize();
    }

    public function isChanged()
    {
        $output = $this->jsonSerialize();
        if ($output != $this->cached_data) {
            return true;
        }
        return false;
    }

    protected final function getAllowedPropertiesStatWithUnderscore()
    {
        return [
            '_id',
            '_rev',
            '_replication_state',
            '_replication_state_time',
            '_replication_id',
            '_replication_state_reason'
        ];
    }
}