<?php
/**
 * Created by PhpStorm.
 * User: fabsolutely
 * Date: 03/02/2017
 * Time: 14:34
 */

namespace Fabs\CouchDB2\Query\Queries;


use Fabs\CouchDB2\Constant\QueryMethods;
use Fabs\CouchDB2\Constant\QueryStatusCodes;
use Fabs\CouchDB2\Query\QueryBase;
use Fabs\CouchDB2\Response\UUIDResponse;
use Fabs\Serialize\SerializableObject;

class UUIDsQuery extends QueryBase
{

    public function __construct($couch_object, $count)
    {
        $this->allowed_response_codes = [QueryStatusCodes::SUCCESS];
        $this->query_method = QueryMethods::GET;
        $this->query_url = '_uuids';
        parent::__construct($couch_object);
        $this->setCount($count);
    }

    public function setCount($count)
    {
        if ($count < 1) {
            throw new \InvalidArgumentException('count should be greater than 0');
        }
        return $this->setQueryParameters('count', $count, 'int');
    }

    /**
     * @return SerializableObject|UUIDResponse
     */
    public function execute()
    {
        $data = parent::execute();
        return UUIDResponse::deserialize($data->getRawData());
    }
}