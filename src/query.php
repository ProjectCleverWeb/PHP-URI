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
 * @package   projectcleverweb\uri\query
 * @license   http://opensource.org/licenses/MIT
 * @see       http://en.wikipedia.org/wiki/URI_scheme
 */

namespace projectcleverweb\uri;

/**
 * The Query Class
 * 
 * This class is acts as extension of a query string the same way the main
 * class acts as an extension of a URI string. However since there is no
 * complex overloading actions or reference variables, this class can be cloned
 * just fine. (NOTE: does implement the __toString() magic method)
 */
class query {
	
	/*** Variables ***/
	
	/**
	 * @var string  $input           The original string that was parsed
	 * @var array   $data            The parsed data as an array
	 * @var string  $build_prefix    The numeric prefix as defined by http_build_query()
	 * @var string  $build_separator The separator as defined by http_build_query()
	 * @var integer $build_enc       The encoding as defined by http_build_query()
	 */
	public $input;
	public $data;
	public $build_prefix;
	public $build_separator;
	public $build_enc;
	
	/*** Magic Methods ***/
	
	/**
	 * Parses a query string into a data array so it can be manipulated.
	 * 
	 * @param string  $query_str       The string to parse
	 * @param string  $build_prefix    The numeric prefix as defined by http_build_query()
	 * @param string  $build_separator The separator as defined by http_build_query()
	 * @param integer $build_enc       The encoding as defined by http_build_query()
	 */
	public function __construct($query_str = '', $build_prefix = '', $build_separator = '&', $build_enc = PHP_QUERY_RFC3986) {
		$this->input           = $query_str;
		$this->build_prefix    = $build_prefix;
		$this->build_separator = $build_separator;
		$this->build_enc       = $build_enc;
		$this->data            = parser::parse_query($this->input, $this->build_separator);
	}
	
	/**
	 * In the event this class is called as or converted to a string, it will
	 * return the current query string, and NOT cause any errors.
	 * 
	 * @return string The current query string
	 */
	public function __toString() {
		return $this->str();
	}
	
	/**
	 * Just return this class when invoked
	 * 
	 * @return query This instance
	 */
	public function __invoke() {
		return $this;
	}
	
	/*** Methods ***/
	
	/**
	 * Updates the build settings
	 * 
	 * @param  string $prefix    The numeric prefix according to the PHP docs
	 * @param  string $separator The separator you want to use in you query string (default is '&')
	 * @param  int    $enc       The encoding to use (default is RFC3986)
	 * @return void
	 */
	public function change_build($prefix = '', $separator = '&', $enc = PHP_QUERY_RFC3986) {
		$this->build_prefix    = $prefix;
		$this->build_separator = $separator;
		$this->build_enc       = $enc;
	}
	
	/**
	 * Builds the query under according to the 'build_*' variable settings.
	 * 
	 * @see http://php.net/manual/en/function.http-build-query.php
	 * @return string The current query string
	 */
	public function str() {
		return generate::query_str($this->data, $this->build_prefix, $this->build_separator, $this->build_enc);
	}
	
	/**
	 * Alias of str()
	 * 
	 * @return string The current query string
	 */
	public function to_string() {
		return $this->str();
	}
	
	/**
	 * Prints the current query string
	 * 
	 * @param  string $prepend The string to prepend to the output
	 * @param  string $append  The string to append to the output
	 * @return void
	 */
	public function p_str($prepend = '', $append = '') {
		echo $prepend.$this->str().$append;
	}
	
	/**
	 * Get the current query array
	 * 
	 * @return array The current query array
	 */
	public function arr() {
		return $this->data;
	}
	
	/**
	 * Alias of arr()
	 * 
	 * @return array The current query array
	 */
	public function to_array() {
		return $this->arr();
	}
	
	/**
	 * Adds query var to the query string if it is not already set and returns
	 * TRUE. Otherwise it returns FALSE. (does not recurse into $data)
	 * 
	 * @param  string $key   The key to add data to
	 * @param  mixed  $value The data to add
	 * @return boolean       TRUE if $key was set, FALSE if the key already existed
	 */
	public function add($key, $value = '') {
		if (!isset($this->data[$key])) {
			$this->data[$key] = $value;
			return TRUE;
		}
		return FALSE;
	}
	
	/**
	 * Adds query var to the query string regardless if it already set or not.
	 * (does not recurse into $data)
	 * 
	 * @param  string $key   The key to replace (or add)
	 * @param  mixed  $value The data to add
	 * @return void
	 */
	public function replace($key, $value = '') {
		$this->data[$key] = $value;
	}
	
	/**
	 * Removes $key from the query if it exists. (does not recurse into $data)
	 * 
	 * @param  string $key The key to remove (if it exists)
	 * @return void
	 */
	public function remove($key) {
		if (isset($this->data[$key])) {
			unset($this->data[$key]);
		}
	}
	
	/**
	 * Checks if $key exists in the query, returns TRUE if $key exists, or
	 * FALSE otherwise. (does not recurse into $data)
	 * 
	 * @param  string $key The key to check
	 * @return boolean     If $key exists then TRUE, FALSE otherwise
	 */
	public function exists($key) {
		return isset($this->data[$key]);
	}
	
	/**
	 * Gets a specific var's value from the query. Returns NULL if $key
	 * DOES NOT exist. (does not recurse into $data)
	 * 
	 * @param  string $key The key to get
	 * @return mixed  The value of $key of it exists, NULL otherwise
	 */
	public function get($key) {
		if (isset($this->data[$key])) {
			return $this->data[$key];
		}
		return NULL;
	}
	
	/**
	 * Renames a specific $key within the query. If the key exists within query
	 * string and is successfully renamed, then TRUE is returned. Otherwise
	 * FALSE is returned. (does not recurse into $data)
	 * 
	 * @param  string $key     The key to rename
	 * @param  string $new_key The new name of $key
	 * @return boolean         TRUE if the key existed and was replaced, FALSE otherwise
	 */
	public function rename($key, $new_key) {
		if (isset($this->data[$key])) {
			$this->data[$new_key] = $this->data[$key];
			unset($this->data[$key]);
			return TRUE;
		}
		return FALSE;
	}
	
	/**
	 * Cloning works just fine in this class, but some may think that because the
	 * main class requires you to use make_clone() so will this class. Since it's
	 * not a big waste, just pass them back a regular clone. I am sure this will
	 * help prevent some confusion/errors.
	 * 
	 * @return query A clone of this instance
	 */
	public function make_clone() {
		return clone $this;
	}
	
	/**
	 * Resets the current object to its initial state
	 * 
	 * @return void
	 */
	public function reset() {
		$this->__construct($this->input, $this->build_prefix, $this->build_separator, $this->build_enc);
	}
}
