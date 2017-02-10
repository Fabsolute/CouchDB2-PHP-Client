<?php
/**
 * Created by PhpStorm.
 * User: fabsolutely
 * Date: 03/02/2017
 * Time: 17:01
 */

namespace Fabs\CouchDB2\Query\Queries;

use Fabs\CouchDB2\Couch;
use Fabs\CouchDB2\Query\DBQuery;
use Fabs\CouchDB2\Query\QueryMethods;
use Fabs\CouchDB2\Query\QueryStatusCodes;

class GetChangesDBQuery extends DBQuery
{
    public function __construct($couch_object, $database_name)
    {
        $this->allowed_response_codes = [QueryStatusCodes::SUCCESS];
        $this->query_method = QueryMethods::GET;

        parent::__construct($couch_object, $database_name);
    }

    public function setContinuous()
    {
        return new GetContinuousChangesDBQuery($this->couch_object, $this);
    }

    public function setDocIDs($array)
    {
        return $this->setQueryParameters('doc_ids', $array, 'ensure_array');
    }

    public function setConflicts($value)
    {
        return $this->setQueryParameters('conflicts', $value, 'json_encode_boolean');
    }

    public function setDescending($value)
    {
        return  $this->setQueryParameters('descending', $value, 'json_encode_boolean');
    }

    public function setFeed($value)
    {
        switch ($value) {
            case 'normal':
            case 'longpoll':
                return $this->setQueryParameters('feed', $value, 'string');
                break;
            case 'continuous':
                throw new \Exception('Must use set_continuous() method when feed is continuous.');
                break;
            default:
                throw new \Exception('Supported feed types : normal, longpoll, continuous.');
                break;
        }
    }

    public function setFilter($value)
    {
        return $this->setQueryParameters('feed', $value, 'string');
    }

    public function setHeartbeat($value)
    {
        return $this->setQueryParameters('heartbeat', $value, 'int');
    }

    public function setIncludeDocs($value)
    {
        return $this->setQueryParameters('include_docs', $value, 'json_encode_boolean');
    }

    public function setAttachments($value)
    {
        return $this->setQueryParameters('attachments', $value, 'json_encode_boolean');
    }

    public function setAttEncodingInfo($value)
    {
        return $this->setQueryParameters('att_encoding_info', $value, 'json_encode_boolean');
    }

    public function setLastEventID($value)
    {
        return $this->setQueryParameters('last-event-id', $value, 'int');
    }

    public function setLimit($value)
    {
        return $this->setQueryParameters('limit', $value, 'int');
    }

    public function setSince($value)
    {
        return $this->setQueryParameters('since', $value, 'int');
    }

    public function setStyle($value)
    {
        return $this->setQueryParameters('style', $value, 'string');
    }

    public function setChangesTimeout($value)
    {
        return $this->setQueryParameters('timeout', $value, 'int');
    }

    public function setView($value)
    {
        return $this->setQueryParameters('view', $value, 'string');
    }
}