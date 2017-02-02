<?php
/**
 * Created by PhpStorm.
 * User: fabsolutely
 * Date: 30/01/2017
 * Time: 06:22
 */

namespace Fabs\CouchDB2;


require_once('Http/Requests.php');
use Fabs\CouchDB2\Http\Requests;

class Couch
{
    /**
     * @var Config
     */
    public $config;

    protected $default_request_options = [];
    protected $default_headers = [];
    protected $should_return_object = false;

    public function __construct($config)
    {
        Requests::add_transport('Fabs\CouchDB2\CouchCURL');

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
        $method_url = $this->get_server_url();
        $method_url = sprintf('%s/%s',$method_url,'_all_dbs');
        $response = Requests::get($method_url, $this->default_headers, $this->default_request_options);
        $response = $this->test_response($response, [200]);
        if (isset($response->body)) {
            return $response->body;
        }
        return $response;
    }

    public function create_database($database_name)
    {
        $method_url = $this->get_database_url();
        $method_url = sprintf('%s/%s', $method_url, $database_name);
        $response = Requests::put($method_url, $this->default_headers, [], $this->default_request_options);
        $response = $this->test_response($response, [201]);
        if (isset($response->body)) {
            return $response->body;
        }
        return $response;
    }

    public function delete_database()
    {

        $method_url = $this->get_database_url();
        $response = Requests::delete($method_url, $this->default_headers, $this->default_request_options);
        $response = $this->test_response($response, [200]);
        if (isset($response->body)) {
            return $response->body;
        }
        return $response;
    }

    public function get_uuids($count = 1)
    {
        $count = (int)$count;
        if ($count < 1) {
            throw new \InvalidArgumentException('count should be greater than 0');
        }
        $server_url = $this->get_server_url();
        $data = ['count' => $count];
        $query_string = http_build_query($data, null, '&');
        $response = Requests::get($server_url . '/_uuids?' . $query_string, $this->default_headers, $this->default_request_options);

        $response = $this->test_response($response, [200]);
        if (isset($response->body)) {
            return $response->body;
        }
        return $response;
    }

    #endregion

    #region private helper functions

    protected function parse_raw_data($raw, $json_as_array = true)
    {
        $raw->body = json_decode($raw->body, $json_as_array);
        return $raw;
    }

    protected function test_response($response, $allowed_status_codes)
    {
        $response = $this->parse_raw_data($response, !$this->should_return_object);
        if (in_array($response->status_code, $allowed_status_codes)) {
            return $response;
        } else {
            throw new CouchDB2Exception($response->status_code, $response->body, $response->url);
        }
    }

    #endregion

    #region auto_loader

    public static function autoloader($class)
    {
        // Check that the class starts with "Requests"
        if (strpos($class, 'Fabs') !== 0) {
            return;
        }
        $file = str_replace('Fabs\CouchDB2\\', '', $class);
        $file = str_replace('_', '/', $file);
        if (file_exists(dirname(__FILE__) . '/' . $file . '.php')) {
            require_once(dirname(__FILE__) . '/' . $file . '.php');
        }
    }

    public static function register_autoloader()
    {
        Requests::register_autoloader();
        spl_autoload_register(array('Fabs\CouchDB2\Couch', 'autoloader'));
    }

    #endregion
}