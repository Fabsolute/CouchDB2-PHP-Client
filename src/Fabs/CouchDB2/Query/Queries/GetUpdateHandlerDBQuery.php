<?php
/**
 * Created by PhpStorm.
 * User: ahmetturk
 * Date: 07/03/2017
 * Time: 14:33
 */

namespace Fabs\CouchDB2\Query\Queries;


use Fabs\CouchDB2\Constant\QueryMethods;
use Fabs\CouchDB2\Constant\QueryStatusCodes;
use Fabs\CouchDB2\Query\DBQuery;

class GetUpdateHandlerDBQuery extends DBQuery
{

    public function __construct($couch_object, $database_name, $design_doc_id, $update_handler_name)
    {
        $this->reset();
        $this->execution_method = 'update_handler';
        $this->query_method = QueryMethods::POST;
        $this->allowed_response_codes = [QueryStatusCodes::SUCCESS,QueryStatusCodes::CREATED];
        $this->query_url = sprintf('_design/%s/_update/%s', $design_doc_id, $update_handler_name);
        $this->query_headers['Content-Type'] = 'application/x-www-form-urlencoded';
        parent::__construct($couch_object, $database_name);
    }

    /**
     * @param string $document_id
     * @return GetUpdateHandlerDBQuery
     */
    public function selectDocument($document_id)
    {
        $this->query_method = QueryMethods::PUT;
        $this->query_url = sprintf('%s/%s', $this->query_url, $document_id);
        return $this;
    }

    /**
     * @param $variable_name
     * @param $value
     * @return GetUpdateHandlerDBQuery
     */
    public function setFormField($variable_name, $value)
    {
        $this->query_data[$variable_name] = $value;
        return $this;
    }

    /**
     * @param $variable_name
     * @param $value
     * @return GetUpdateHandlerDBQuery
     */
    public function setQueryField($variable_name, $value)
    {
        return $this->setQueryParameters($variable_name, $value);
    }
}