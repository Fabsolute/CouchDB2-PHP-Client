<?php

namespace Fabs\CouchDB2\Tool;

use Fabs\CouchDB2\Exception\DocumentConflictException;
use Fabs\CouchDB2\Query\Queries\GetUpdateHandlerDBQuery;

class CustomGetUpdateHandlerDBQuery extends GetUpdateHandlerDBQuery
{
    public function execute()
    {
        try {
            return parent::execute();
        } catch (DocumentConflictException $conflict) {
            return $this->execute();
        }
    }
}
