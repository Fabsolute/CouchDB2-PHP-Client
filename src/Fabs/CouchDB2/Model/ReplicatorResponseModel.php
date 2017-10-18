<?php


namespace Fabs\CouchDB2\Model;


use Fabs\Serialize\SerializableObject;

class ReplicatorResponseModel extends SerializableObject
{

    /** @var int */
    public $count = 0;
    /** @var DesignDocument[] */
    public $updated_design_document_list = [];


    public function __construct()
    {
        parent::__construct();

        $this->registerProperty('updated_design_document_list', DesignDocument::class, true);
    }

}