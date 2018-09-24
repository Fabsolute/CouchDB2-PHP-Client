<?php


namespace Fabs\CouchDB2\Model;


class ReplicatorDocument extends CouchObject
{

    /** @var string */
    public $source = null;
    /** @var string */
    public $target = null;
    /** @var bool */
    public $continuous = false;
    /** @var bool */
    public $create_target = false;


    public function __construct()
    {
        parent::__construct();

        $this->addStringValidation('source')->isRequired();
        $this->addStringValidation('target')->isRequired();
        $this->addBooleanValidation('continuous')->isRequired();
        $this->addBooleanValidation('create_target')->isRequired();
    }
}