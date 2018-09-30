<?php

namespace Fabs\CouchDB2\Tool;


use Fabs\CouchDB2\Exception\CouchDBException;

interface CouchDBExceptionHandler
{
    /**
     * @param CouchDBException $exception
     */
    public function handle($exception);

    /**
     * @param string $db_name
     * @param string[] $failed_ids
     */
    public function handleSaveAllFailed($db_name, $failed_ids);
}
