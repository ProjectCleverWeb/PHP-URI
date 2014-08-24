<?php

namespace uri;

/**
 * Main URI Class
 * 
 * This class parses URI string into a dynamic and easy to use object. In
 * many ways, this class simply acts as an extension of a PHP string. Calling
 * this class as if it were a string will result in the current URI string
 * being used throughout PHP.
 */
abstract class main {
	/*** Variables ***/
	
	public $error;
	public $input;
	
	/*
	"Ghost" Variables
	=================
	These variables can be accesed from within the class, but as far as the rest
	of PHP is concerned, these variables simply don't exist.
	*/
	public $object;
	private $chain;
	
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
		$this->input  = $input;
		$this->object = \uri\parser::parse($input);
		
		if (!empty($this->object->host)) {
			\uri\generate::authority($this->object);
			\uri\generate::aliases($this->object);
			
			// Enable Chain Events
			$this->chain = new \uri\chain($this);
			
			// References required for Sudo-Private Variables
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
		return \uri\generate::string($this->object);
	}
	
	/**
	 * Invoked? just return the current URI as a string, nothing fancy.
	 * 
	 * @return string The current URI as a string
	 */
	public function __invoke() {
		return \uri\generate::string($this->object);
	}
	
	/**
	 * Because of how references are created within this class, cloning doesn't
	 * work as expected. This magic method warn's people until the issue can be
	 * correctly addressed. (It may be impossible to resolve this issue, with
	 * the current configuration)
	 * 
	 * @return void
	 */
	public function __clone() {
		$this->_err('CLONE', debug_backtrace(), 'clone');
	}
	
	/**
	 * Allows access to the different parts of the URI to be synchronized. This
	 * means that what is returned should always be accurate. Triggers a notice
	 * if the variable cannot be accessed.
	 * 
	 * @param  string $name The requested variable
	 * @return string|null  The value of the variable, or NULL if it can't be accessed
	 */
	public function __get($name) {
		if (isset($this->object->$name)) {
			\uri\generate::scheme($this->object);
			\uri\generate::authority($this->object);
			return $this->object->$name;
		} else {
			$this->_err('UNDEFINED', debug_backtrace(), $name);
			return NULL;
		}
	}
	
	/**
	 * Allows access to the different parts of the URI to be synchronized. This
	 * means that what is returned should always be accurate. Triggers a notice
	 * if the variable cannot be accessed.
	 * 
	 * @param  string $name  The requested variable
	 * @param  string $value The new value for the variable
	 * @return string|null   The new value of the variable, or NULL if it can't be accessed
	 */
	public function __set($name, $value) {
		if (isset($this->object->$name) && $name != 'authority') {
			\uri\actions::modify($this->object, 'replace', $name, $value);
			return $value;
		} else {
			$this->_err('FORBIDDEN', debug_backtrace(), $name);
			return NULL;
		}
	}
	
	/**
	 * Allows access to the different parts of the URI to be synchronized. This
	 * means that what is returned should always be accurate.
	 * 
	 * @param  string  $name The requested variable
	 * @return boolean       Returns TRUE if the variable is not empty, FALSE otherwise
	 */
	public function __isset($name) {
		\uri\generate::scheme($this->object);
		\uri\generate::authority($this->object);
		if (isset($this->object->$name)) {
			return !empty($this->object->$name);
		}
		return FALSE;
	}
	
	/**
	 * Allows access to the different parts of the URI to be synchronized. This
	 * means that what is returned should always be accurate. Triggers a notice
	 * if the variable cannot be accessed.
	 * 
	 * @param  string $name The requested variable
	 * @return boolean      Returns TRUE if the varaible was successfully emptied, FALSE otherwise.
	 */
	public function __unset($name) {
		if (isset($this->object->$name) && $name != 'host' && $name != 'authority') {
			\uri\actions::modify($this->object, 'replace', $name, '');
			return TRUE;
		} elseif (isset($this->object->$name)) {
			$this->_err('FORBIDDEN', debug_backtrace(), $name);
		} else {
			$this->_err('UNDEFINED', debug_backtrace(), $name);
		}
		return FALSE;
	}
	
	
	
	/*** Methods ***/
	
	/**
	 * Returns the current URI as a string.
	 * 
	 * @return string The current URI as a string
	 */
	public function str() {
		return \uri\generate::string($this->object);
	}
	
	/**
	 * Alias of str()
	 * 
	 * @return string The current URI as a string
	 */
	public function to_string() {
		return \uri\generate::string($this->object);
	}
	
	/**
	 * Prints the current URI as a string
	 * 
	 * @param  string $prepend The string to prepend to the output
	 * @param  string $append  The string to append to the output
	 * @return void
	 */
	public function p_str($prepend = '', $append = '') {
		echo $prepend.\uri\generate::string($this->object).$append;
	}
	
	/**
	 * Returns the current URI as an array
	 * 
	 * @return array The current URI as an array
	 */
	public function arr() {
		return \uri\generate::to_array($this->object);
	}
	
	/**
	 * Alias of arr()
	 * 
	 * @return array The current URI as an array
	 */
	public function to_array() {
		return \uri\generate::to_array($this->object);
	}
	
	/**
	 * The path broken down into dirname, basename, extension, filename, & array
	 * 
	 * @return array The information array
	 */
	public function path_info() {
		return \uri\generate::path_info($this->object);
	}
	
	/**
	 * The query parsed into an array
	 * 
	 * @return array The query array
	 */
	public function query_arr() {
		return \uri\generate::query_array($this->object);
	}
	
	/**
	 * Replaces $section of the URI with $str, given $str is a valid replacement
	 * 
	 * @param  string $section The section to replace
	 * @param  string $str     The string to replace the section with
	 * @return string|false    The resulting URI if the modification is valid, FALSE otherwise
	 */
	public function replace($section, $str) {
		return \uri\actions::modify($this->object, 'replace', $section, $str);
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
		return \uri\actions::modify($this->object, 'prepend', $section, $str);
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
		return \uri\actions::modify($this->object, 'append', $section, $str);
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
		return \uri\query::add($this->object, $key, $value);
	}
	
	/**
	 * Adds query var to the query string regardless if it already set or not
	 * 
	 * @param  string $key   The key to replace
	 * @param  mixed  $value The value of $key
	 * @return void
	 */
	public function query_replace($key, $value) {
		\uri\query::replace($this->object, $key, $value);
	}
	
	/**
	 * Removes $key from the query if it exists
	 * 
	 * @param  string $key The key to remove
	 * @return void
	 */
	public function query_remove($key) {
		\uri\query::remove($this->object, $key);
	}
	
	/**
	 * Checks if $key exists in the query
	 * 
	 * @param  string $key The key to search for
	 * @return boolean     TRUE if the $key exists, FALSE otherwise
	 */
	public function query_exists($key) {
		return \uri\query::exists($this->object, $key);
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
		return \uri\query::get($this->object, $key);
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
		return \uri\query::rename($this->object, $key, $new_key);
	}
	
	/**
	 * Returns the chain class, which allows events to be chained together
	 * rather than the reference being called several times. see \uri\chain
	 * 
	 * @return object The chain class
	 */
	public function chain() {
		return $this->chain;
	}
	
	/**
	 * Resets the current object to its initial state
	 * 
	 * @return void
	 */
	public function reset() {
		$this->__construct($this->input);
	}
	
	/**
	 * A unknown/forbidden property has been called. trigger an error
	 * 
	 * @param  string $type  Type of error
	 * @param  array  $trace The output from debug_backtrace()
	 * @param  string $name  Property name
	 * @return void
	 */
	private function _err($type, $trace, $name) {
		$fmt = 'Undifined property via <code>%1$s::%2$s()</code>: Property <code>%3$s</code> cannot be unset in <b>%4$s</b> on line <b>%5$s</b>. Error triggered';
		if ($type == 'FORBIDDEN') {
			$fmt = 'Forbidden property via <code>%1$s::%2$s()</code>: Property <code>%3$s</code> cannot be unset in <b>%4$s</b> on line <b>%5$s</b>. Error triggered';
		} elseif($type == 'CLONE') {
			$fmt = 'Invalid clone in <b>%4$s</b> on line <b>%5$s</b>. Because of how cloning works, and how references are configured within the class, extensions of %1$s cannot be cloned. Please make a new instance instead, like so: <code>$clone = new \\uri($original->str()); $clone->input = $original->input;</code>. Error triggered';
		}
		
		trigger_error(
			sprintf(
				$fmt,
				$trace[0]['class'],
				$trace[0]['function'],
				$name,
				$trace[0]['file'],
				$trace[0]['line']
			),
			E_USER_NOTICE
		);
	}
}
