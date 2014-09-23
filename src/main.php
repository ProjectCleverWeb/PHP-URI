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
 * 
 * @property query $query This is an instance of the query class (treat as if 'public')
 */
abstract class main extends overloading {
	/*** Variables ***/
	
	/**
	 * @var string       $input The input of __construct() (this is always set, even if the input is invalid)
	 * @var false|string $error FALSE if everything is correcty parsed, is a string otherwise (error msg)
	 */
	public $input;
	public $error;
	
	/**
	 * "Ghost" Variables
	 * =================
	 * These variables can be accesed from within the class (or by parent/child
	 * classes), but as far as the rest of PHP is concerned, these variables
	 * simply don't exist. This basically means, if you don't know what your doing
	 * just leave these alone.
	 * 
	 * @var \stdClass $object The primary data object
	 * @var chain     $chain  The instance of 'chain' for this class (only accessible via chain())
	*/
	protected $object;
	protected $chain;
	
	/**
	 * Sudo-Private Variables
	 * ======================
	 * These variables can be accessed just like normal public variables, and can
	 * even be changed like public variables. This implementation of private
	 * variables combined with the __get(), __set(), __isset(), & __unset() magic
	 * constants allow each variable to stay in sync with the group, and still be
	 * accessable.
	 * 
	 * @var string $authority      This is automatically generated, and cannot be changed directly.
	 * @var string $domain         Alias of $host (by reference)
	 * @var string $fqdn           Alias of $host (by reference)
	 * @var string $fragment       The fragment for the uri
	 * @var string $host           The host for the uri (required for the URI to be correctly parsed)
	 * @var string $protocol       Alias of $scheme (by reference)
	 * @var string $pass           The password for the uri
	 * @var string $password       Alias of $pass (by reference)
	 * @var string $path           The path for the uri
	 * @var string $port           The port for the uri
	 * @var string $scheme         The scheme for the uri
	 * @var string $scheme_name    The name of the scheme for the uri
	 * @var string $scheme_symbols The scheme symbols for the uri
	 * @var string $user           The user for the uri
	 * @var string $username       Alias of $user (by reference)
	 * @var query  $query          The current instance of 'query' for this URI
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
	private $scheme;
	private $scheme_name;
	private $scheme_symbols;
	private $user;
	private $username;
	protected $query;
	
	
	
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
		$this->object = parser::parse_uri($input);
		
		if (!empty($this->object->host)) {
			$this->error = FALSE;
			$this->query = new query($this->object->query);
			generate::authority($this->object);
			generate::aliases($this->object);
			
			// Enable Chain Events
			$this->chain = new chain($this, $this->query);
			
			// References required for Sudo-Private Variables
			$this->_make_references();
		} else {
			$this->error = 'Input could not be parsed as a URI';
		}
	}
	
	/**
	 * In the event this class is called as or converted to a string, it will
	 * return the current URI string, and NOT cause any errors. Alias of str()
	 * 
	 * @return string The current URI as a string
	 */
	public function __toString() {
		return $this->str();
	}
	
	/**
	 * Just return this class when invoked
	 * 
	 * @return main The current instance of 'main'
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
	private function _make_references() {
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
		return generate::string($this, $this->object);
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
		echo $prepend.$this->str().$append;
	}
	
	/**
	 * Get the current URI as an array
	 * 
	 * @return array The current URI as an array
	 */
	public function arr() {
		return generate::to_array($this, $this->object);
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
		return $this->query->data;
	}
	
	/**
	 * Replaces $section of the URI with $str, given $str is a valid replacement
	 * 
	 * @param  string $section The section to replace
	 * @param  string $str     The string to replace the section with
	 * @return string|false    The resulting URI if the modification is valid, FALSE otherwise
	 */
	public function replace($section, $str) {
		return actions::modify($this, $this->object, __FUNCTION__, $section, $str);
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
		return actions::modify($this, $this->object, __FUNCTION__, $section, $str);
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
		return actions::modify($this, $this->object, __FUNCTION__, $section, $str);
	}
	
	/**
	 * Alias of $query->add()
	 * 
	 * @param  string $key   The key to add
	 * @param  mixed  $value The value of $key
	 * @return boolean       TRUE on success, FALSE otherwise
	 */
	public function query_add($key, $value) {
		return $this->query->add($key, $value);
	}
	
	/**
	 * Alias of $query->str()
	 * 
	 * @return string The current query string
	 */
	public function query_string() {
		return $this->query->str();
	}
	
	/**
	 * Alias of $query->replace()
	 * 
	 * @param  string $key   The key to replace
	 * @param  mixed  $value The value of $key
	 * @return void
	 */
	public function query_replace($key, $value) {
		$this->query->replace($key, $value);
	}
	
	/**
	 * Alias of $query->remove()
	 * 
	 * @param  string $key The key to remove
	 * @return void
	 */
	public function query_remove($key) {
		$this->query->remove($key);
	}
	
	/**
	 * Alias of $query->exists()
	 * 
	 * @param  string $key The key to search for
	 * @return boolean     TRUE if the $key exists, FALSE otherwise
	 */
	public function query_exists($key) {
		return $this->query->exists($key);
	}
	
	/**
	 * Alias of $query->get()
	 * 
	 * @param  string $key The key to get
	 * @return mixed|null  The value of $key, or NULL if it does not exist.
	 */
	public function query_get($key) {
		return $this->query->get($key);
	}
	
	/**
	 * Alias of $query->rename()
	 * 
	 * @param  string $key     The key to rename
	 * @param  string $new_key The new name of $key
	 * @return boolean         TRUE on success, FALSE otherwise
	 */
	public function query_rename($key, $new_key) {
		return $this->query->rename($key, $new_key);
	}
	
	/**
	 * Returns the current instance of the 'chain' class, which allows events to
	 * be chained together rather than the reference being called several times.
	 * 
	 * @return chain The current instance of the 'chain' class
	 */
	public function chain() {
		return $this->chain;
	}
	
	/**
	 * Returns the a new instance at the current state. This is meant to replace
	 * traditional cloning.
	 * 
	 * @return main A new instance of 'main' at the current state
	 */
	public function make_clone() {
		$clone        = new $this($this->str());
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
