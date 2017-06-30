<?php
/**
 * Created by PhpStorm.
 * User: ahmetturk
 * Date: 30/06/2017
 * Time: 12:19
 */

namespace Fabs\CouchDB2\Model;


class LuceneSearchQueryModel
{
    /** @var string */
    public $field = null;
    /** @var string */
    public $value = null;
    /** @var string */
    public $is_exact = false;
    /** @var string */
    public $is_required = false;

    /**
     * LuceneSearchQueryModel constructor.
     * @param string $field
     * @param string $value
     * @param bool $is_exact
     * @param bool $is_required
     */
    function __construct($field, $value, $is_exact = true, $is_required = true)
    {
        $this->field = $field;
        $this->value = $value;
        $this->is_exact = $is_exact;
        $this->is_required = $is_required;
    }
}