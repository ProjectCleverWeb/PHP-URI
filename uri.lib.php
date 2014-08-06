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
 * @version   1.0.0
 * @license   http://opensource.org/licenses/MIT
 * @see       http://en.wikipedia.org/wiki/URI_scheme
 */

namespace {
	/**
	 * URI Class
	 * 
	 * This class acts as a callable alias to the \uri\main abstract class.
	 */
	class uri extends \uri\main {}
}

namespace uri {
	
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
	
	
	
	/**
	 * The Parser Class
	 * 
	 * This class controls how the initial input is parsed. This class is
	 * designed to be easily upgraded to use different types of parsing. should
	 * it be desired.
	 */
	class parser {
		/*** Constants ***/
		
		// This regex is broken down to be readable in regex_parse()
		const REGEX = '/^(([a-z]+)?(\:\/\/|\:|\/\/))?(?:([a-z0-9$_\.\+!\*\'\(\),;&=\-]+)(?:\:([a-z0-9$_\.\+!\*\'\(\),;&=\-]*))?@)?((?:\d{3}.\d{3}.\d{3}.\d{3})|(?:[a-z0-9\-_]+(?:\.[a-z0-9\-_]+)*))(?:\:([0-9]+))?((?:\:|\/)[a-z0-9\-_\/\.]+)?(?:\?([a-z0-9$_\.\+!\*\'\(\),;:@&=\-%]*))?(?:#([a-z0-9\-_]*))?/i';
		
		/*** Methods ***/
		
		/**
		 * Wrapper function for parsing a string into a URI object
		 * 
		 * @param  string $uri  The input to be parsed as a URI
		 * @return object       If the input can be correctly parsed, then it returns an object with at least the 'host' populated
		 */
		public static function parse($uri) {
			if (!is_string($uri)) {
				return FALSE;
			}
			
			$parsed = self::regex_parse($uri);
			
			// Could not be parsed correctly
			if (empty($parsed)) {
				$parsed = array_fill(1, 10, '');
			}
			
			return (object) array(
				'scheme'         => $parsed[1],
				'scheme_name'    => $parsed[2],
				'scheme_symbols' => $parsed[3],
				'user'           => $parsed[4],
				'pass'           => $parsed[5],
				'host'           => $parsed[6],
				'port'           => $parsed[7],
				'path'           => $parsed[8],
				'query'          => $parsed[9],
				'fragment'       => $parsed[10],
			);
		}
		
		/**
		 * Parses a URI string into a usable array.
		 * 
		 * @param  string $uri The URI to be parsed
		 * @return array|false Returns an array if the sting could be correctly parsed, FALSE otherwise
		 */
		private static function regex_parse($uri) {
			// $regex = (
			//   '/'.
			//   '^(([a-z]+)?(\:\/\/|\:|\/\/))?'.              // Scheme, Scheme Name, & Scheme Symbols
			//   '(?:'.                                        // Auth Start
			//     '([a-z0-9$_\.\+!\*\'\(\),;&=\-]+)'.         // Username
			//     '(?:\:([a-z0-9$_\.\+!\*\'\(\),;&=\-]*))?'.  // Password
			//   '@)?'.                                        // Auth End
			//   '('.                                          // Host Start
			//     '(?:\d{3}.\d{3}.\d{3}.\d{3})'.              // IP Address
			//     '|'.                                        // -OR-
			//     '(?:[a-z0-9\-_]+(?:\.[a-z0-9\-_]+)*)'.      // Domain Name
			//   ')'.                                          // Host End
			//   '(?:\:([0-9]+))?'.                            // Port
			//   '((?:\:|\/)[a-z0-9\-_\/\.]+)?'.               // Path
			//   '(?:\?([a-z0-9$_\.\+!\*\'\(\),;:@&=\-%]*))?'. // Query
			//   '(?:#([a-z0-9\-_]*))?'.                       // Fragment
			//   '/i'
			// );
			preg_match_all(self::REGEX, $uri, $parsed, PREG_SET_ORDER);
			
			// Host is required
			if (!isset($parsed[0][6])) {
				return FALSE;
			}
			
			// Return what was parsed, but make sure that each offset is set regardless
			return $parsed[0] + array_fill(0, 11, '');
		}
	}
	
	
	
	/**
	 * The Modifier Class
	 * 
	 * This class is in charge of making user-based changes.
	 */
	class modify {
		/*** Methods ***/
		
		/**
		 * Modfies the Scheme Name
		 * 
		 * @param  object $object  The object to modify
		 * @param  string $action  The action to take
		 * @param  string $str     The modfication
		 * @return string|false    Returns the resulting URI on success, FALSE otherwise
		 */
		public static function scheme_name(&$object, $action, $str) {
			$org = $object->scheme_name;
			\uri\actions::callback($object, $action, __FUNCTION__, $str);
			if (!(preg_match('/\A[a-z]{1,10}\Z/', $object->scheme_name) || empty($str))) {
				$object->scheme_name = $org;
				return FALSE;
			} elseif (empty($object->scheme_symbols)) {
				$object->scheme_symbols = '://';
			}
			
			return \uri\generate::string($object);
		}
		
		/**
		 * Modfies the Scheme Symbols
		 * 
		 * @param  object $object  The object to modify
		 * @param  string $action  The action to take
		 * @param  string $str     The modfication
		 * @return string|false    Returns the resulting URI on success, FALSE otherwise
		 */
		public static function scheme_symbols(&$object, $action, $str) {
			$org = $object->scheme_symbols;
			\uri\actions::callback($object, $action, __FUNCTION__, $str);
			if (!(preg_match('/\A(:)?([\/]{2,3})?\Z/', $object->scheme_symbols) || empty($str))) {
				$object->scheme_symbols = $org;
				return FALSE;
			}
			
			return \uri\generate::string($object);
		}
		
		/**
		 * Modfies the Scheme
		 * 
		 * @param  object $object  The object to modify
		 * @param  string $action  The action to take
		 * @param  string $str     The modfication
		 * @return string|false    Returns the resulting URI on success, FALSE otherwise
		 */
		public static function scheme(&$object, $action, $str) {
			$org = array($object->scheme, $object->scheme_name, $object->scheme_symbols);
			\uri\actions::callback($object, $action, __FUNCTION__, $str);
			if (empty($object->scheme)) {
				$object->scheme = $object->scheme_name = $object->scheme_symbols = '';
			} else {
				preg_match('/\A([a-z]{1,10})?(\:|:\/\/|\/\/|:\/\/\/)\Z/i', $object->scheme, $matches);
				if (empty($matches[1]) && empty($matches[2])) {
					// restore values
					$object->scheme         = $org[0];
					$object->scheme_name    = $org[1];
					$object->scheme_symbols = $org[2];
					return FALSE;
				} else {
					// apply changes
					$matches                = $matches + array('', '', '');
					$object->scheme         = $matches[0];
					$object->scheme_name    = $matches[1];
					$object->scheme_symbols = $matches[2];
				}
			}
			
			return \uri\generate::string($object);
		}
		
		/**
		 * Alias of scheme()
		 * 
		 * @param  object $object  The object to modify
		 * @param  string $action  The action to take
		 * @param  string $str     The modfication
		 * @return string|false    Returns the resulting URI on success, FALSE otherwise
		 */
		public static function protocol(&$object, $action, $str) {
			return self::scheme($object, $action, $str);
		}
		
		/**
		 * Modfies the Username
		 * 
		 * @param  object $object  The object to modify
		 * @param  string $action  The action to take
		 * @param  string $str     The modfication
		 * @return string          Returns the resulting URI on success, FALSE otherwise
		 */
		public static function user(&$object, $action, $str) {
			$str = rawurlencode($str);
			
			\uri\actions::callback($object, $action, __FUNCTION__, $str);
			return \uri\generate::string($object);
		}
		
		/**
		 * Alias of user()
		 * 
		 * @param  object $object  The object to modify
		 * @param  string $action  The action to take
		 * @param  string $str     The modfication
		 * @return string          Returns the resulting URI on success, FALSE otherwise
		 */
		public static function username(&$object, $action, $str) {
			return self::user($object, $action, $str);
		}
		
		/**
		 * Modfies the Password
		 * 
		 * @param  object $object  The object to modify
		 * @param  string $action  The action to take
		 * @param  string $str     The modfication
		 * @return string          Returns the resulting URI on success, FALSE otherwise
		 */
		public static function pass(&$object, $action, $str) {
			$str = rawurlencode($str);
			
			\uri\actions::callback($object, $action, __FUNCTION__, $str);
			return \uri\generate::string($object);
		}
		
		/**
		 * Alias of pass()
		 * 
		 * @param  object $object  The object to modify
		 * @param  string $action  The action to take
		 * @param  string $str     The modfication
		 * @return string          Returns the resulting URI on success, FALSE otherwise
		 */
		public static function password(&$object, $action, $str) {
			return self::pass($object, $action, $str);
		}
		
		/**
		 * Modfies the Host
		 * 
		 * @param  object $object  The object to modify
		 * @param  string $action  The action to take
		 * @param  string $str     The modfication
		 * @return string|false    Returns the resulting URI on success, FALSE otherwise
		 */
		public static function host(&$object, $action, $str) {
			$org = $object->host;
			\uri\actions::callback($object, $action, __FUNCTION__, $str);
			if ((
				!preg_match('/\A(([a-z0-9_]([a-z0-9\-_]+)?)\.)+[a-z0-9]([a-z0-9\-]+)?\Z/i', $object->host) // fqdn
				&&
				!preg_match('/\A([0-9]\.){3}[0-9]\Z/i', $object->host) // ip
			)) {
				$object->host = $org;
				return FALSE;
			}
			
			return \uri\generate::string($object);
		}
		
		/**
		 * Alias of host()
		 * 
		 * @param  object $object  The object to modify
		 * @param  string $action  The action to take
		 * @param  string $str     The modfication
		 * @return string|false    Returns the resulting URI on success, FALSE otherwise
		 */
		public static function domain(&$object, $action, $str) {
			return self::host($object, $action, $str);
		}
		
		/**
		 * Alias of host()
		 * 
		 * @param  object $object  The object to modify
		 * @param  string $action  The action to take
		 * @param  string $str     The modfication
		 * @return string|false    Returns the resulting URI on success, FALSE otherwise
		 */
		public static function fqdn(&$object, $action, $str) {
			return self::host($object, $action, $str);
		}
		
		/**
		 * Modfies the Port
		 * 
		 * @param  object $object  The object to modify
		 * @param  string $action  The action to take
		 * @param  string $str     The modfication
		 * @return string|false    Returns the resulting URI on success, FALSE otherwise
		 */
		public static function port(&$object, $action, $str) {
			$org = $object->port;
			if (isset($str[0]) && $str[0] == ':') {
				$str = substr($str, 1);
			}
			\uri\actions::callback($object, $action, __FUNCTION__, $str);
			if (!(preg_match('/\A[0-9]{0,5}\Z/', $object->port) || empty($str))) {
				$object->port = $org;
				return FALSE;
			}
			
			return \uri\generate::string($object);
		}
		
		/**
		 * Modfies the Path
		 * 
		 * @param  object $object  The object to modify
		 * @param  string $action  The action to take
		 * @param  string $str     The modfication
		 * @return string          Returns the resulting URI on success, FALSE otherwise
		 */
		public static function path(&$object, $action, $str) {
			\uri\actions::callback($object, $action, __FUNCTION__, $str);
			return \uri\generate::string($object);
		}
		
		/**
		 * Modfies the Query
		 * 
		 * @param  object $object  The object to modify
		 * @param  string $action  The action to take
		 * @param  string $str     The modfication
		 * @return string          Returns the resulting URI on success, FALSE otherwise
		 */
		public static function query(&$object, $action, $str) {
			if (isset($str[0]) && $str[0] == '?') {
				$str = substr($str, 1);
			}
			
			\uri\actions::callback($object, $action, __FUNCTION__, $str);
			return \uri\generate::string($object);
		}
		
		/**
		 * Modfies the Fragment
		 * 
		 * @param  object $object  The object to modify
		 * @param  string $action  The action to take
		 * @param  string $str     The modfication
		 * @return string          Returns the resulting URI on success, FALSE otherwise
		 */
		public static function fragment(&$object, $action, $str) {
			if (isset($str[0]) && $str[0] == '#') {
				$str = substr($str, 1);
			}
			$str = urlencode($str);
			
			\uri\actions::callback($object, $action, __FUNCTION__, $str);
			return \uri\generate::string($object);
		}
	}
	
	
	
	/**
	 * The Actions Class
	 * 
	 * This class handlles the available actions
	 */
	class actions {
		
		/**
		 * Acts as universal alias to the modify class, ensuring the call is viable
		 * 
		 * @param  object $object  The object to modify
		 * @param  string $action  The action to take
		 * @param  string $section The section of the object to modify
		 * @param  string $str     The modfication
		 * @return string|false    Returns the resulting URI on success, FALSE otherwise
		 */
		public static function modify(&$object, $action, $section, $str) {
			settype($section, 'string');
			settype($str, 'string');
			$section = strtolower($section);
			
			if (is_callable(array('\\uri\\modify', $section))) {
				return call_user_func_array(array('\\uri\\modify', $section), array(&$object, $action, $str));
			} else {
				return FALSE;
			}
		}
		
		/**
		 * Handles which action is taken; since there are only 3 very simple
		 * actions, it makes sense to put them all in 1 method.
		 * 
		 * @param  object $object  The object to modify
		 * @param  string $action  The action to take
		 * @param  string $section The section of the object to modify
		 * @param  string $str     The modfication
		 * @return void
		 */
		public static function callback(&$object, $action, $section, $str) {
			switch ($action) {
				case 'replace':
					$object->$section = $str;
					break;
				case 'prepend':
					$object->$section = $str.$object->$section;
					break;
				case 'append':
					$object->$section = $object->$section.$str;
			}
		}
		
	}
	
	
	
	/**
	 * The Generator Class
	 * 
	 * This class makes sure everything stays in sync and is produced correctly.
	 * Unlike the the modify class, this class only changes $object to keep
	 * things syncronized. It's primary purpose is to use the information in
	 * $object to create some type of returnable value.
	 */
	class generate {
		
		/*** Methods ***/
		
		/**
		 * Generates all the aliases for $object
		 * 
		 * @param  object $object The object to modify
		 * @return void
		 */
		public static function aliases(&$object) {
			$object->protocol = &$object->scheme;
			$object->username = &$object->user;
			$object->password = &$object->pass;
			$object->domain   = &$object->host;
			$object->fqdn     = &$object->host;
		}
		
		/**
		 * Generate the scheme. This method exists to make changing how the scheme
		 * is generated easier; and will likely help prevent redundant code in the
		 * future
		 * 
		 * @param  object $object The object to modify
		 * @return void
		 */
		public static function scheme(&$object) {
			$object->scheme = $object->scheme_name.$object->scheme_symbols;
		}
		
		/**
		 * Regenerates the Authority string
		 * 
		 * @param  object $object The object to modify
		 * @return void
		 */
		public static function authority(&$object) {
			$str_arr = array($object->user);
			if (empty($object->user) == FALSE && empty($object->pass)) {
				$str_arr[] = '@';
			} elseif (!empty($object->user)) {
				$str_arr[] = ':'.$object->pass.'@';
			}
			$str_arr[] = $object->host;
			if (!empty($object->port)) {
				$str_arr[] = ':'.$object->port;
			}
			$object->authority = implode('', $str_arr);
		}
		
		/**
		 * Generate a the full URI as a string, from the current object
		 * 
		 * @param  object $object The object to use
		 * @return string         The current URI string
		 */
		public static function string(&$object) {
			self::scheme($object);
			self::authority($object);
			$str_arr = array($object->scheme, $object->authority, $object->path);
			if (!empty($object->query)) {
				$str_arr[] = '?'.$object->query;
			}
			if (!empty($object->fragment)) {
				$str_arr[] = '#'.$object->fragment;
			}
			return implode('', $str_arr);
		}
		
		/**
		 * Generate a the full URI as a string, from the current object
		 * 
		 * @param  object $object The object to use
		 * @return array          The current URI as an array
		 */
		public static function to_array(&$object) {
			$arr = array(
				'authority'      => $object->authority,
				'fragment'       => $object->fragment,
				'host'           => $object->host,
				'pass'           => $object->pass,
				'path'           => $object->path,
				'port'           => $object->port,
				'query'          => $object->query,
				'scheme'         => $object->scheme,
				'scheme_name'    => $object->scheme_name,
				'scheme_symbols' => $object->scheme_symbols,
				'user'           => $object->user,
			);
			
			$arr['domain']   = &$arr['host'];
			$arr['fqdn']     = &$arr['host'];
			$arr['password'] = &$arr['pass'];
			$arr['protocol'] = &$arr['scheme'];
			$arr['username'] = &$arr['user'];
			
			ksort($arr);
			return $arr;
		}
		
		/**
		 * Returns various information about the current $path
		 * 
		 * @param  object $object The object to use
		 * @return array          Associative array of information about the current $path
		 */
		public static function path_info(&$object) {
			$defaults = array(
				'dirname' => '',
				'basename' => '',
				'extension' => '',
				'filename' => '',
				'array' => array()
			);
			
			$info          = pathinfo($object->path) + $defaults;
			$info['array'] = array_values(array_filter(explode('/', $object->path)));
			ksort($info);
			
			return $info;
		}
		
		/**
		 * The current $query string parsed into an array
		 * 
		 * @param  object $object The object to use
		 * @return array          The query string as an array
		 */
		public static function query_array(&$object) {
			parse_str($object->query, $return);
			return (array) $return;
		}
		
	}
	
	
	/**
	 * The Query Class
	 * 
	 * This class is resposible for checking and taking actions on the query
	 * string. It should be noted that this class relies heavily on
	 * generate::query_arr() and that excessive modification to the query
	 * string should be done manually through generate::query_arr() and then
	 * \uri\main::$query should be set (use http_build_query()).
	 */
	class query {
		
		/*** Methods ***/
		
		/**
		 * Builds the query under RFC3986. RFC3986 is used as a replacement for
		 * RFC1738 because it is more portable.
		 * 
		 * @param  array $query_array The array to build the query string from
		 * @return string             The resulting query string
		 */
		private static function build_query($query_array) {
			return http_build_query($query_array, '', '&', PHP_QUERY_RFC3986);
		}
		
		/**
		 * Adds query var to the query string if it is not already set and returns
		 * TRUE. Otherwise it returns FALSE
		 * 
		 * @param  object $object The object to modify
		 * @param  string $key    The key to add
		 * @param  string $value  The value of $key
		 * @return boolean        TRUE on success, FALSE otherwise
		 */
		public static function add(&$object, $key, $value) {
			$qarray = \uri\generate::query_array($object);
			if (!isset($qarray[$key])) {
				$qarray[$key] = $value;
				\uri\actions::modify($object, 'replace', 'QUERY', self::build_query($qarray));
				return TRUE;
			}
			return FALSE;
		}
		
		/**
		 * Adds query var to the query string regardless if it already set or not
		 * 
		 * @param  object $object The object to modify
		 * @param  string $key    The key to replace
		 * @param  string $value  The value of $key
		 * @return void
		 */
		public static function replace(&$object, $key, $value) {
			$qarray       = \uri\generate::query_array($object);
			$qarray[$key] = $value;
			\uri\actions::modify($object, 'replace', 'QUERY', self::build_query($qarray));
		}
		
		/**
		 * Removes $key from the query if it exists
		 * 
		 * @param  object $object The object to modify
		 * @param  string $key    The key to remove
		 * @return void
		 */
		public static function remove(&$object, $key) {
			$qarray = \uri\generate::query_array($object);
			if (isset($qarray[$key])) {
				unset($qarray[$key]);
				\uri\actions::modify($object, 'replace', 'QUERY', self::build_query($qarray));
			}
		}
		
		/**
		 * Checks if $key exists in the query
		 * 
		 * @param  object $object The object to use
		 * @param  string $key    The key to search for
		 * @return boolean        TRUE if the $key exists, FALSE otherwise
		 */
		public static function exists(&$object, $key) {
			$qarray = \uri\generate::query_array($object);
			return isset($qarray[$key]);
		}
		
		/**
		 * Gets a specific var's value from the query. It is HIGHLY recommended
		 * that you use query_arr() instead, when fetching multiple values from
		 * the same query string. Returns NULL if $key does not exist.
		 * 
		 * @param  object $object The object to use
		 * @param  string $key    The key to get
		 * @return mixed|null     The value of $key, or NULL if it does not exist.
		 */
		public static function get(&$object, $key) {
			$qarray = \uri\generate::query_array($object);
			if (isset($qarray[$key])) {
				return $qarray[$key];
			}
			return NULL;
		}
		
		/**
		 * Renames a specific $key within the query. If the key exists within query
		 * string and is successfully renamed, the TRUE is returned. Otherwise
		 * FALSE is returned.
		 * 
		 * @param  object $object  The object to modify
		 * @param  string $key     The key to rename
		 * @param  string $new_key The new name of $key
		 * @return boolean         TRUE on success, FALSE otherwise
		 */
		public static function rename(&$object, $key, $new_key) {
			$qarray = \uri\generate::query_array($object);
			if (isset($qarray[$key])) {
				$qarray[$new_key] = $qarray[$key];
				unset($qarray[$key]);
				\uri\actions::modify($object, 'replace', 'QUERY', self::build_query($qarray));
				return TRUE;
			}
			return FALSE;
		}
	}
	
	/**
	 * The Chaining Class
	 * 
	 * This class is like \uri\main except that only actionable methods can be
	 * called. This mean they must either modify $object or print something. It
	 * also means that the normal return of all methods is replaced with this
	 * class in its' current instance.
	 */
	class chain {
		
		/*** Variables ***/
		private $class;
		private $object;
		public  $error_count;
		
		/*** Magic Methods ***/
		
		/**
		 * Simple method to init a chainable object
		 * 
		 * @param object $class The current instance of \uri\main
		 */
		public function __construct(&$class) {
			$this->class       = &$class;
			$this->object      = &$class->object;
			$this->error_count = 0;
			return $this;
		}
		
		/*** Methods ***/
		
		/**
		 * Chainable alias to \uri\main::replace() within the current instance
		 * 
		 * @return object This instance
		 */
		public function p_str($prepend = '', $append = '') {
			echo $prepend.\uri\generate::string($this->object).$append;
			return $this;
		}
		
		/**
		 * Chainable alias to \uri\main::replace() within the current instance
		 * 
		 * @param  string $section The section to replace
		 * @param  string $str     The string to replace the section with
		 * @return object          This instance
		 */
		public function replace($section, $str) {
			if (\uri\actions::modify($this->object, 'replace', $section, $str) === FALSE) {
				$this->error_count++;
			}
			return $this;
		}
		
		/**
		 * Chainable alias to \uri\main::prepend() within the current instance
		 * 
		 * @param  string $section The section to prepend
		 * @param  string $str     The string to prepend the section with
		 * @return object          This instance
		 */
		public function prepend($section, $str) {
			if (\uri\actions::modify($this->object, 'prepend', $section, $str) === FALSE) {
				$this->error_count++;
			}
			return $this;
		}
		
		/**
		 * Chainable alias to \uri\main::append() within the current instance
		 * 
		 * @param  string $section The section to append
		 * @param  string $str     The string to append the section with
		 * @return object          This instance
		 */
		public function append($section, $str) {
			if (\uri\actions::modify($this->object, 'append', $section, $str) === FALSE) {
				$this->error_count++;
			}
			return $this;
		}
		
		/**
		 * Chainable alias to \uri\main::query_add() within the current instance
		 * 
		 * @param  string $key   The key to add
		 * @param  mixed  $value The value of $key
		 * @return object        This instance
		 */
		public function query_add($key, $value) {
			\uri\query::add($this->object, $key, $value);
			return $this;
		}
		
		/**
		 * Chainable alias to \uri\main::query_replace() within the current instance
		 * 
		 * @param  string $key   The key to replace
		 * @param  mixed  $value The value of $key
		 * @return object        This instance
		 */
		public function query_replace($key, $value) {
			\uri\query::replace($this->object, $key, $value);
			return $this;
		}
		
		/**
		 * Chainable alias to \uri\main::query_remove() within the current instance
		 * 
		 * @param  string $key The key to remove
		 * @return object      This instance
		 */
		public function query_remove($key) {
			\uri\query::remove($this->object, $key);
			return $this;
		}
		
		/**
		 * Chainable alias to \uri\main::query_rename() within the current instance
		 * 
		 * @param  string $key     The key to rename
		 * @param  string $new_key The new name of $key
		 * @return object          This instance
		 */
		public function query_rename($key, $new_key) {
			if (\uri\query::rename($this->object, $key, $new_key) === FALSE) {
				$this->error_count++;
			}
			return $this;
		}
		
		/**
		 * Chainable alias to \uri\main::reset() within the current instance
		 * 
		 * @return object This instance
		 */
		public function reset() {
			$this->class->__construct($this->class->input);
			return $this;
		}
		
		/**
		 * Provides a simple error handle for invalid chainable methods
		 * 
		 * @param  array $trace Debug Backtrace
		 * @return void
		 */
		private function _err($trace) {
			$this->error_count++;
			trigger_error(
				sprintf(
					'The method <code>%1$s()</code> cannot be chained in <b>%2$s</b> on line <b>%3$s</b>. Error triggered',
					$trace[0]['function'],
					$trace[0]['file'],
					$trace[0]['line']
				),
				E_USER_NOTICE
			);
		}
		
		/*** Invalid Chaining Methods ***/
		
		/**
		 * Invalid Chaining Method
		 */
		public function str() {
			$this->_err(debug_backtrace());
			return $this;
		}
		
		/**
		 * Invalid Chaining Method
		 */
		public function to_string() {
			$this->_err(debug_backtrace());
			return $this;
		}
		
		/**
		 * Invalid Chaining Method
		 */
		public function arr() {
			$this->_err(debug_backtrace());
			return $this;
		}
		
		/**
		 * Invalid Chaining Method
		 */
		public function to_array() {
			$this->_err(debug_backtrace());
			return $this;
		}
		
		/**
		 * Invalid Chaining Method
		 */
		public function path_info() {
			$this->_err(debug_backtrace());
			return $this;
		}
		
		/**
		 * Invalid Chaining Method
		 */
		public function query_array() {
			$this->_err(debug_backtrace());
			return $this;
		}
		
		/**
		 * Invalid Chaining Method
		 */
		public function query_exists() {
			$this->_err(debug_backtrace());
			return $this;
		}
		
		/**
		 * Invalid Chaining Method
		 */
		public function query_get() {
			$this->_err(debug_backtrace());
			return $this;
		}
	}
}