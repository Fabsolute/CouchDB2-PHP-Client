<?php
/**
 * Created by PhpStorm.
 * User: ahmetturk
 * Date: 20/03/2017
 * Time: 10:27
 */

namespace Fabs\CouchDB2\Response;


class ChangesResponse extends BaseResponse
{
    public $results = [];

    public function __construct()
    {
        parent::__construct();
        $this->registerProperty('results', ChangesResponseElement::class, true);
    }

    /**
     * @return ChangesResponseElement
     */
    public function getOne()
    {
        return $this->results[0] ?? null;
    }

    /**
     * @return ChangesResponseElement[]
     */
    public function getResults()
    {
        return $this->results;
    }
}