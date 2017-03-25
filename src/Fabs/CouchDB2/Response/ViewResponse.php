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

    public function __construct()
    {
        parent::__construct();
        $this->registerProperty('rows', ViewResponseElement::class, true);
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

    /**
     * @return ViewResponseElement[]
     */
    public function getRows()
    {
        return $this->rows;
    }

    /**
     * @return int
     */
    public function getTotalRows()
    {
        return $this->total_rows;
    }

    /**
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }
}