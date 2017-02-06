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
    protected $status_code;
    protected $response;
    protected $url;

    /**
     * CouchDB2Exception constructor.
     * @param string $status_code
     * @param int $response
     * @param string $url
     */
    public function __construct($status_code, $response, $url)
    {
        $this->status_code = $status_code;
        $this->response = $response;
        $this->url = $url;

        $message = sprintf('Status Code: %s, Couch Response: %s', $status_code, json_encode($response));
        parent::__construct($message, $status_code);
    }

    public function get_status_code()
    {
        return $this->status_code;
    }

    public function get_response()
    {
        return $this->response;
    }

    public function get_url()
    {
        return $this->url;
    }
}