<?php
/**
 * Created by PhpStorm.
 * User: ahmetturk
 * Date: 19/03/2017
 * Time: 10:26
 */

namespace Fabs\CouchDB2\Response;


class UUIDResponse extends BaseResponse
{
    public $uuids = [];

    /**
     * @return string[]
     */
    public function getUUIDs()
    {
        return $this->uuids;
    }
}