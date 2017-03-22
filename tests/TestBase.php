<?php

/**
 * Created by PhpStorm.
 * User: ahmetturk
 * Date: 19/03/2017
 * Time: 11:03
 */
class TestBase extends \PHPUnit\Framework\TestCase
{
    public function getCouchObject()
    {
        $config = new \Fabs\CouchDB2\CouchConfig('localhost', 5984, 'byfabs', '13487171');
        return new \Fabs\CouchDB2\Couch($config);
    }
}