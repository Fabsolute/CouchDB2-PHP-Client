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

class GetDocDBQuery extends DBQuery
{
    public function __construct($couch_object, $database_name, $doc_id)
    {
        $this->reset();
        $this->execution_method = 'get_doc';
        $this->query_url = $doc_id;
        $this->query_method = QueryMethods::GET;
        $this->allowed_response_codes = [QueryStatusCodes::SUCCESS];
        parent::__construct($couch_object, $database_name);
    }

    public function set_if_none_match($value)
    {
        if (is_string($value)) {
            $this->query_headers['If-None-Match'] = $value;
        }
        return $this;
    }

    public function set_attachments($value)
    {
        return $this->set_query_params('attachments', $value, 'json_encode_boolean');
    }

    public function set_att_encoding_info($value)
    {
        return $this->set_query_params('set_att_encoding_info', $value, 'json_encode_boolean');
    }

    public function set_atts_since($value)
    {
        return $this->set_query_params('atts_since', $value, 'ensure_array');
    }

    public function set_conflicts($value)
    {
        return $this->set_query_params('conflicts', $value, 'json_encode_boolean');
    }

    public function set_deleted_conflicts($value)
    {
        return $this->set_query_params('deleted_conflicts', $value, 'json_encode_boolean');
    }

    public function set_latest($value)
    {
        return $this->set_query_params('latest', $value, 'json_encode_boolean');
    }

    public function set_local_seq($value)
    {
        return $this->set_query_params('local_seq', $value, 'json_encode_boolean');
    }

    public function set_meta($value)
    {
        return $this->set_query_params('meta', $value, 'json_encode_boolean');
    }

    public function set_open_revs($value)
    {
        return $this->set_query_params('open_revs', $value, 'ensure_array');
    }

    public function set_rev($value)
    {
        return $this->set_query_params('rev', $value, 'string');
    }

    public function set_revs($value)
    {
        return $this->set_query_params('revs', $value, 'json_encode_boolean');
    }

    public function set_revs_info($value)
    {
        return $this->set_query_params('revs_info', $value, 'json_encode_boolean');
    }
}