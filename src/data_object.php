<?php
/**
 * PHP URI Library
 * 
 * A PHP library for working with URIs (aka URLs), that is designed around the
 * URI standard (RFC 3986). Requires PHP 5.4 or later. This library replaces
 * and extends all of PHP's parse_url() features, and adds several new features
 * for manipulating URI/URL strings.
 * 
 * @author    Nicholas Jordon
 * @link      https://github.com/ProjectCleverWeb/PHP-URI
 * @copyright 2014 Nicholas Jordon - All Rights Reserved
 * @version   2.0.0
 * @package   projectcleverweb\uri\data_object
 * @license   http://opensource.org/licenses/MIT
 * @see       http://en.wikipedia.org/wiki/URI_scheme
 */

namespace projectcleverweb\uri;

/**
 * The Primary Data Object
 * 
 * Using a defined class such as this has several advantages over a stdClass;
 * as a result, this allows for more features and optimizations to be build
 * into the other classes.
 */
class data_object {
	
	/**
	 * @var string $scheme         The scheme for the uri
	 * @var string $scheme_name    The name of the scheme for the uri
	 * @var string $scheme_symbols The scheme symbols for the uri
	 * @var string $user           The user for the uri
	 * @var string $pass           The password for the uri
	 * @var string $host           The host for the uri
	 * @var string $path           The path for the uri
	 * @var string $port           The port for the uri
	 * @var string $query          Unlike main::$query this is the actual query string
	 * @var string $fragment       The fragment for the uri
	 * @var string $protocol       Alias of $scheme (by reference)
	 * @var string $domain         Alias of $host (by reference)
	 * @var string $fqdn           Alias of $host (by reference)
	 * @var string $password       Alias of $pass (by reference)
	 * @var string $username       Alias of $user (by reference)
	 * @var string $authority      This is automatically generated
	 */
	public $scheme = ''; // vvv actual vvv
	public $scheme_name = '';
	public $scheme_symbols = '';
	public $user = '';
	public $pass = '';
	public $host = '';
	public $port = '';
	public $path = '';
	public $query = '';
	public $fragment = '';
	public $protocol; // vvv aliases vvv
	public $domain;
	public $username;
	public $password;
	public $fqdn;
	public $authority = ''; // generated
	
	public function __construct($data_array) {
		settype($data_array, 'array');
		$keys = array('scheme', 'scheme_name', 'scheme_symbols', 'user', 'pass', 'host', 'port', 'path', 'query', 'fragment');
		if (count(array_diff($keys, array_keys($data_array))) == 0) {
			foreach ($data_array as $key => &$value) {
				if (!is_string($value)) {
					$value = '';
				}
			}
			
			$this->scheme         = $data_array['scheme'];
			$this->scheme_name    = $data_array['scheme_name'];
			$this->scheme_symbols = $data_array['scheme_symbols'];
			$this->user           = $data_array['user'];
			$this->pass           = $data_array['pass'];
			$this->host           = $data_array['host'];
			$this->port           = $data_array['port'];
			$this->path           = $data_array['path'];
			$this->query          = $data_array['query'];
			$this->fragment       = $data_array['fragment'];
		}
		$this->protocol       = &$this->scheme;
		$this->domain         = &$this->host;
		$this->fqdn           = &$this->host;
		$this->username       = &$this->user;
		$this->password       = &$this->pass;
	}
}








