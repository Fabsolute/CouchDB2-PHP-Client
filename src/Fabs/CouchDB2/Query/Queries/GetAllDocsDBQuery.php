<?php
/**
 * Created by PhpStorm.
 * User: fabsolutely
 * Date: 06/02/2017
 * Time: 09:08
 */

namespace Fabs\CouchDB2\Query\Queries;


use Fabs\CouchDB2\Query\DBQuery;
use Fabs\CouchDB2\Query\QueryMethods;
use Fabs\CouchDB2\Query\QueryStatusCodes;
use Fabs\CouchDB2\Response\ViewResponse;

class GetAllDocsDBQuery extends DBQuery
{
    public function __construct($couch_object, $database_name)
    {
        $this->reset();
        $this->query_url = '_all_docs';
        $this->query_method = QueryMethods::GET;
        $this->allowed_response_codes = [QueryStatusCodes::SUCCESS];
        parent::__construct($couch_object, $database_name);
    }

    public function setIfNoneMatch($value)
    {
        if (is_string($value)) {
            $this->query_headers['If-None-Match'] = $value;
        }
        return $this;
    }

    public function setConflicts($value)
    {
        return $this->setQueryParameters('conflicts', $value, 'json_encode_boolean');
    }

    public function setDescending($value)
    {
        return $this->setQueryParameters('descending', $value, 'json_encode_boolean');
    }

    /**
     * @param $value
     * @return GetAllDocsDBQuery
     */
    public function setEndKey($value)
    {
        return $this->setQueryParameters('end_key', $value, 'json_encode');
    }

    /**
     * @param $value
     * @return GetAllDocsDBQuery
     */
    public function setEndKeyDocID($value)
    {
        return $this->setQueryParameters('end_key_doc_id', $value, 'json_encode');
    }

    /**
     * @param $value
     * @return GetAllDocsDBQuery
     */
    public function setStartKey($value)
    {
        return $this->setQueryParameters('start_key', $value, 'json_encode');
    }

    /**
     * @param $value
     * @return GetAllDocsDBQuery
     */
    public function setStartKeyDocID($value)
    {
        return $this->setQueryParameters('start_key_doc_id', $value, 'json_encode');
    }

    /**
     * @param $value
     * @return GetAllDocsDBQuery
     */
    public function setInclusiveEnd($value)
    {
        return $this->setQueryParameters('inclusive_end', $value, 'json_encode_boolean');
    }

    /**
     * @param $value
     * @return GetAllDocsDBQuery
     */
    public function setIncludeDocs($value)
    {
        return $this->setQueryParameters('include_docs', $value, 'json_encode_boolean');
    }

    /**
     * @param $value
     * @return GetAllDocsDBQuery
     */
    public function setKeys($value)
    {
        $this->query_data['keys'] = array_values($value);
        return $this;
    }

    /**
     * @param $value
     * @return GetAllDocsDBQuery
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
     * @return GetAllDocsDBQuery $this
     */
    public function setLimit($value)
    {
        return $this->setQueryParameters('limit', $value, 'int');
    }

    /**
     * @param $value
     * @return GetAllDocsDBQuery
     */
    public function setSkip($value)
    {
        return $this->setQueryParameters('skip', $value, 'int');
    }

    /**
     * @param $value
     * @return GetAllDocsDBQuery
     */
    public function setStale($value)
    {
        return $this->setQueryParameters('stale', $value, 'string');
    }

    /**
     * @param $value
     * @return GetAllDocsDBQuery
     */
    public function setUpdateSeq($value)
    {
        return $this->setQueryParameters('update_seq', $value, 'json_encode_boolean');
    }

    /**
     * @return SerializableObject|ViewResponse
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
        return ViewResponse::deserialize($response->getRawData());
    }

}