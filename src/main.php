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
 * @package   projectcleverweb\uri\main
 * @license   http://opensource.org/licenses/MIT
 * @see       http://en.wikipedia.org/wiki/URI_scheme
 */

namespace projectcleverweb\uri;

/**
 * Main URI Class
 * 
 * This class parses URI string into a dynamic and easy to use object. In
 * many ways, this class simply acts as an extension of a PHP string. Calling
 * this class as if it were a string will result in the current URI string
 * being used throughout PHP.
 */
abstract class main extends overloading {
	/*** Variables ***/
	
	public $error;
	public $input;
	
	/*
	"Ghost" Variables
	=================
	These variables can be accesed from within the class (or by parent/child
	classes), but as far as the rest of PHP is concerned, these variables
	simply don't exist. This basically means, if you don't know what your doing
	just leave these alone.
	*/
	protected $object;
	protected $chain;
	
	/*
	Sudo-Private Variables
	======================
	These variables can be accessed just like normal public variables, and can
	even be changed like public variables. This implementation of private
	variables combined with the __get(), __set(), __isset(), & __unset() magic
	constants allow each variable to stay in sync with the group, and still be
	accessable.
	*/
	private $authority;
	private $domain;
	private $fqdn;
	private $fragment;
	private $host;
	private $protocol;
	private $pass;
	private $password;
	private $path;
	private $port;
	private $query;
	private $scheme;
	private $scheme_name;
	private $scheme_symbols;
	private $user;
	private $username;
	
	
	
	/*** Magic Methods ***/
	
	/**
	 * Parses the input as a URI and populates the variables. Fails if input is
	 * not a string or if the string cannot be parsed as a URI.
	 * 
	 * @param string $input The URI to parse.
	 */
	public function __construct($input) {
		$this->input = $input;
		if (!is_string($input)) {
			$input = '';
		}
		$this->object = parser::parse($input);
		
		if (!empty($this->object->host)) {
			generate::authority($this->object);
			generate::aliases($this->object);
			
			// Enable Chain Events
			$this->chain = new chain($this);
			
			// References required for Sudo-Private Variables
			$this->_make_references();
		} else {
			$this->error = 'Input could not be parsed as a URI';
		}
	}
	
	/**
	 * In the event this class is called as or converted to a string, it will
	 * return the current URI string, and NOT cause any errors.
	 * 
	 * @return string The current URI as a string
	 */
	public function __toString() {
		return $this->str();
	}
	
	/**
	 * Invoked? just return the current URI as a string, nothing fancy.
	 * 
	 * @return string The current URI as a string
	 */
	public function __invoke() {
		return $this;
	}
	
	/**
	 * Because of how references are created within this class, cloning doesn't
	 * work as expected. This magic method warn's people to use make_clone()
	 * instead.
	 * 
	 * @see    http://stackoverflow.com/questions/25420812/issue-with-cloning-and-pass-by-reference
	 * @return void
	 */
	public function __clone() {
		$trace = debug_backtrace();
		$fmt   = 'Invalid clone in <b>%2$s</b> on line <b>%3$s</b>. Because of how cloning works, and how references are configured within the class, extensions of %1$s cannot be cloned. Please use <code>%1$s->make_clone()</code> instead. Error triggered';
		trigger_error(
			sprintf($fmt, $trace[0]['class'], $trace[0]['file'], $trace[0]['line']),
			E_USER_NOTICE
		);
	}
	
	
	
	/*** Methods ***/
	
	/**
	 * Generates all the references to $this->object from $this
	 * 
	 * @return void
	 */
	public function _make_references() {
		$this->authority      = &$this->object->authority;
		$this->domain         = &$this->object->domain;
		$this->fqdn           = &$this->object->fqdn;
		$this->fragment       = &$this->object->fragment;
		$this->host           = &$this->object->host;
		$this->protocol       = &$this->object->protocol;
		$this->pass           = &$this->object->pass;
		$this->password       = &$this->object->password;
		$this->path           = &$this->object->path;
		$this->port           = &$this->object->port;
		$this->query          = &$this->object->query;
		$this->scheme         = &$this->object->scheme;
		$this->scheme_name    = &$this->object->scheme_name;
		$this->scheme_symbols = &$this->object->scheme_symbols;
		$this->user           = &$this->object->user;
		$this->username       = &$this->object->username;
	}
	
	/**
	 * Returns the current URI as a string.
	 * 
	 * @return string The current URI as a string
	 */
	public function str() {
		return generate::string($this->object);
	}
	
	/**
	 * Alias of str()
	 * 
	 * @return string The current URI as a string
	 */
	public function to_string() {
		return $this->str();
	}
	
	/**
	 * Prints the current URI as a string
	 * 
	 * @param  string $prepend The string to prepend to the output
	 * @param  string $append  The string to append to the output
	 * @return void
	 */
	public function p_str($prepend = '', $append = '') {
		echo $prepend.generate::string($this->object).$append;
	}
	
	/**
	 * Returns the current URI as an array
	 * 
	 * @return array The current URI as an array
	 */
	public function arr() {
		return generate::to_array($this->object);
	}
	
	/**
	 * Alias of arr()
	 * 
	 * @return array The current URI as an array
	 */
	public function to_array() {
		return $this->arr();
	}
	
	/**
	 * The path broken down into dirname, basename, extension, filename, & array
	 * 
	 * @return array The information array
	 */
	public function path_info() {
		return generate::path_info($this->object);
	}
	
	/**
	 * The query parsed into an array
	 * 
	 * @return array The query array
	 */
	public function query_arr() {
		return generate::query_array($this->object);
	}
	
	/**
	 * Replaces $section of the URI with $str, given $str is a valid replacement
	 * 
	 * @param  string $section The section to replace
	 * @param  string $str     The string to replace the section with
	 * @return string|false    The resulting URI if the modification is valid, FALSE otherwise
	 */
	public function replace($section, $str) {
		return actions::modify($this->object, __FUNCTION__, $section, $str);
	}
	
	/**
	 * Prepends $section of the URI with $str, given the $section is still valid
	 * once $str is in place
	 * 
	 * @param  string $section The section to prepend
	 * @param  string $str     The string to prepend the section with
	 * @return string|false    The resulting URI if the modification is valid, FALSE otherwise
	 */
	public function prepend($section, $str) {
		return actions::modify($this->object, __FUNCTION__, $section, $str);
	}
	
	/**
	 * Appends $section of the URI with $str, given $section is still valid
	 * once $str is in place
	 * 
	 * @param  string $section The section to append
	 * @param  string $str     The string to append the section with
	 * @return string|false    The resulting URI if the modification is valid, FALSE otherwise
	 */
	public function append($section, $str) {
		return actions::modify($this->object, __FUNCTION__, $section, $str);
	}
	
	/**
	 * Adds query var to the query string if it is not already set and returns
	 * TRUE. Otherwise it returns FALSE
	 * 
	 * @param  string $key   The key to add
	 * @param  mixed  $value The value of $key
	 * @return boolean       TRUE on success, FALSE otherwise
	 */
	public function query_add($key, $value) {
		return query::add($this->object, $key, $value);
	}
	
	/**
	 * Adds query var to the query string regardless if it already set or not
	 * 
	 * @param  string $key   The key to replace
	 * @param  mixed  $value The value of $key
	 * @return void
	 */
	public function query_replace($key, $value) {
		query::replace($this->object, $key, $value);
	}
	
	/**
	 * Removes $key from the query if it exists
	 * 
	 * @param  string $key The key to remove
	 * @return void
	 */
	public function query_remove($key) {
		query::remove($this->object, $key);
	}
	
	/**
	 * Checks if $key exists in the query
	 * 
	 * @param  string $key The key to search for
	 * @return boolean     TRUE if the $key exists, FALSE otherwise
	 */
	public function query_exists($key) {
		return query::exists($this->object, $key);
	}
	
	/**
	 * Gets a specific var's value from the query. It is HIGHLY recommended
	 * that you use query_arr() instead, when fetching multiple values from
	 * the same query string. Returns NULL if $key does not exist.
	 * 
	 * @param  string $key The key to get
	 * @return mixed|null  The value of $key, or NULL if it does not exist.
	 */
	public function query_get($key) {
		return query::get($this->object, $key);
	}
	
	/**
	 * Renames a specific $key within the query. If the key exists within query
	 * string and is successfully renamed, the TRUE is returned. Otherwise
	 * FALSE is returned.
	 * 
	 * @param  string $key     The key to rename
	 * @param  string $new_key The new name of $key
	 * @return boolean         TRUE on success, FALSE otherwise
	 */
	public function query_rename($key, $new_key) {
		return query::rename($this->object, $key, $new_key);
	}
	
	/**
	 * Returns the chain class, which allows events to be chained together
	 * rather than the reference being called several times. see
	 * \projectcleverweb\uri\chain
	 * 
	 * @return object The chain class
	 */
	public function chain() {
		return $this->chain;
	}
	
	/**
	 * Returns the a new instance at the current state. This is meant to replace
	 * traditional cloning.
	 * 
	 * @return object A new instance at the current state
	 */
	public function make_clone() {
		$clone        = new $this(generate::string($this->object));
		$clone->input = $this->input;
		return $clone;
	}
	
	/**
	 * Resets the current object to its initial state
	 * 
	 * @return void
	 */
	public function reset() {
		$this->__construct($this->input);
	}
}
