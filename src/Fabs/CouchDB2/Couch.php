<?php
/**
 * Created by PhpStorm.
 * User: fabsolutely
 * Date: 30/01/2017
 * Time: 06:22
 */

namespace Fabs\CouchDB2;

use Fabs\CouchDB2\Query\DBQuery;
use Fabs\CouchDB2\Query\Queries\AllDatabasesQuery;
use Fabs\CouchDB2\Query\Queries\CreateDatabaseQuery;
use Fabs\CouchDB2\Query\Queries\DeleteDatabaseQuery;
use Fabs\CouchDB2\Query\Queries\UUIDsQuery;
use Fabs\CouchDB2\Query\QueryBase;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class Couch
{
    /**
     * @var CouchConfig
     */
    public $config;

    protected $default_request_options = [];
    protected $default_headers = [];
    protected $should_return_object = false;

    public function __construct($config)
    {
        $this->config = $config;
        $this->default_request_options['auth'] = [$this->config->username, $this->config->password];
        $this->default_request_options['timeout'] = 300;
        $this->default_headers['Content-Type'] = 'application/json';
        $this->default_headers['Accept'] = 'application/json,text/html,text/plain,*/*';
    }

    public function should_return_object($value)
    {
        $this->should_return_object = $value;
        return $this;
    }

    public function get_server_url()
    {
        return sprintf("http://%s:%s", $this->config->server, $this->config->port);
    }

    #region executions

    public function all_databases()
    {
        return new AllDatabasesQuery($this);
    }

    public function create_database($database_name)
    {
        return new CreateDatabaseQuery($this, $database_name);
    }

    public function delete_database($database_name)
    {
        return new DeleteDatabaseQuery($this, $database_name);
    }

    public function get_uuids($count = 1)
    {
        return new UUIDsQuery($this, $count);
    }

    public function select_database($database_name)
    {
        return new DBQuery($this, $database_name);
    }

    #endregion

    #region private helper functions

    /**
     * @param $response Response
     * @param $url string
     * @param $allowed_status_codes
     * @return mixed
     * @throws CouchDBException
     */
    protected function test_response($response, $url, $allowed_status_codes)
    {
        if (in_array($response->getStatusCode(), $allowed_status_codes)) {
            $body = $response->getBody();
            if (!empty($body)) {
                $body = json_decode($body, !$this->should_return_object);
                return $body;
            }
            return true;
        } else {
            throw new CouchDBException($response->getStatusCode(), $response->getBody(), $url);
        }
    }

    #endregion
    /**
     * @param $query_object QueryBase
     * @return mixed
     */
    public function execute($query_object)
    {
        $query_url = $query_object->get_query_url();
        $query_method = $query_object->get_query_method();
        $query_data = $query_object->get_query_data();
        $query_headers = $query_object->get_query_headers();
        $query_options = $query_object->get_query_options();

        $query_options['base_uri'] = $this->get_server_url();
        $query_options['exceptions'] = false;

        $allowed_response_codes = $query_object->get_allowed_response_codes();

        $execution_url = sprintf('/%s', $query_url);
        $execution_headers = array_merge($this->default_headers, $query_headers);
        $execution_options = array_merge($this->default_request_options, $query_options);

        $client = new Client($execution_options);
        $request = new Request($query_method, $execution_url, $execution_headers, $query_data);
        $response = $client->send($request);

        $response = $this->test_response($response, sprintf('%s%s', $this->get_server_url(), $execution_url), $allowed_response_codes);
        return $response;
    }

}