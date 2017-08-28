<?php


namespace Fabs\CouchDB2\Model;


use Fabs\Serialize\SerializableObject;

class FullText extends SerializableObject
{
    /** @var string */
    public $index = null;

    public function __construct()
    {
        parent::__construct();

        $this->addStringValidation('index');
    }
}