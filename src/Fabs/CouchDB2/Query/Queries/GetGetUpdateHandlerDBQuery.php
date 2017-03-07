<?php
/**
 * Created by PhpStorm.
 * User: ahmetturk
 * Date: 07/03/2017
 * Time: 14:33
 */

namespace Fabs\CouchDB2\Query\Queries;


use Fabs\CouchDB2\Query\DBQuery;
use Fabs\CouchDB2\Query\QueryMethods;
use Fabs\CouchDB2\Query\QueryStatusCodes;

class GetUpdateHandlerDBQuery extends DBQuery
{

    public function __construct($couch_object, $database_name, $design_doc_id, $update_handler_name)
    {
        $this->reset();
        $this->execution_method = 'update_handler';
        $this->query_method = QueryMethods::POST;
        $this->allowed_response_codes = [QueryStatusCodes::SUCCESS];
        $this->query_url = sprintf('_design/%s/_update/%s', $design_doc_id, $update_handler_name);
        parent::__construct($couch_object, $database_name);
    }

    public function selectDocument($document_id)
    {
        $this->query_method = QueryMethods::PUT;
        $this->query_url = sprintf('_%s/%s', $this->query_url, $document_id);
        return $this;
    }


    public function setFormField($variable_name, $value)
    {
        $this->query_data[$variable_name] = $value;
        return $this;
    }

    public function setQueryField($variable_name, $value)
    {
        return $this->setQueryParameters($variable_name,$value);
    }
}