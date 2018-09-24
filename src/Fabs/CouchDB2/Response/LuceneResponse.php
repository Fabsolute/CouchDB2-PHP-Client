<?php
/**
 * Created by PhpStorm.
 * User: ahmetturk
 * Date: 30/06/2017
 * Time: 12:20
 */

namespace Fabs\CouchDB2\Response;

class LuceneResponse extends BaseResponse
{
    /** @var  string */
    protected $q = null;
    /** @var int */
    protected $fetch_duration = 0;
    /** @var int  */
    protected $total_rows = 0;
    /** @var int  */
    protected $limit = 0;
    /** @var int  */
    protected $search_duration = 0;
    /** @var string */
    protected $etag = null;
    /** @var int  */
    protected $skip = 0;
    /** @var  LuceneResponseElement[] */
    protected $rows;

    public function __construct()
    {
        parent::__construct();

        $this->registerProperty('rows', LuceneResponseElement::class, true);
    }

    /**
     * @return string
     */
    public function getQ()
    {
        return $this->q;
    }

    /**
     * @return int
     */
    public function getFetchDuration()
    {
        return $this->fetch_duration;
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
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @return int
     */
    public function getSearchDuration()
    {
        return $this->search_duration;
    }

    /**
     * @return string
     */
    public function getETag()
    {
        return $this->etag;
    }

    /**
     * @return int
     */
    public function getSkip()
    {
        return $this->skip;
    }

    /**
     * @return LuceneResponseElement[]
     */
    public function getRows()
    {
        return $this->rows;
    }
}