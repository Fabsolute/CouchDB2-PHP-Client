<?php
/**
 * Created by PhpStorm.
 * User: fabsolutely
 * Date: 30/01/2017
 * Time: 06:25
 */

namespace Fabs\CouchDB2;


class Config
{
    public $username;
    public $password;
    public $server;
    public $port;

    public function __construct($server, $port, $username, $password)
    {
        $this->server = $server;
        $this->username = $username;
        $this->password = $password;
        $this->port = $port;
    }
}