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
use Fabs\CouchDB2\Response\ViewResponse;

class GetViewDBQuery extends DBQuery
{
    public function __construct($couch_object, $database_name, $design_doc_id, $view_name)
    {
        $this->reset();
        $this->execution_method = 'get_view';
        $this->query_method = QueryMethods::GET;
        $this->allowed_response_codes = [QueryStatusCodes::SUCCESS];
        $this->query_url = sprintf('_design/%s/_view/%s', $design_doc_id, $view_name);
        parent::__construct($couch_object, $database_name);
    }

    /**
     * @param $value
     * @return GetViewDBQuery
     */
    public function setConflicts($value)
    {
        return $this->setQueryParameters('conflicts', $value, 'json_encode_boolean');
    }

    /**
     * @param $value
     * @return GetViewDBQuery
     */
    public function setDescending($value)
    {
        return $this->setQueryParameters('descending', $value, 'json_encode_boolean');
    }

    /**
     * @param $value
     * @return GetViewDBQuery
     */
    public function setEndKey($value)
    {
        return $this->setQueryParameters('end_key', $value, 'json_encode');
    }

    /**
     * @param $value
     * @return GetViewDBQuery
     */
    public function setEndKeyDocID($value)
    {
        return $this->setQueryParameters('end_key_doc_id', $value, 'json_encode');
    }

    /**
     * @param $value
     * @return GetViewDBQuery
     */
    public function setGroup($value)
    {
        return $this->setQueryParameters('group', $value, 'json_encode_boolean');
    }

    /**
     * @param $value
     * @return GetViewDBQuery
     */
    public function setGroupLevel($value)
    {
        return $this->setQueryParameters('group_level', $value, 'int');
    }

    /**
     * @param $value
     * @return GetViewDBQuery
     */
    public function setIncludeDocs($value)
    {
        return $this->setQueryParameters('include_docs', $value, 'json_encode_boolean');
    }

    /**
     * @param $value
     * @return GetViewDBQuery
     */
    public function setAttachments($value)
    {
        return $this->setQueryParameters('attachments', $value, 'json_encode_boolean');
    }

    /**
     * @param $value
     * @return GetViewDBQuery
     */
    public function setAttEncodingInfo($value)
    {
        return $this->setQueryParameters('att_encoding_info', $value, 'json_encode_boolean');
    }

    /**
     * @param $value
     * @return GetViewDBQuery
     */
    public function setInclusiveEnd($value)
    {
        return $this->setQueryParameters('inclusive_end', $value, 'json_encode_boolean');
    }

    /**
     * @param $value
     * @return GetViewDBQuery
     */
    public function setKeys($value)
    {
        $this->query_data['keys'] = $value;
        return $this;
    }

    /**
     * @param $value
     * @return GetViewDBQuery
     */
    public function addKey($value)
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
    public function setLimit($value)
    {
        return $this->setQueryParameters('limit', $value, 'int');
    }

    /**
     * @param $value
     * @return GetViewDBQuery
     */
    public function setReduce($value)
    {
        return $this->setQueryParameters('reduce', $value, 'json_encode_boolean');
    }

    /**
     * @param $value
     * @return GetViewDBQuery
     */
    public function setSkip($value)
    {
        return $this->setQueryParameters('skip', $value, 'int');
    }

    /**
     * @param $value
     * @return GetViewDBQuery
     */
    public function setSorted($value)
    {
        return $this->setQueryParameters('sorted', $value, 'json_encode_boolean');
    }

    /**
     * @param $value
     * @return GetViewDBQuery
     */
    public function setStable($value)
    {
        return $this->setQueryParameters('stable', $value, 'json_encode_boolean');
    }

    /**
     * @param $value
     * @return GetViewDBQuery
     */
    public function setStale($value)
    {
        return $this->setQueryParameters('stale', $value, 'string');
    }

    /**
     * @param $value
     * @return GetViewDBQuery
     */
    public function setStartKey($value)
    {
        return $this->setQueryParameters('start_key', $value, 'json_encode');
    }

    /**
     * @param $value
     * @return GetViewDBQuery
     */
    public function setStartKeyDocID($value)
    {
        return $this->setQueryParameters('start_key_doc_id', $value, 'json_encode');
    }

    /**
     * @param $value
     * @return GetViewDBQuery
     */
    public function setUpdate($value)
    {
        return $this->setQueryParameters('update', $value, 'string');
    }

    /**
     * @param $value
     * @return GetViewDBQuery
     */
    public function setUpdateSeq($value)
    {
        return $this->setQueryParameters('update_seq', $value, 'json_encode_boolean');
    }

    /**
     * @return ViewResponse
     */
    public function execute()
    {
        if (isset($this->query_data['keys'])) {
            if (count($this->query_data['keys']) > 1) {
                $this->query_method = QueryMethods::POST;
            } elseif (count($this->query_data['keys'])) {
                $this->setQueryParameters('key', $this->query_data['keys'][0], 'json_encode');
                unset($this->query_data['keys']);
            } else {
                unset($this->query_data['keys']);
            }
        }
        $response = parent::execute();
        return new ViewResponse($response->getRawData());
    }
}