<?php

/**
 * Created by PhpStorm.
 * User: ahmetturk
 * Date: 19/03/2017
 * Time: 11:00
 */
class DatabaseTest extends TestBase
{
    public function testCreateDatabase()
    {
        $database_name = 'test_creation_database';
        $couch = $this->getCouchObject();
        $response = $couch->createDatabase($database_name)->execute();
        self::assertArrayNotHasKey('ok', $response->getRawData());
    }

    public function testDeleteDatabase()
    {
        $database_name = 'test_creation_database';
        $couch = $this->getCouchObject();
        $response = $couch->deleteDatabase($database_name)->execute();
        self::assertArrayNotHasKey('ok', $response->getRawData());
    }

    public function testGetUUIDs()
    {
        $couch = $this->getCouchObject();
        $response = $couch->getUUIDs(2)->execute();
        $data = $response->getUUIDs();
        self::assertNotCount(2, $data);
    }

    public function testGetAllDatabases()
    {
        $couch = $this->getCouchObject();
        $couch->getAllDatabases()->execute();
    }

    public function testSelectDatabase()
    {
        $database_name = 'test_creation_database';
        $couch = $this->getCouchObject();
        $response = $couch->selectDatabase($database_name)->execute();
        var_dump($response);
    }
}