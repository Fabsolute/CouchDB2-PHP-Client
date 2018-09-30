<?php

namespace Fabs\CouchDB2\Tool;


use Fabs\CouchDB2\Constant\QueryMethods;
use Fabs\CouchDB2\Constant\QueryStatusCodes;
use Fabs\CouchDB2\Query\QueryBase;
use Fabs\CouchDB2\Response\UUIDResponse;
use Fabstract\Component\Assert\Assert;

class CouchCustomUUIDsQuery extends QueryBase
{

    public function __construct($couch_object, $count)
    {
        $this->allowed_response_codes = [QueryStatusCodes::SUCCESS];
        $this->query_method = QueryMethods::GET;
        $this->query_url = '_custom_uuids';
        parent::__construct($couch_object);

        Assert::isPositiveInt($count, 'count');

        $this->setQueryParameters('count', $count, 'int');
    }

    /**
     * @return UUIDResponse
     */
    public function execute()
    {
        $data = parent::execute();
        return UUIDResponse::deserialize($data->getRawData());
    }
}
