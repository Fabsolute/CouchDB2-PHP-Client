<?php
/**
 * Created by PhpStorm.
 * User: ahmetturk
 * Date: 30/06/2017
 * Time: 12:18
 */

namespace Fabs\CouchDB2\Model;

class LuceneSortOption
{
    /** @var  string */
    public $sort_by;
    /** @var  bool */
    public $ascending;
    /** @var  string */
    public $type;

    function __construct($sort_by, $type, $ascending = true)
    {
        $this->sort_by = $sort_by;
        $this->ascending = $ascending;
        $this->type = $type;
    }
}