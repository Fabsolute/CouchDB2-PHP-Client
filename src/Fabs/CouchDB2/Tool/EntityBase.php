<?php

namespace Fabs\CouchDB2\Tool;

use Fabs\CouchDB2\Model\CouchObject;

abstract class EntityBase extends CouchObject
{
    /** @var int */
    public $created_at_timestamp = 0;
    /** @var int */
    public $updated_at_timestamp = 0;
    /** @var bool */
    public $dummy = false;
    /** @var array */
    protected $cached_data;

    /**
     * BaseEntity constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->makeTransient('created_at_timestamp');
        $this->makeTransient('updated_at_timestamp');
        $this->makeTransient('cached_data');
    }

    /**
     * @return array|mixed
     */
    public function jsonSerialize()
    {
        $output = parent::jsonSerialize();

        if ($this->created_at_timestamp === 0) {
            $this->created_at_timestamp = DateTimeHandler::now();
        }
        $this->updated_at_timestamp = DateTimeHandler::now();

        $output['created_at_timestamp'] = $this->created_at_timestamp;
        $output['updated_at_timestamp'] = $this->updated_at_timestamp;

        return $output;
    }

    /**
     * @param array $data
     */
    public function deserializeFromArray($data)
    {
        parent::deserializeFromArray($data);

        if (isset($data['created_at_timestamp'])) {
            $this->created_at_timestamp = $data['created_at_timestamp'];
        }

        if (isset($data['updated_at_timestamp'])) {
            $this->updated_at_timestamp = $data['updated_at_timestamp'];
        }
    }

    /**
     * @return bool
     */
    public function isChanged()
    {
        $output = $this->getCleanData();
        return $output != $this->cached_data;
    }

    /**
     * @return array
     */
    public function getChangedList()
    {
        if (!$this->isChanged()) {
            return [];
        }

        $new_data = $this->getCleanData();
        $cached_data = $this->cached_data;

        $diff = $this->arrayDiffRecursive($new_data, $cached_data);
        $diff_path_list = $this->createPath($diff);
        $changed_list = $this->selectLastPath($diff_path_list);

        $changed_list['updated_at_timestamp'] = $this->updated_at_timestamp;
        return $changed_list;
    }


    private function arrayDiffRecursive($new_data, $old_data)
    {
        $changed_list = [];

        foreach ($new_data as $key => $new_value) {
            if (array_key_exists($key, $old_data)) {
                $old_value = $old_data[$key];
                if ($new_value !== $old_value) {
                    if (is_array($new_value) && is_array($old_value) && count($new_value) > 0) {
                        $compare_diff = $this->arrayDiffRecursive($new_value, $old_value);
                        if (count($compare_diff) > 0) {
                            $changed_list[$key] = $compare_diff;
                        }
                    } else {
                        $changed_list[$key] = $new_value;
                    }
                }
            } else {
                $changed_list[$key] = $new_value;
            }
        }
        return $changed_list;
    }

    private function createPath($array, $key_accumulator = [])
    {
        $path_list = [];
        foreach ($array as $key => $value) {
            $merged_accumulator = array_merge($key_accumulator, [$key]);
            $array_key = implode('|', $merged_accumulator);
            if (is_array($value)) {
                $path_list[$array_key] = $this->createPath($value, $merged_accumulator);
            } else {
                $path_list[$array_key] = $value;
            }
        }
        return $path_list;
    }

    private function selectLastPath($array)
    {
        $path_list = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                if (count($value) > 0) {
                    $path_list = array_merge($path_list, $this->selectLastPath($value));
                } else {
                    $path_list[$key] = null;
                }
            } else {
                $path_list[$key] = $value;
            }
        }
        return $path_list;
    }


    public function updateCachedData()
    {
        $this->cached_data = $this->getCleanData();
    }

    /**
     * @return array|mixed
     */
    private function getCleanData()
    {
        $output = $this->jsonSerialize();
        unset($this->cached_data['updated_at_timestamp']);
        unset($this->cached_data['created_at_timestamp']);
        unset($output['updated_at_timestamp']);
        unset($output['created_at_timestamp']);
        return $output;
    }
}