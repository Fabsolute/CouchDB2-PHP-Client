<?php
/**
 * Created by PhpStorm.
 * User: fabsolutely
 * Date: 03/02/2017
 * Time: 14:24
 */

namespace Fabs\CouchDB2\Query\Queries;


use Fabs\CouchDB2\Constant\QueryMethods;
use Fabs\CouchDB2\Constant\QueryStatusCodes;
use Fabs\CouchDB2\Query\QueryBase;

class CreateDatabaseQuery extends QueryBase
{
    public function __construct($couch_object, $database_name)
    {
        $this->allowed_response_codes = [QueryStatusCodes::CREATED];
        $this->query_method = QueryMethods::PUT;
        $this->query_url = $database_name;
        parent::__construct($couch_object);
    }

    public function setDatabaseName($database_name)
    {
        $this->query_url = $database_name;
    }

    public function execute()
    {
        if (empty($this->query_url)) {
            throw  new \Exception('Database name cannot be null');
        }
        return parent::execute();
    }

    public function getQueryUrl()
    {
        $url = parent::getQueryUrl();
        if ($this->couch_object->config->db_name_prefix != null) {
            $url = $this->couch_object->config->db_name_prefix . $url;
        }
        return $url;
    }
}