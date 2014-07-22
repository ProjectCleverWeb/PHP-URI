<?php
/**
 * PHP URI Library
 * 
 * A PHP library for working with URI's. Requires PHP 5.3.7 or later. Replaces
 * and extends PHP's parse_url()
 * 
 * Originally inspired by P Guardiario's work.
 * 
 * @author    Nicholas Jordon
 * @link      https://github.com/ProjectCleverWeb/PHP-URI
 * @copyright 2014 Nicholas Jordon - All Rights Reserved
 * @version   1.0.0 RC2
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
	 * this class as if it were a string will result in the current URL string
	 * being used throughout PHP.
	 */
	abstract class main {
		/*** Variables ***/
		
		public $error;
		public $input;
		private $object;
		
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
			$this->object = \uri\parser::parse($input);
			
			if (is_object($this->object)) {
				\uri\generate::authority($this->object);
				\uri\generate::aliases($this->object);
				
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
		 * Invoked? just return the current URI, nothing fancy.
		 * 
		 * @return string The current URI as a string
		 */
		public function __invoke() {
			return \uri\generate::string($this->object);
		}
		
		/**
		 * Allows access to the different parts of the URI to be synchronized. This
		 * means that what is returned should always be accurate. Throws notice if
		 * the variable cannot be accessed.
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
				$trace = debug_backtrace();
				trigger_error(
					sprintf(
						'Undefined property via %1$s::__get(): \'%2$s\' in %3$s on line %4$s',
						__NAMESPACE__.'\\'.__CLASS__,
						$name,
						$trace[0]['file'],
						$trace[0]['line']
					),
					E_USER_NOTICE
				);
				return NULL;
			}
		}
		
		/**
		 * Allows access to the different parts of the URI to be synchronized. This
		 * means that what is returned should always be accurate. Throws notice if
		 * the variable cannot be accessed.
		 * 
		 * @param  string $name  The requested variable
		 * @param  string $value The new value for the variable
		 * @return string|null   The new value of the variable, or NULL if it can't be accessed
		 */
		public function __set($name, $value) {
			if (\uri\modify::modify($this->object, 'replace', $name, $value)) {
				return $value;
			} else {
				$trace = debug_backtrace();
				trigger_error(
					sprintf(
						'Forbidden property via %1$s::__set(): \'%2$s\' in %3$s on line %4$s',
						__NAMESPACE__.'\\'.__CLASS__,
						$name,
						$trace[0]['file'],
						$trace[0]['line']
					),
					E_USER_NOTICE
				);
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
			return !empty($this->object->$name);
		}
		
		/**
		 * Allows access to the different parts of the URI to be synchronized. This
		 * means that what is returned should always be accurate. Throws notice if
		 * the variable cannot be accessed.
		 * 
		 * @param  string $name The requested variable
		 * @return boolean      Returns TRUE if the varaible was successfully emptied, FALSE otherwise.
		 */
		public function __unset($name) {
			if (isset($this->object->$name)) {
				$this->object->$name = '';
				\uri\generate::scheme($this->object);
				\uri\generate::authority($this->object);
				return TRUE;
			} else {
				$trace = debug_backtrace();
				trigger_error(
					sprintf(
						'Undifined property via %1$s::__unset(): \'%2$s\' in %3$s on line %4$s',
						__NAMESPACE__.'\\'.__CLASS__,
						$name,
						$trace[0]['file'],
						$trace[0]['line']
					),
					E_USER_NOTICE
				);
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
		 * @return void
		 */
		public function p_str() {
			echo \uri\generate::string($this->object);
		}
		
		/**
		 * Returns the current URI as an array
		 * 
		 * @return array The current URI as an array
		 */
		public function arr() {
			return (array) $this->object;
		}
		
		/**
		 * Alias of arr()
		 * 
		 * @return array The current URI as an array
		 */
		public function to_array() {
			return (array) $this->object;
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
		 * @return null|array The query array
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
			return \uri\modify::modify($this->object, 'replace', $section, $str);
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
			return \uri\modify::modify($this->object, 'prepend', $section, $str);
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
			return \uri\modify::modify($this->object, 'append', $section, $str);
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
		 * @return object|false If the input can be correctly parsed, then it returns an object, FALSE otherwise
		 */
		public static function parse($uri) {
			if (!is_string($uri)) {
				return FALSE;
			}
			
			$parsed = self::regex_parse($uri);
			
			// Could not be parsed correctly
			if (empty($parsed)) {
				return FALSE;
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
		 * Acts as universal alias to the rest of the class, ensuring the call is
		 * viable.
		 * 
		 * @param  object $object  The object to modify
		 * @param  string $action  The action to take
		 * @param  string $section The section of the object to modify
		 * @param  string $str     The modfication
		 * @return string|false    Returns the resulting URI on success, FALSE otherwise
		 */
		public static function modify(&$object, $action, $section, $str) {
			settype($section, 'string');
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
		private static function action_callback(&$object, $action, $section, $str) {
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
			self::action_callback($object, $action, __FUNCTION__, $str);
			if (!preg_match('/\A[a-z]{1,10}\Z/', $object->scheme_name)) {
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
			self::action_callback($object, $action, __FUNCTION__, $str);
			if (!preg_match('/\A(:)?([\/]{2,3})?\Z/', $object->scheme_symbols)) {
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
			self::action_callback($object, $action, __FUNCTION__, $str);
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
			
			self::action_callback($object, $action, __FUNCTION__, $str);
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
			
			self::action_callback($object, $action, __FUNCTION__, $str);
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
			self::action_callback($object, $action, __FUNCTION__, $str);
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
			if ($str[0] == ':') {
				$str = substr($str, 1);
			}
			self::action_callback($object, $action, __FUNCTION__, $str);
			if (!preg_match('/\A[0-9]{0,5}\Z/', $object->port)) {
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
			self::action_callback($object, $action, __FUNCTION__, $str);
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
			if (is_array($str)) {
				$str = http_build_query($str, '', '&', PHP_QUERY_RFC3986);
			} elseif ($str[0] == '?') {
				$str = substr($str, 1);
			}
			
			self::action_callback($object, $action, __FUNCTION__, $str);
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
			if ($str[0] == '#') {
				unset($str[0]);
			}
			$str = urlencode($str);
			
			self::action_callback($object, $action, __FUNCTION__, $str);
			return \uri\generate::string($object);
		}
	}
	
	
	
	/**
	 * 
	 */
	class generate {
		
		/*** Methods ***/
		
		public static function aliases(&$object) {
			$object->protocol = &$object->scheme;
			$object->username = &$object->user;
			$object->password = &$object->pass;
			$object->domain   = &$object->host;
			$object->fqdn     = &$object->host;
		}
		
		public static function scheme(&$object) {
			$object->scheme = $object->scheme_name.$object->scheme_symbols;
		}
		
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
		
		public static function path_info(&$object) {
			$defaults = array(
				'dirname' => '',
				'basename' => '',
				'extension' => '',
				'filename' => '',
				'array' => array()
			);
			
			$info = pathinfo($object->path) + $defaults;
			$info['array'] = array_values(array_filter(explode('/', $object->path)));
			ksort($info);
			
			return $info;
		}
		
		public static function query_array(&$object) {
			parse_str($object->query, $return);
			return $return;
		}
		
	}
	
	
	/**
	 * 
	 */
	class query {
		
		/*** Methods ***/
		
		
		
		
	}
	
	
}