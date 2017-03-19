<?php
/**
 * Created by PhpStorm.
 * User: ahmetturk
 * Date: 19/03/2017
 * Time: 09:02
 */

namespace Fabs\CouchDB2\Response;


class ViewResponse extends BaseResponse
{
    protected $total_rows = 0;
    protected $offset = 0;
    protected $rows = [];

    public function __construct($data)
    {
        parent::__construct($data);
        $this->registerProperty($this->rows, ViewResponseElement::class, true);
    }

    /**
     * @return ViewResponseElement
     */
    public function getOne()
    {
        if (count($this->rows) > 0) {
            return $this->rows[0];
        }
        return null;
    }

    public function getRows()
    {
        return $this->rows;
    }

    public function getTotalRows()
    {
        return $this->total_rows;
    }

    public function getOffset()
    {
        return $this->offset;
    }
}