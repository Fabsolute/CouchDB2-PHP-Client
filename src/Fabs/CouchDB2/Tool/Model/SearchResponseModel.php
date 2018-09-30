<?php

namespace Fabs\CouchDB2\Tool\Model;

use Fabs\CouchDB2\Tool\EntityBase;
use Fabs\Serialize\SerializableObject;

class SearchResponseModel extends SerializableObject
{
    /** @var EntityBase[] */
    public $entity_list = [];
    /** @var int */
    public $total_count = 0;
}
