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
	 * This class acts as an extension of a URI string.
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
				// parse error
			}
		}
		
		public function __toString() {
			return \uri\generate::string($this->object);
		}
		
		public function __invoke() {
			return \uri\generate::string($this->object);
		}
		
		public function __get($name) {
			if (isset($this->object->$name)) {
				\uri\generate::scheme($this->object);
				\uri\generate::authority($this->object);
				return $this->object->$name;
			} else {
				$trace = debug_backtrace();
				trigger_error(
					sprintf(
						'Undefined property via __get(): \'%1$s\' in %2$s on line %3$s',
						$name,
						$trace[0]['file'],
						$trace[0]['line']
					),
					E_USER_NOTICE
				);
				return NULL;
			}
		}
		
		public function __set($name, $value) {
			if (\uri\modify::modify($this->object, 'replace', $name, $value)) {
				return $value;
			} else {
				$trace = debug_backtrace();
				trigger_error(
					sprintf(
						'Forbidden property via __set(): \'%1$s\' in %2$s on line %3$s',
						$name,
						$trace[0]['file'],
						$trace[0]['line']
					),
					E_USER_NOTICE
				);
				return NULL;
			}
		}
		
		public function __isset($name) {
			\uri\generate::scheme($this->object);
			\uri\generate::authority($this->object);
			return isset($this->object->$name);
		}
		
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
						'Undifined property via __unset(): \'%1$s\' in %2$s on line %3$s',
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
		
		public function str() {
			return \uri\generate::string($this->object);
		}
		
		public function to_string() {
			return \uri\generate::string($this->object);
		}
		
		public function p_str() {
			echo \uri\generate::string($this->object);
		}
		
		public function arr() {
			return (array) $this->object;
		}
		
		public function to_array() {
			return (array) $this->object;
		}
		
		public function path_info() {
			return \uri\generate::path_info($this->object);
		}
		
		public function query_arr() {
			return \uri\generate::query_array($this->object);
		}
		
		public function replace($section, $str) {
			return \uri\modify::modify($this->object, 'replace', $section, $str);
		}
		
		public function prepend($section, $str) {
			return \uri\modify::modify($this->object, 'prepend', $section, $str);
		}
		
		public function append($section, $str) {
			return \uri\modify::modify($this->object, 'append', $section, $str);
		}
		
		public function reset() {
			$this->__construct($this->input);
		}
	}
	
	
	
	/**
	 * 
	 */
	class parser {
		
		/*** Constants ***/
		
		const REGEX = '/^(([a-z]+)?(\:\/\/|\:|\/\/))?(?:([a-z0-9$_\.\+!\*\'\(\),;&=\-]+)(?:\:([a-z0-9$_\.\+!\*\'\(\),;&=\-]*))?@)?((?:\d{3}.\d{3}.\d{3}.\d{3})|(?:[a-z0-9\-_]+(?:\.[a-z0-9\-_]+)*))(?:\:([0-9]+))?((?:\:|\/)[a-z0-9\-_\/\.]+)?(?:\?([a-z0-9$_\.\+!\*\'\(\),;:@&=\-%]*))?(?:#([a-z0-9\-_]*))?/i';
		
		
		
		/*** Methods ***/
		
		public static function parse($uri) {
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
		
		private static function regex_parse($uri) {
			settype($uri, 'string');
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
			return $parsed[0] + array('','','','','','','','','','','');
		}
	}
	
	
	
	/**
	 * 
	 */
	class modify {
		
		/*** Methods ***/
		
		public static function modify(&$object, $action, $section, $str) {
			settype($section, 'string');
			$section = strtolower($section);
			
			if (is_callable(array('\\uri\\modify', $section))) {
				return call_user_func_array(array('\\uri\\modify', $section), array($object, $action, $str));
			} else {
				return FALSE;
			}
		}
		
		public static function replace(&$object, $section, $str) {
			$object->$section = $str;
		}
		
		public static function prepend(&$object, $section, $str) {
			$object->$section = $str.$object->$section;
		}
		
		public static function append(&$object, $section, $str) {
			$object->$section = $object->$section.$str;
		}
		
		public static function scheme_name(&$object, $action, $str) {
			$org = $object->scheme_name;
			call_user_func_array(array('\\uri\\modify', $action), array($object, 'scheme_name', $str));
			if (!preg_match('/\A[a-z]{1,10}\Z/', $object->scheme_name)) {
				$object->scheme_name = $org;
				return FALSE;
			} elseif (empty($object->scheme_symbols)) {
				$object->scheme_symbols = '://';
			}
			
			return \uri\generate::string($object);
		}
		
		public static function scheme_symbols(&$object, $action, $str) {
			$org = $object->scheme_symbols;
			call_user_func_array(array('\\uri\\modify', $action), array($object, 'scheme_symbols', $str));
			if (!preg_match('/\A(:)?([\/]{2,3})?\Z/', $object->scheme_symbols)) {
				$object->scheme_symbols = $org;
				return FALSE;
			}
			
			return \uri\generate::string($object);
		}
		
		public static function scheme(&$object, $action, $str) {
			$org = array($object->scheme, $object->scheme_name, $object->scheme_symbols);
			call_user_func_array(array('\\uri\\modify', $action), array($object, 'scheme', $str));
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
		
		public static function protocol(&$object, $action, $str) {
			self::scheme($object, $action, $str);
		}
		
		public static function user(&$object, $action, $str) {
			$str = rawurlencode($str);
			
			call_user_func_array(array('\\uri\\modify', $action), array($object, 'user', $str));
			return \uri\generate::string($object);
		}
		
		public static function username(&$object, $action, $str) {
			self::user($object, $action, $str);
		}
		
		public static function pass(&$object, $action, $str) {
			$str = rawurlencode($str);
			
			call_user_func_array(array('\\uri\\modify', $action), array($object, 'pass', $str));
			return \uri\generate::string($object);
		}
		
		public static function password(&$object, $action, $str) {
			self::pass($object, $action, $str);
		}
		
		public static function host(&$object, $action, $str) {
			$org = $object->host;
			call_user_func_array(array('\\uri\\modify', $action), array($object, 'host', $str));
			if (
				(
					!preg_match('/\A(([a-z0-9_]([a-z0-9\-_]+)?)\.)+[a-z0-9]([a-z0-9\-]+)?\Z/i', $object->host) // fqdn
					&&
					!preg_match('/\A([0-9]\.){3}[0-9]\Z/i', $object->host) // ip
				)
				||
				strlen($object->host) > 255
			) {
				$object->host = $org;
				return FALSE;
			}
			
			return \uri\generate::string($object);
		}
		
		public static function domain(&$object, $action, $str) {
			self::host($object, $action, $str);
		}
		
		public static function fqdn(&$object, $action, $str) {
			self::host($object, $action, $str);
		}
		
		public static function port(&$object, $action, $str) {
			$org = $object->port;
			if ($str[0] == ':') {
				$str = substr($str, 1);
			}
			call_user_func_array(array('\\uri\\modify', $action), array($object, 'port', $str));
			if (!preg_match('/\A[0-9]{0,5}\Z/', $object->port)) {
				$object->port = $org;
				return FALSE;
			}
			
			return \uri\generate::string($object);
		}
		
		public static function path(&$object, $action, $str) {
			$str = str_replace(array('//','\\'), '/', $str);
			$path_arr = explode('/', $str);
			$safe_arr = array();
			foreach ($path_arr as $path_part) {
				$safe_arr[] = rawurlencode($path_part);
			}
			$str = implode('/', $safe_arr);
			
			call_user_func_array(array('\\uri\\modify', $action), array($object, 'path', $str));
			return \uri\generate::string($object);
		}
		
		public static function query(&$object, $action, $str) {
			if (is_array($str)) {
				$str = http_build_query($str, '', '&', PHP_QUERY_RFC3986);
			} elseif ($str[0] == '?') {
				$str = substr($str, 1);
			}
			
			call_user_func_array(array('\\uri\\modify', $action), array($object, 'query', $str));
			return \uri\generate::string($object);
		}
		
		public static function fragment(&$object, $action, $str) {
			if ($str[0] == '#') {
				unset($str[0]);
			}
			$str = urlencode($str);
			
			call_user_func_array(array('\\uri\\modify', $action), array($object, 'fragment', $str));
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