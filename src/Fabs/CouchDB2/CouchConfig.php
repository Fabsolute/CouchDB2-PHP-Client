<?php
/**
 * Created by PhpStorm.
 * User: fabsolutely
 * Date: 30/01/2017
 * Time: 06:25
 */

namespace Fabs\CouchDB2;


class CouchConfig
{
    public $username;
    public $password;
    public $server;
    public $port;
    public $db_name_prefix;

    public function __construct($server, $port, $username, $password, $db_name_prefix = null)
    {
        $this->server = $server;
        $this->username = $username;
        $this->password = $password;
        $this->port = $port;
        $this->db_name_prefix = $db_name_prefix;
    }
}