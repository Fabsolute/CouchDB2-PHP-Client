<?php
/**
 * Created by PhpStorm.
 * User: fabsolutely
 * Date: 03/02/2017
 * Time: 17:06
 */

namespace Fabs\CouchDB2\Query\Queries;


use Fabs\CouchDB2\Query\DBQuery;

class GetContinuousChangesDBQuery extends GetChangesDBQuery
{
    protected $callback_function;

    /**
     * GetContinuousChangesDBQuery constructor.
     * @param $couch_object
     * @param $changes_query GetChangesDBQuery
     */
    public function __construct($couch_object, $changes_query)
    {
        parent::__construct($couch_object, $changes_query->getDatabaseName());
    }

    public function execute()
    {
        # TODO unutma
        throw new \Exception('not_implemented_yet');
        # return parent::execute();
    }

    public function setCallbackFunction($callback_function)
    {
        if (!is_callable($callback_function)) {
            throw new \Exception('$continuous_callback must be callable');
        }
        $this->callback_function = $callback_function;
        return $this;
    }
}