<?php
/**
 * Created by PhpStorm.
 * User: fabsolutely
 * Date: 03/02/2017
 * Time: 14:24
 */

namespace Fabs\CouchDB2\Query\Queries;


use Fabs\CouchDB2\Query\QueryBase;
use Fabs\CouchDB2\Query\QueryMethods;
use Fabs\CouchDB2\Query\QueryStatusCodes;

class DeleteDatabaseQuery extends QueryBase
{
    public function __construct($couch_object, $database_name)
    {
        $this->allowed_response_codes = [QueryStatusCodes::SUCCESS];
        $this->query_method = QueryMethods::DELETE;
        $this->query_url = $database_name;
        parent::__construct($couch_object);
    }

    public function set_database_name($database_name)
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
}