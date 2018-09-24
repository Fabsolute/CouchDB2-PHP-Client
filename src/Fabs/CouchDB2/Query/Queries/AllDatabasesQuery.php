<?php

/**
 * Created by PhpStorm.
 * User: fabsolutely
 * Date: 03/02/2017
 * Time: 14:18
 */
namespace Fabs\CouchDB2\Query\Queries;

use Fabs\CouchDB2\Constant\QueryMethods;
use Fabs\CouchDB2\Constant\QueryStatusCodes;
use Fabs\CouchDB2\Query\QueryBase;

class AllDatabasesQuery extends QueryBase
{
    public function __construct($couch_object)
    {
        $this->allowed_response_codes = [QueryStatusCodes::SUCCESS];
        $this->query_method = QueryMethods::GET;
        $this->query_url = '_all_dbs';
        parent::__construct($couch_object);
    }
}