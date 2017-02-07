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

    public function setIfNoneMatch($value)
    {
        if (is_string($value)) {
            $this->query_headers['If-None-Match'] = $value;
        }
        return $this;
    }

    public function setAttachments($value)
    {
        return $this->setQueryParameters('attachments', $value, 'json_encode_boolean');
    }

    public function setAttEncodingInfo($value)
    {
        return $this->setQueryParameters('set_att_encoding_info', $value, 'json_encode_boolean');
    }

    public function setAttsSince($value)
    {
        return $this->setQueryParameters('atts_since', $value, 'ensure_array');
    }

    public function setConflicts($value)
    {
        return $this->setQueryParameters('conflicts', $value, 'json_encode_boolean');
    }

    public function setDeletedConflicts($value)
    {
        return $this->setQueryParameters('deleted_conflicts', $value, 'json_encode_boolean');
    }

    public function setLatest($value)
    {
        return $this->setQueryParameters('latest', $value, 'json_encode_boolean');
    }

    public function setLocalSeq($value)
    {
        return $this->setQueryParameters('local_seq', $value, 'json_encode_boolean');
    }

    public function setMeta($value)
    {
        return $this->setQueryParameters('meta', $value, 'json_encode_boolean');
    }

    public function setOpenRevs($value)
    {
        return $this->setQueryParameters('open_revs', $value, 'ensure_array');
    }

    public function setRev($value)
    {
        return $this->setQueryParameters('rev', $value, 'string');
    }

    public function setRevs($value)
    {
        return $this->setQueryParameters('revs', $value, 'json_encode_boolean');
    }

    public function setRevsInfo($value)
    {
        return $this->setQueryParameters('revs_info', $value, 'json_encode_boolean');
    }
}