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

    public function set_continuous()
    {
        return new GetContinuousChangesDBQuery($this->couch_object, $this);
    }

    public function doc_ids($array)
    {
        return $this->set_query_params('doc_ids', $array, 'ensure_array');
    }

    public function conflicts($value)
    {
        return $this->set_query_params('conflicts', $value, 'json_encode_boolean');
    }

    public function descending($value)
    {
        return  $this->set_query_params('descending', $value, 'json_encode_boolean');
    }

    public function feed($value)
    {
        switch ($value) {
            case 'normal':
            case 'longpoll':
                return $this->set_query_params('feed', $value, 'string');
                break;
            case 'continuous':
                throw new \Exception('Must use set_continuous() method when feed is continuous.');
                break;
            default:
                throw new \Exception('Supported feed types : normal, longpoll, continuous.');
                break;
        }
    }

    public function filter($value)
    {
        return $this->set_query_params('feed', $value, 'string');
    }

    public function heartbeat($value)
    {
        return $this->set_query_params('heartbeat', $value, 'int');
    }

    public function include_docs($value)
    {
        return $this->set_query_params('include_docs', $value, 'json_encode_boolean');
    }

    public function attachments($value)
    {
        return $this->set_query_params('attachments', $value, 'json_encode_boolean');
    }

    public function att_encoding_info($value)
    {
        return $this->set_query_params('att_encoding_info', $value, 'json_encode_boolean');
    }

    public function last_event_id($value)
    {
        return $this->set_query_params('last-event-id', $value, 'int');
    }

    public function limit($value)
    {
        return $this->set_query_params('limit', $value, 'int');
    }

    public function since($value)
    {
        return $this->set_query_params('since', $value, 'int');
    }

    public function style($value)
    {
        return $this->set_query_params('style', $value, 'string');
    }

    public function changes_timeout($value)
    {
        return $this->set_query_params('timeout', $value, 'int');
    }

    public function view($value)
    {
        return $this->set_query_params('view', $value, 'string');
    }
}