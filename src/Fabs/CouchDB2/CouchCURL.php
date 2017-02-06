<?php
/**
 * Created by PhpStorm.
 * User: fabsolutely
 * Date: 01/02/2017
 * Time: 23:11
 */

namespace Fabs\CouchDB2;


use Fabs\CouchDB2\Http\CURL;

class CouchCURL extends CURL
{
    public function request($url, $headers = array(), $data = array(), $options = array())
    {
        if (isset($options['upload_file'])) {
            curl_setopt($this->handle, CURLOPT_INFILE, $options['upload_file']);
            curl_setopt($this->handle, CURLOPT_INFILESIZE, filesize($options['upload_file']));
            curl_setopt($this->handle, CURLOPT_PUT, 1);
        }
        return parent::request($url, $headers, $data, $options);
    }
}