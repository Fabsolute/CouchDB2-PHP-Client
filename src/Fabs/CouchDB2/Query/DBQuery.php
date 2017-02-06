<?php

/**
 * Created by PhpStorm.
 * User: fabsolutely
 * Date: 03/02/2017
 * Time: 07:06
 */
namespace Fabs\CouchDB2\Query;

use Fabs\CouchDB2\Query\Queries\BulkDocsDBQuery;
use Fabs\CouchDB2\Query\Queries\GetChangesDBQuery;
use Fabs\CouchDB2\Query\Queries\GetDocDBQuery;
use Fabs\CouchDB2\Query\Queries\GetViewDBQuery;
use Fabs\CouchDB2\Query\Queries\SaveDocDBQuery;

class DBQuery extends QueryBase
{
    protected $database_name = '';
    protected $execution_method = '';

    public function __construct($couch_object, $database_name)
    {
        $this->database_name = $database_name;
        parent::__construct($couch_object);
    }

    /**
     * @return string
     */
    public function get_database_name()
    {
        return $this->database_name;
    }

    /**
     * @return string
     */
    public function get_query_url()
    {
        $url = parent::get_query_url();
        return sprintf('%s/%s', $this->get_database_name(), $url);
    }

    public function execute()
    {
        $execution = parent::execute();
        if ($this->execution_method == 'exists') {
            if ($execution->status_code == 200) {
                return true;
            }
            return false;
        }
        return $execution;
    }


    public function exists()
    {
        $this->reset();
        $this->execution_method = 'exists';
        $this->allowed_response_codes = [QueryStatusCodes::SUCCESS, QueryStatusCodes::NOT_FOUND];
        $this->query_method = QueryMethods::HEAD;
        return $this;
    }

    public function doc_exists($doc_id)
    {
        $this->reset();
        $this->execution_method = 'exists';
        $this->query_url = $doc_id;
        $this->allowed_response_codes = [QueryStatusCodes::SUCCESS, QueryStatusCodes::NOT_FOUND];
        $this->query_method = QueryMethods::HEAD;
        return $this;
    }

    public function compact($design_doc = null)
    {
        $this->reset();
        $this->execution_method = 'compact';
        if (!empty($design_doc)) {
            $this->query_url = sprintf('%s/%s', '_compact', $design_doc);
        } else {
            $this->query_url = '_compact';
        }
        $this->query_method = QueryMethods::POST;
        $this->allowed_response_codes = [QueryStatusCodes::ACCEPTED];
        return $this;
    }

    /**
     * @return DBQuery
     */
    public function get_info()
    {
        $this->reset();
        $this->execution_method = 'get_info';
        $this->query_method = QueryMethods::GET;
        $this->allowed_response_codes = [QueryStatusCodes::SUCCESS];
        return $this;
    }

    public function cleanup_views()
    {
        $this->reset();
        $this->execution_method = 'cleanup_views';
        $this->query_url = '_view_cleanup';
        $this->query_method = QueryMethods::POST;
        $this->allowed_response_codes = [QueryStatusCodes::ACCEPTED];
        return $this;
    }

    public function ensure_full_commit()
    {
        $this->reset();
        $this->execution_method = 'ensure_full_commit';
        $this->query_url = '_ensure_full_commit';
        $this->query_method = QueryMethods::POST;
        $this->allowed_response_codes = [QueryStatusCodes::ACCEPTED];
        return $this;
    }

    public function get_changes()
    {
        return new GetChangesDBQuery($this->couch_object, $this->database_name);
    }

    public function get_doc($doc_id)
    {
        return new GetDocDBQuery($this->couch_object, $this->get_database_name(), $doc_id);
    }

    public function save_doc($doc)
    {
        return new SaveDocDBQuery($this->couch_object, $this->get_database_name(), $doc);
    }

    public function bulk_docs()
    {
        return new BulkDocsDBQuery($this->couch_object, $this->get_database_name());
    }

    public function delete_doc($doc)
    {
        $this->reset();
        $doc = (array)$doc;
        if (isset($doc['_id']) && isset($doc['_rev'])) {
            $this->execution_method = 'delete_doc';
            $this->query_url = $doc['_id'];
            $this->query_method = QueryMethods::DELETE;
            $this->allowed_response_codes = [QueryStatusCodes::SUCCESS];
            $this->set_query_params('_rev', $doc['_rev'], 'string');
            return $this;
        }
        throw new \InvalidArgumentException('doc must contains _id and _rev');
    }


    public function get_view($view_id, $view_name)
    {
        return new GetViewDBQuery($this->couch_object, $this->get_database_name(), $view_id, $view_name);
    }
}