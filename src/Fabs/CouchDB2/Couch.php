<?php
/**
 * Created by PhpStorm.
 * User: fabsolutely
 * Date: 30/01/2017
 * Time: 06:22
 */

namespace Fabs\CouchDB2;

use Fabs\CouchDB2\Exception\CouchDBException;
use Fabs\CouchDB2\Exception\DocumentDeletedException;
use Fabs\CouchDB2\Exception\DocumentNotFoundException;
use Fabs\CouchDB2\Exception\ViewNotFoundException;
use Fabs\CouchDB2\Model\CouchError;
use Fabs\CouchDB2\Query\DBQuery;
use Fabs\CouchDB2\Query\Queries\AllDatabasesQuery;
use Fabs\CouchDB2\Query\Queries\CreateDatabaseQuery;
use Fabs\CouchDB2\Query\Queries\DeleteDatabaseQuery;
use Fabs\CouchDB2\Query\Queries\UUIDsQuery;
use Fabs\CouchDB2\Query\QueryBase;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Exception;

class Couch
{
    /**
     * @var CouchConfig
     */
    public $config;

    protected $default_request_options = [];
    protected $default_headers = [];

    public function __construct($config)
    {
        $this->config = $config;
        $this->default_request_options['auth'] = [$this->config->username, $this->config->password];
        $this->default_request_options['timeout'] = 300;
        $this->default_headers['Content-Type'] = 'application/json';
        $this->default_headers['Accept'] = 'application/json,text/html,text/plain,*/*';
    }

    public function getServerUrl()
    {
        return sprintf("http://%s:%s", $this->config->server, $this->config->port);
    }

    #region executions

    public function getAllDatabases()
    {
        return new AllDatabasesQuery($this);
    }

    public function getUUIDs($count = 1)
    {
        return new UUIDsQuery($this, $count);
    }

    public function selectDatabase($database_name)
    {
        return new DBQuery($this, $database_name);
    }

    public function createDatabase($database_name)
    {
        return new CreateDatabaseQuery($this, $database_name);
    }

    public function deleteDatabase($database_name)
    {
        return new DeleteDatabaseQuery($this, $database_name);
    }

    #endregion

    #region private helper functions

    /**
     * @param $response Response
     * @param $request Request
     * @param $allowed_status_codes
     * @return mixed
     * @throws CouchDBException
     */
    protected function testResponse($response, $request, $allowed_status_codes)
    {
        if (in_array($response->getStatusCode(), $allowed_status_codes)) {
            $body = $response->getBody();
            if (!empty($body)) {
                $body = json_decode($body, true);
                return $body;
            }
            return true;
        } else {
            $status_code = $response->getStatusCode();
            /** @var CouchError $response_body */
            $response_body = CouchError::deserialize(json_decode($response->getBody(), true));
            $error = $response_body->getError();
            $reason = $response_body->getReason();

            if ($status_code == 404) {
                if ($error == 'not_found') {
                    if ($reason == 'missing_named_view') {
                        throw  new ViewNotFoundException($this->getServerUrl(), $request, $response, $response_body);
                    } else if ($reason == 'missing') {
                        throw new DocumentNotFoundException($this->getServerUrl(), $request, $response, $response_body);
                    } else if ($reason == 'deleted') {
                        throw new DocumentDeletedException($this->getServerUrl(), $request, $response, $response_body);
                    }
                }
            }

            throw new CouchDBException($this->getServerUrl(), $request, $response, $response_body);
        }
    }

    #endregion
    /**
     * @param $query_object QueryBase
     * @return mixed
     */
    public function execute($query_object)
    {
        $query_url = $query_object->getQueryUrl();
        $query_method = $query_object->getQueryMethod();
        $query_data = $query_object->getQueryData();
        $query_headers = $query_object->getQueryHeaders();
        $query_options = $query_object->getQueryOptions();

        $query_options['base_uri'] = $this->getServerUrl();
        $query_options['exceptions'] = false;

        $allowed_response_codes = $query_object->getAllowedResponseCodes();

        $execution_url = sprintf('/%s', $query_url);
        $execution_headers = array_merge($this->default_headers, $query_headers);
        $execution_options = array_merge($this->default_request_options, $query_options);

        $client = new Client($execution_options);
        $request = new Request($query_method, $execution_url, $execution_headers, $query_data);
        $response = $client->send($request);

        $response = $this->testResponse($response, $request, $allowed_response_codes);
        return $response;
    }

}