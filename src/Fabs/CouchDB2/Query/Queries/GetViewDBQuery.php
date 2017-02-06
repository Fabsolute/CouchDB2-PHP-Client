<?php
/**
 * Created by PhpStorm.
 * User: fabsolutely
 * Date: 06/02/2017
 * Time: 12:13
 */

namespace Fabs\CouchDB2\Query\Queries;

use Fabs\CouchDB2\Query\DBQuery;
use Fabs\CouchDB2\Query\QueryMethods;
use Fabs\CouchDB2\Query\QueryStatusCodes;

class GetViewDBQuery extends DBQuery
{
    public function __construct($couch_object, $database_name, $view_id, $view_name)
    {
        $this->reset();
        $this->execution_method = 'get_view';
        $this->query_method = QueryMethods::GET;
        $this->allowed_response_codes = [QueryStatusCodes::SUCCESS];
        $this->query_url = sprintf('_design/%s/_view/%s', $view_id, $view_name);
        parent::__construct($couch_object, $database_name);
    }

    /**
     * @param $value
     * @return GetViewDBQuery
     */
    public function conflicts($value)
    {
        return $this->set_query_params('conflicts', $value, 'json_encode_boolean');
    }

    /**
     * @param $value
     * @return GetViewDBQuery
     */
    public function descending($value)
    {
        return $this->set_query_params('descending', $value, 'json_encode_boolean');
    }

    /**
     * @param $value
     * @return GetViewDBQuery
     */
    public function end_key($value)
    {
        return $this->set_query_params('end_key', $value, 'json_encode');
    }

    /**
     * @param $value
     * @return GetViewDBQuery
     */
    public function end_key_doc_id($value)
    {
        return $this->set_query_params('end_key_doc_id', $value, 'json_encode');
    }

    /**
     * @param $value
     * @return GetViewDBQuery
     */
    public function group($value)
    {
        return $this->set_query_params('group', $value, 'json_encode_boolean');
    }

    /**
     * @param $value
     * @return GetViewDBQuery
     */
    public function group_level($value)
    {
        return $this->set_query_params('group_level', $value, 'int');
    }

    /**
     * @param $value
     * @return GetViewDBQuery
     */
    public function include_docs($value)
    {
        return $this->set_query_params('include_docs', $value, 'json_encode_boolean');
    }

    /**
     * @param $value
     * @return GetViewDBQuery
     */
    public function attachments($value)
    {
        return $this->set_query_params('attachments', $value, 'json_encode_boolean');
    }

    /**
     * @param $value
     * @return GetViewDBQuery
     */
    public function att_encoding_info($value)
    {
        return $this->set_query_params('att_encoding_info', $value, 'json_encode_boolean');
    }

    /**
     * @param $value
     * @return GetViewDBQuery
     */
    public function inclusive_end($value)
    {
        return $this->set_query_params('inclusive_end', $value, 'json_encode_boolean');
    }

    /**
     * @param $value
     * @return GetViewDBQuery
     */
    public function key($value)
    {
        return $this->set_query_params('key', $value, 'json_encode');
    }

    /**
     * @param $value
     * @return GetViewDBQuery
     */
    public function keys($value)
    {
        $this->query_data['keys'] = $value;
        return $this;
    }

    /**
     * @param $value
     * @return GetViewDBQuery
     */
    public function add_key($value)
    {
        if (!isset($this->query_data['keys'])) {
            $this->query_data['keys'] = [];
        }

        $this->query_data['keys'][] = $value;
        return $this;
    }

    /**
     * @param $value
     * @return GetViewDBQuery $this
     */
    public function limit($value)
    {
        return $this->set_query_params('limit', $value, 'int');
    }

    /**
     * @param $value
     * @return GetViewDBQuery
     */
    public function reduce($value)
    {
        return $this->set_query_params('reduce', $value, 'json_encode_boolean');
    }

    /**
     * @param $value
     * @return GetViewDBQuery
     */
    public function skip($value)
    {
        return $this->set_query_params('skip', $value, 'int');
    }

    /**
     * @param $value
     * @return GetViewDBQuery
     */
    public function sorted($value)
    {
        return $this->set_query_params('sorted', $value, 'json_encode_boolean');
    }

    /**
     * @param $value
     * @return GetViewDBQuery
     */
    public function stable($value)
    {
        return $this->set_query_params('stable', $value, 'json_encode_boolean');
    }

    /**
     * @param $value
     * @return GetViewDBQuery
     */
    public function stale($value)
    {
        return $this->set_query_params('stale', $value, 'string');
    }

    /**
     * @param $value
     * @return GetViewDBQuery
     */
    public function start_key($value)
    {
        return $this->set_query_params('start_key', $value, 'json_encode');
    }

    /**
     * @param $value
     * @return GetViewDBQuery
     */
    public function start_key_doc_id($value)
    {
        return $this->set_query_params('start_key_doc_id', $value, 'json_encode');
    }

    /**
     * @param $value
     * @return GetViewDBQuery
     */
    public function update($value)
    {
        return $this->set_query_params('update', $value, 'string');
    }

    /**
     * @param $value
     * @return GetViewDBQuery
     */
    public function update_seq($value)
    {
        return $this->set_query_params('update_seq', $value, 'json_encode_boolean');
    }

    public function execute()
    {
        if (isset($this->query_data['keys']) && count($this->query_data['keys'])) {
            $this->query_method = QueryMethods::POST;
        }
        return parent::execute();
    }

}