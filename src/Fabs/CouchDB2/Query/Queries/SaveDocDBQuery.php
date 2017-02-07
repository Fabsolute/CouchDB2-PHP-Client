<?php
/**
 * Created by PhpStorm.
 * User: fabsolutely
 * Date: 06/02/2017
 * Time: 06:51
 */

namespace Fabs\CouchDB2\Query\Queries;


use Fabs\CouchDB2\Query\DBQuery;
use Fabs\CouchDB2\Query\QueryMethods;
use Fabs\CouchDB2\Query\QueryStatusCodes;

class SaveDocDBQuery extends DBQuery
{
    protected $should_update = false;
    protected $query_doc;

    public function __construct($couch_object, $database_name, $doc)
    {
        $this->reset();
        $this->execution_method = 'save_doc';
        $this->query_doc = $doc;
        $doc = (array)$doc;
        $this->query_data = $doc;

        if (isset($doc['_id']) && !empty($doc['_id']) && isset($doc['_rev']) && !empty($doc['_rev'])) {
            $this->query_url = $doc['_id'];
            $this->query_method = QueryMethods::PUT;
            $this->allowed_response_codes = [QueryStatusCodes::CREATED];
        } else {
            $this->query_method = QueryMethods::POST;
            $this->allowed_response_codes = [QueryStatusCodes::CREATED];
        }
        parent::__construct($couch_object, $database_name);
    }

    public function setShouldUpdate($value)
    {
        if (is_bool($value)) {
            $this->should_update = $value;
        }
        return $this;
    }

    public function setIfMatch($value)
    {
        if (is_string($value)) {
            $this->query_headers['If-Match'] = $value;
        }
        return $this;
    }

    public function setDelayedCommitPolicy($value)
    {
        if (is_bool($value)) {
            $this->query_headers['X-Couch-Full-Commit'] = $value;
        }
        return $this;
    }

    public function setBatch($value)
    {
        return $this->setQueryParameters('batch', $value, 'string');
    }

    public function setNewEdits($value)
    {
        return $this->setQueryParameters('new_edits', $value, 'json_encode_bool');
    }

    public function execute()
    {
        $output = parent::execute();
        if ($this->should_update) {
            if (isset($output['_id']) && isset($output['_rev'])) {
                if ($this->query_doc != null) {
                    if (!is_a($this->query_doc, 'Fabs\\CouchDB2\\Http\\Response')) {
                        if (is_object($this->query_doc)) {
                            $this->query_doc->_id = $output['_id'];
                            $this->query_doc->_rev = $output['_rev'];
                        } else {
                            $this->query_doc['_id'] = $output['_id'];
                            $this->query_doc['_rev'] = $output['_rev'];
                        }
                        return $this->query_doc;
                    }
                }
            }
        }
        return $output;
    }

}