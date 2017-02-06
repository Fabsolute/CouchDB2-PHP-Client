<?php
/**
 * Created by PhpStorm.
 * User: fabsolutely
 * Date: 30/01/2017
 * Time: 06:22
 */

namespace Fabs\CouchDB2;

use Fabs\CouchDB2\Http\Requests;

class CouchDB extends Couch
{
    /**
     * @var string
     */
    public $database_name;

    private $query_parameters = [];

    public function __construct($config, $database = null)
    {
        parent::__construct($config);
        $this->database_name = $database;
    }

    public function set_database_name($database_name)
    {
        $this->database_name = $database_name;
        return $this;
    }

    public function get_database_name()
    {
        return $this->database_name;
    }

    public function get_method_url($method_name)
    {
        $url = $this->get_database_url();
        return sprintf("%s/%s", $url, $method_name);
    }

    public function get_database_url()
    {
        $url = $this->get_server_url();
        return sprintf("%s/%s", $url, $this->database_name);
    }

    #region executions

    public function get_info()
    {
        $method_url = $this->get_database_url();
        $response = Requests::get($method_url, $this->default_headers, $this->default_request_options);
        $response = $this->test_response($response, [200]);
        if (isset($response->body)) {
            return $response->body;
        }
        return $response;
    }

    public function exists()
    {
        try {
            $this->get_info();
            return true;
        } catch (\Exception $e) {
            if ($e->getCode() == 404) {
                return false;
            }
            throw $e;
        }
    }

    public function compact()
    {
        $method_url = $this->get_method_url('_compact');
        $response = Requests::post($method_url, $this->default_headers, [], $this->default_request_options);
        $response = $this->test_response($response, [202]);
        if (isset($response->body)) {
            return $response->body;
        }
        return $response;
    }

    public function cleanup_views()
    {
        $method_url = $this->get_method_url('_view_cleanup');
        $response = Requests::post($method_url, $this->default_headers, [], $this->default_request_options);
        $response = $this->test_response($response, [202]);
        if (isset($response->body)) {
            return $response->body;
        }
        return $response;
    }

    public function get_changes($is_continuous = false, $continuous_callback = null)
    {
        $method_url = $this->get_method_url('_changes');
        if ($is_continuous) {
            if (!is_callable($continuous_callback)) {
                throw new \InvalidArgumentException('$continuous_callback must be callable');
            }

            # TODO burayi unutmayalim
            return $continuous_callback;
        }

        $query = http_build_query($this->query_parameters, null, '&');
        if (strlen($query)) {
            $method_url = sprintf('%s?%s', $method_url, $query);
        }

        $response = Requests::get($method_url, $this->default_headers, $this->default_request_options);
        $response = $this->test_response($response, [200, 201]);
        if (isset($response->body)) {
            return $response->body;
        }
        return $response;
    }

    public function get_doc($doc_id)
    {
        $method_url = $this->get_database_url();
        $method_url = sprintf('%s/%s', $method_url, $doc_id);

        $response = Requests::get($method_url, $this->default_headers, $this->default_request_options);
        $response = $this->test_response($response, [200]);
        if (isset($response->body)) {
            return $response->body;
        }
        return $response;
    }

    public function save_doc($doc)
    {
        $method_url = $this->get_database_url();

        $doc = (array)$doc;
        $encoded_doc = json_encode($doc);
        if (isset($doc['_id']) && !empty($doc['_id']) && isset($doc['_rev']) && !empty($doc['_rev'])) {
            $method_url = sprintf('%s/%s', $method_url, $doc['_id']);
            $response = Requests::put($method_url, $this->default_headers, $encoded_doc, $this->default_request_options);
        } else {
            unset($doc['_rev']);
            $response = Requests::post($method_url, $this->default_headers, $encoded_doc, $this->default_request_options);
        }

        $response = $this->test_response($response, [200, 201]);
        if (isset($response->body)) {
            return $response->body;
        }
        return $response;
    }

    public function save_docs($docs, $all_or_nothing = false)
    {
        $method_url = $this->get_method_url('_bulk_docs');

        $data = ['docs' => []];
        foreach ($docs as $doc) {
            $data['docs'][] = (array)$doc;
        }

//        if ($all_or_nothing) {
//            $data['all_or_nothing'] = true;
//        }

        $response = Requests::post($method_url, $this->default_headers, $data, $this->default_request_options);
        $response = $this->test_response($response, [200, 201, 202]);
        if (isset($response->body)) {
            return $response->body;
        }
        return $response;
    }


    public function delete_doc($doc_id, $doc_rev)
    {
        $method_url = $this->get_database_url();
        $method_url = sprintf('%s/%s?rev=%s', $method_url, $doc_id, $doc_rev);

        $response = Requests::delete($method_url, $this->default_headers, $this->default_request_options);
        $response = $this->test_response($response, [200, 202]);
        if (isset($response->body)) {
            return $response->body;
        }
        return $response;
    }

    public function get_view($view_id, $view_name)
    {
        $method_url = $this->get_database_url();
        $method_url = sprintf('%s/_design/%s/_view/%s', $method_url, $view_id, $view_name);

        list($is_method_get, $query, $data) = $this->prepare_query();

        if (strlen($query) > 0) {
            $method_url = sprintf('%s?%s', $method_url, $query);
        }

        if ($is_method_get) {
            $response = Requests::get($method_url, $this->default_headers, $this->default_request_options);
        } else {
            $response = Requests::post($method_url, $this->default_headers, $data, $this->default_request_options);
        }

        $response = $this->test_response($response, [200]);
        if (isset($response->body)) {
            return $response->body;
        }
        return $response;
    }

    public function update_doc_handler($update_doc_id, $handler_name, $doc_id = null, $options = [])
    {
        $headers = $this->default_headers;
        $data = [];
        $content_type = null;

        $method_url = $this->get_method_url('_design');
        $method_url = sprintf('%s/%s/_update/%s', $method_url, $update_doc_id, $handler_name);

        if (strlen($doc_id) > 0) {
            $method_url = sprintf('%s/%s', $method_url, $doc_id);
        }

        if (isset($options['parameters'])) {
            $query = http_build_query($options['parameters'], null, '&');
            if (strlen($query) > 0) {
                $method_url = sprintf('%s?%s', $method_url, $query);
            }
        }

        if (isset($options['Content-Type'])) {
            $headers['Content-Type'] = $options['Content-Type'];
        }

        if (isset($options['data'])) {
            $data = $options['data'];
        }

        $response = Requests::put($method_url, $headers, $data, $this->default_request_options);
        $response = $this->test_response($response, [200, 201, 202]);
        if (isset($response->body)) {
            return $response->body;
        }
        return $response;
    }

    public function copy_doc($doc_id, $new_doc_id)
    {
        $method_url = $this->get_database_url();
        $method_url = sprintf('%s/%s', $method_url, $doc_id);

        $headers = ['Destination' => $new_doc_id];
        $headers = array_merge($this->default_headers, $headers);

        $response = Requests::request($method_url, $headers, [], 'COPY', $this->default_request_options);
        $response = $this->test_response($response, [200, 201]);
        if (isset($response->body)) {
            return $response->body;
        }
        return $response;
    }

    public function save_attachment($doc_id, $doc_rev, $file, $attachment_name, $content_type)
    {
        $method_url = $this->get_database_url();
        if (strlen($doc_rev) > 0) {
            $method_url = sprintf('%s/%s/%s?rev=%s', $method_url, $doc_id, $attachment_name, $doc_rev);
        } else {
            $method_url = sprintf('%s/%s/%s', $method_url, $doc_id, $attachment_name);
        }
        $headers = ['Content-Type' => $content_type];
        $headers = array_merge($this->default_headers, $headers);

        $response = Requests::put($method_url, $headers, $file, $this->default_request_options);
        $response = $this->test_response($response, [200]);
        if (isset($response->body)) {
            return $response->body;
        }
        return $response;
    }

    public function save_attachment_from_file($doc_id, $doc_rev, $file_name, $attachment_name,
                                              $content_type = 'application/octet-stream')
    {
        $method_url = $this->get_database_url();
        if (strlen($doc_rev) > 0) {
            $method_url = sprintf('%s/%s/%s?rev=%s', $method_url, $doc_id, $attachment_name, $doc_rev);
        } else {
            $method_url = sprintf('%s/%s/%s', $method_url, $doc_id, $attachment_name);
        }

        $headers = ['Content-Type' => $content_type];
        $headers = array_merge($this->default_headers, $headers);
        $file_stream = fopen($file_name, 'r');
        $request_options = ['upload_file' => $file_stream];
        $request_options = array_merge($this->default_request_options, $request_options);

        $response = Requests::put($method_url, $headers, [], $request_options);
        $response = $this->test_response($response, [200]);
        if (isset($response->body)) {
            return $response->body;
        }
        return $response;
    }

    public function delete_attachment($doc_id, $doc_rev, $attachment_name)
    {
        $method_url = $this->get_database_url();
        $method_url = sprintf('%s/%s/%s?rev=%s', $method_url, $doc_id, $attachment_name, $doc_rev);

        $response = Requests::delete($method_url, $this->default_headers, $this->default_request_options);
        $response = $this->test_response($response, [200, 202]);
        if (isset($response->body)) {
            return $response->body;
        }
        return $response;
    }

    public function get_list($list_view_id, $list_name, $view_name, $view_id = null, $additional_parameters = [])
    {
        $list_view_id = preg_replace("@^_design/@", "", $list_view_id);
        $method_url = $this->get_database_url();
        if ($view_id == null) {
            $method_url = sprintf('%s/_design/%s/_list/%s/%s', $method_url, $list_view_id, $list_name, $view_name);
        } else {
            $method_url = sprintf('%s/_design/%s/_list/%s/%s/%s', $method_url, $list_view_id, $list_name, $view_id, $view_name);
        }

        list($is_method_get, $query, $data) = $this->prepare_query($additional_parameters);

        if (strlen($query) > 0) {
            $method_url = sprintf('%s?%s', $method_url, $query);

        }

        if ($is_method_get) {
            $response = Requests::get($method_url, $this->default_headers, $this->default_request_options);
        } else {
            $response = Requests::post($method_url, $this->default_headers, $data, $this->default_request_options);
        }

        $response = $this->test_response($response, [200]);
        if (isset($response->body)) {
            return $response->body;
        }
        return $response;
    }

    public function get_show($view_id, $view_name, $doc_id = null, $additional_parameters = [])
    {
        $view_id = preg_replace("@^_design/@", "", $view_id);
        $method_url = $this->get_database_url();
        $method_url = sprintf('%s/_design/%s/_show/%s', $method_url, $view_id, $view_name);

        if (strlen($doc_id) > 0) {
            $method_url = sprintf('%s/%s', $method_url, $doc_id);
        }

        if (is_array($additional_parameters) && count($additional_parameters) > 0) {
            $query = http_build_query($additional_parameters, null, '&');
            $method_url = sprintf('%s?%s', $method_url, $query);
        }

        $response = Requests::get($method_url, $this->default_headers, $this->default_request_options);
        $response = $this->test_response($response, [200]);
        if (isset($response->body)) {
            return $response->body;
        }
        return $response;
    }

    public function get_view_info($view_id)
    {
        $view_id = preg_replace("@^_design/@", "", $view_id);
        $method_url = $this->get_database_url();
        $method_url = sprintf('%s/_design/%s/_info', $method_url, $view_id);

        $response = Requests::get($method_url, $this->default_headers, $this->default_request_options);
        $response = $this->test_response($response, [200]);
        if (isset($response->body)) {
            return $response->body;
        }
        return $response;
    }

    public function compact_all_views()
    {
        $temp_should_return_value = $this->should_return_object;

        $this->should_return_object(false);
        $response = $this->start_key('_design/')->end_key('_design0')->get_all_docs();
        $this->should_return_object($temp_should_return_value);
        if ($response) {
            if (isset($response->body['rows']) && is_array($response->body['rows'])) {
                foreach ($response->body['rows'] as $view_row) {
                    $this->compact_views($view_row['key']);
                }
            }
        }
        return $response;
    }

    public function compact_views($view_id)
    {
        $view_id = preg_replace("@^_design/@", "", $view_id);
        $method_url = $this->get_method_url('_compact');
        $method_url = sprintf('%s/%s', $method_url, $view_id);

        $response = Requests::post($method_url, $this->default_headers, [], $this->default_request_options);
        $response = $this->test_response($response, [202]);
        if (isset($response->body)) {
            return $response->body;
        }
        return $response;
    }

    public function ensure_full_commit()
    {
        $method_url = $this->get_method_url('_ensure_full_commit');
        $response = Requests::post($method_url, $this->default_headers, [], $this->default_request_options);
        $response = $this->test_response($response, [200, 201]);
        if (isset($response->body)) {
            return $response->body;
        }
        return $response;
    }

    public function get_all_docs()
    {
        $method_url = $this->get_method_url('_all_docs');
        list($is_method_get, $query, $data) = $this->prepare_query();
        if (strlen($query) > 0) {
            $method_url .= '?' . $query;
        }

        if ($is_method_get) {
            $response = Requests::get($method_url, $this->default_headers, $this->default_request_options);
        } else {
            $response = Requests::post($method_url, $this->default_headers, $data, $this->default_request_options);
        }
        $response = $this->test_response($response, [200, 201]);
        if (isset($response->body)) {
            return $response->body;
        }
        return $response;
    }

    #endregion

    #region private helper functions

    private function prepare_query($additional_parameters = [])
    {
        if (!is_array($additional_parameters)) {
            $additional_parameters = [];
        }

        $view_query = array_merge($this->query_parameters, $additional_parameters);

        $is_method_get = true;
        $data = null;
        if (isset($view_query['keys'])) {
            $is_method_get = false;
            $data = json_encode(['keys' => $view_query['keys']]);
            unset($view_query['keys']);
        }

        $query = http_build_query($view_query, null, '&');
        $output = [$is_method_get, $query, $data];
        return $output;
    }

    #endregion

    #region queries

    public function feed($value)
    {
        if ($value == 'longpoll') {
            $this->add_query_params('feed', $value, null);
        } else if ($value == 'continuous') {
            throw new \InvalidArgumentException('dont use feed function. use get_changes(true,callback) function');
        }

        return $this;
    }

    public function filter($value, $additional_parameters = [])
    {
        if (strlen(trim($value)) > 0) {
            $this->add_query_params('filter', trim($value), null);
            $this->query_parameters = array_merge($this->query_parameters, $additional_parameters);
        }
        return $this;
    }

    public function open_revs($value)
    {
        if ($value == 'all') {
            $this->add_query_params('open_revs', $value, null);
        } elseif (is_array($value)) {
            $this->add_query_params('open_revs', $value, 'json_encode');
        }
        return $this;
    }

    public function since($value)
    {
        $this->add_query_params('since', $value, 'int');
        return $this;
    }

    public function heartbeat($value)
    {
        $this->add_query_params('heartbeat', $value, 'int');
        return $this;
    }

    public function style($value)
    {
        $this->add_query_params('style', $value);
        return $this;
    }

    public function conflicts()
    {
        $this->add_query_params('conflicts', 'true');
        return $this;
    }

    public function revs()
    {
        $this->add_query_params('revs', 'true');
        return $this;
    }

    public function revs_info()
    {
        $this->add_query_params('revs_info', 'true');
        return $this;
    }

    public function rev($value)
    {
        $this->add_query_params('rev', $value);
        return $this;
    }

    public function key($value)
    {
        $this->add_query_params('key', $value, 'json_encode');
        return $this;
    }

    public function keys($value)
    {
        $this->add_query_params('keys', $value, 'ensure_array');
        return $this;
    }

    public function start_key($value)
    {
        $this->add_query_params('startkey', $value, 'json_encode');
        return $this;
    }

    public function end_key($value)
    {
        $this->add_query_params('endkey', $value, 'json_encode');
        return $this;
    }

    public function start_key_doc_id($value)
    {
        $this->add_query_params('startkey_docid', $value, 'string');
        return $this;
    }

    public function end_key_doc_id($value)
    {
        $this->add_query_params('endkey_docid', $value, 'string');
        return $this;
    }

    public function limit($value)
    {
        $this->add_query_params('limit', $value, 'int');
        return $this;
    }

    public function stale($value)
    {
        if ($value == 'ok' || $value == 'update_after') {
            $this->add_query_params('stale', $value, 'string');
        }
        return $this;
    }

    public function descending($value)
    {
        $this->add_query_params('descending', $value, 'json_encode_boolean');
        return $this;
    }

    public function skip($value)
    {
        $this->add_query_params('skip', $value, 'int');
        return $this;
    }

    public function group($value)
    {
        $this->add_query_params('group', $value, 'json_encode_boolean');
        return $this;
    }

    public function group_level($value)
    {
        $this->add_query_params('group_level', $value, 'int');
        return $this;
    }

    public function reduce($value)
    {
        $this->add_query_params('reduce', $value, 'json_encode_boolean');
        return $this;
    }

    public function include_docs($value)
    {
        $this->add_query_params('include_docs', $value, 'json_encode_boolean');
        return $this;
    }

    public function inclusive_end($value)
    {
        $this->add_query_params('inclusive_end', $value, 'json_encode_boolean');
        return $this;
    }

    public function attachments($value)
    {
        $this->add_query_params('attachments', $value, 'json_encode_boolean');
        return $this;
    }

    private function add_query_params($name, $args, $filter = null)
    {

        switch ($filter) {
            case 'int':
                $this->query_parameters[$name] = (int)$args;
                break;
            case 'json_encode':
                $this->query_parameters[$name] = json_encode($args);
                break;
            case 'ensure_array':
                if (is_array($args)) {
                    $this->query_parameters[$name] = $args;
                }
                break;
            case 'string':
                $this->query_parameters[$name] = (string)$args;
                break;
            case 'json_encode_boolean':
                $this->query_parameters[$name] = json_encode((boolean)$args);
                break;
            default:
                $this->query_parameters[$name] = $args;
                break;
        }

    }


    #endregion
}