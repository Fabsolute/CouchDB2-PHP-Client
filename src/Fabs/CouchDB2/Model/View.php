<?php


namespace Fabs\CouchDB2\Model;


use Fabs\Serialize\Condition\RenderIfNotNullCondition;
use Fabs\Serialize\SerializableObject;

class View extends SerializableObject
{
    /** @var string */
    public $map = null;
    /** @var string */
    public $reduce = null;

    public function __construct()
    {
        parent::__construct();

        $this->addCondition('reduce', new RenderIfNotNullCondition());
    }
}