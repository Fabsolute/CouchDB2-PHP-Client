<?php
/**
 * Exception for 500 Internal Server Error responses
 *
 * @package Requests
 */

/**
 * Exception for 500 Internal Server Error responses
 *
 * @package Requests
 */
namespace Fabs\CouchDB2\Http;
class Requests_Exception_HTTP_500 extends Requests_Exception_HTTP {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 500;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Internal Server Error';
}