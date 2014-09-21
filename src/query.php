<?php
/**
 * PHP URI Library
 * 
 * A PHP library for working with URI's, that is designed around the URI
 * standard. Requires PHP 5.4 or later. This library replaces and extends all
 * of PHP's parse_url() features, and even has some handy aliases.
 * 
 * Originally inspired by P Guardiario's work.
 * 
 * @author    Nicholas Jordon
 * @link      https://github.com/ProjectCleverWeb/PHP-URI
 * @copyright 2014 Nicholas Jordon - All Rights Reserved
 * @version   2.0.0
 * @package   projectcleverweb\uri\query
 * @license   http://opensource.org/licenses/MIT
 * @see       http://en.wikipedia.org/wiki/URI_scheme
 */

namespace projectcleverweb\uri;

/**
 * The Query Class
 * 
 * This class is resposible for checking and taking actions on the query
 * string. It should be noted that this class relies heavily on
 * generate::query_arr() and that excessive modification to the query
 * string should be done manually through generate::query_arr() and then
 * \projectcleverweb\uri\main::$query should be set (use http_build_query()).
 */
class query {
	
	/*** Variables ***/
	
	public    $build_spec;
	public    $build_prefix;
	public    $build_separator;
	protected $data;
	
	
	/*** Magic Methods ***/
	public function __construct($query_str) {
		$this->data            = parser::parse_query($query_str);
		$this->build_spec      = PHP_QUERY_RFC3986;
		$this->build_prefix    = '';
		$this->build_separator = '&';
	}
	
	/*** Methods ***/
	
	/**
	 * Builds the query under according to the 'build_*' variable settings.
	 * 
	 * @see http://php.net/manual/en/function.http-build-query.php
	 */
	private function str() {
		// return generate::query_str($this->data, $this->build_prefix, $this->build_separator, $this->build_spec);
		return http_build_query($this->data, $this->build_prefix, $this->build_separator, $this->build_spec);
	}
	
	/**
	 * Adds query var to the query string if it is not already set and returns
	 * TRUE. Otherwise it returns FALSE
	 */
	public function add($key, $value) {
		
	}
	
	/**
	 * Adds query var to the query string regardless if it already set or not
	 */
	public function replace($key, $value) {
		
	}
	
	/**
	 * Removes $key from the query if it exists
	 */
	public function remove($key) {
		
	}
	
	/**
	 * Checks if $key exists in the query, returns TRUE if $key exists, or
	 * FALSE otherwise.
	 */
	public function exists($key) {
		
	}
	
	/**
	 * Gets a specific var's value from the query. Returns NULL if $key
	 * DOES NOT exist.
	 */
	public function get($key) {
		
	}
	
	/**
	 * Renames a specific $key within the query. If the key exists within query
	 * string and is successfully renamed, the TRUE is returned. Otherwise
	 * FALSE is returned.
	 */
	public function rename($key, $new_key) {
		
	}
}
