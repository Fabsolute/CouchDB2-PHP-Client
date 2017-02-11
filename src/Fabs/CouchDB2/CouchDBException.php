<?php
/**
 * Created by PhpStorm.
 * User: fabsolutely
 * Date: 01/02/2017
 * Time: 19:42
 */

namespace Fabs\CouchDB2;


class CouchDBException extends \Exception
{
    protected $url;
    protected $request;
    protected $response;
    protected $request_body;
    protected $response_body;

    /**
     * CouchDB2Exception constructor.
     * @param string $base_server_url
     * @param \GuzzleHttp\Psr7\Request $request
     * @param \GuzzleHttp\Psr7\Response $response
     */
    public function __construct($base_server_url, $request, $response)
    {
        $this->response = $response;
        $this->request = $request;
        $this->url = sprintf('%s%s?%s', $base_server_url, $request->getUri()->getPath(), $request->getUri()->getQuery());
        $status_code = $response->getStatusCode();
        $this->request_body = json_decode($request->getBody(), false);
        $this->response_body = json_decode($response->getBody(), false);

        $message = sprintf('Status Code: %s, Couch Response: %s', $status_code, json_encode($this->response_body));
        parent::__construct($message, $status_code);
    }

    /**
     * @return \GuzzleHttp\Psr7\Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return \GuzzleHttp\Psr7\Request
     */
    public function getRequest(){
        return $this->request;
    }

    /**
     * @return mixed
     */
    public function getResponseBody(){
        return $this->response_body;
    }

    /**
     * @return mixed
     */
    public function getRequestBody(){
        return $this->request_body;
    }

    public function getUrl()
    {
        return $this->url;
    }
}