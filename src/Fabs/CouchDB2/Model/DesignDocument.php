<?php


namespace Fabs\CouchDB2\Model;


use Fabs\Serialize\Condition\RenderIfNotEmptyArrayCondition;

class DesignDocument extends CouchObject
{
    /** @var  string */
    public $language = 'javascript';
    /** @var  View[] */
    public $views = [];
    /** @var string[] */
    public $updates = [];
    /** @var string[] */
    public $filters = [];
    /** @var FullText[] */
    public $fulltext = [];

    public function __construct()
    {
        parent::__construct();

        $this->registerProperty('views', View::class, true);
        $this->registerProperty('fulltext', FullText::class, true);
        $this->addCondition('views', new RenderIfNotEmptyArrayCondition());
        $this->addCondition('updates', new RenderIfNotEmptyArrayCondition());
        $this->addCondition('filters', new RenderIfNotEmptyArrayCondition());
        $this->addCondition('fulltext', new RenderIfNotEmptyArrayCondition());
    }
}