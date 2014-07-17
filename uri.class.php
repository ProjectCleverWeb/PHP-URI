<?php
/**
 * PHP library for working with URI's. Requires
 * PHP 5.3.7 or later. Replaces and extends PHP's
 * parse_url()
 * 
 * Originally based on P Guardiario's work.
 * 
 * @author    Nicholas Jordon
 * @copyright 2014 Nicholas Jordon - All Rights Reserved
 * @license   http://opensource.org/licenses/MIT
 * @version   1.0.0 RC1
 * @see       http://en.wikipedia.org/wiki/URI_scheme
 */
 
/**
 * PHP URI
 * 
 * Parses the input as a URI string. On failure $error
 * is set to 1 and $error_msg is populated.
 */
class uri {
	
	/*** Constants ***/
	const PARSER_REGEX = '/^(([a-z]+)?(\:\/\/|\:|\/\/))?(?:([a-z0-9$_\.\+!\*\'\(\),;&=\-]+)(?:\:([a-z0-9$_\.\+!\*\'\(\),;&=\-]*))?@)?((?:\d{3}.\d{3}.\d{3}.\d{3})|(?:[a-z0-9\-_]+(?:\.[a-z0-9\-_]+)*))(?:\:([0-9]+))?((?:\:|\/)[a-z0-9\-_\/\.]+)?(?:\?([a-z0-9$_\.\+!\*\'\(\),;:@&=\-%]*))?(?:#([a-z0-9\-_]*))?/i';
	
	/*** Variables ***/
	public $authority;
	public $domain;
	public $error;
	public $error_msg;
	public $fqdn;
	public $fragment;
	public $host;
	public $input;
	public $protocol;
	public $pass;
	public $password;
	public $path;
	public $port;
	public $query;
	public $scheme;
	public $scheme_name;
	public $scheme_symbols;
	public $user;
	public $username;
	
	
	/*** Magic Methods ***/
	
	
	/**
	 * Parses the input as a URI and populates the
	 * variables. Fails if input is not a string or
	 * if the string cannot be parsed as a URI.
	 * 
	 * @param string $input The URI to parse.
	 */
	public function __construct($input) {
		$t = $this;
		$t->input    = $input;
		$t->error    = FALSE;
		$t->protocol = &$this->scheme;
		$t->username = &$this->user;
		$t->password = &$this->pass;
		$t->domain   = &$this->host;
		$t->fqdn     = &$this->host;
		if (is_string($input) == FALSE || $this->parse($input) == FALSE) {
			$t->error = TRUE;
			$t->error_msg = 'Input was not a string!';
			
			$t->scheme         = FALSE;
			$t->scheme_name    = FALSE;
			$t->scheme_symbols = FALSE;
			$t->user           = FALSE;
			$t->pass           = FALSE;
			$t->host           = FALSE;
			$t->port           = FALSE;
			$t->authority      = FALSE;
			$t->path           = FALSE;
			$t->query          = FALSE;
			$t->fragment       = FALSE;
		}
	}
	
	/**
	 * If this class gets typecast as a sting it should
	 * return the current URI as a string.
	 * 
	 * @return false|string The current URI.
	 */
	public function __toString() {
		return $this->str();
	}
	
	/**
	 * If this object is called as a function, it will
	 * re-initialize around the new input
	 * 
	 * @param  string $input The new URI to parse
	 * @return void
	 */
	public function __invoke($input) {
		$this->__construct($input);
	}
	
	
	
	/*** Methods ***/
	
	
	
	/**
	 * Parses the supplied string as a URI and sets the
	 * variables in the class.
	 * 
	 * @param  string $uri The string to be parsed.
	 * @return boolean
	 */
	private function parse($uri) {
		$t = $this;
		$parsed = $t->_parse($uri);
		if (empty($parsed)) {
			$t->error = TRUE;
			$t->error_msg = 'Could not parse the input as a URI';
			return FALSE;
		}
		$defaults = array(
			'scheme'         => '',
			'scheme_name'    => '',
			'scheme_symbols' => '',
			'user'           => '',
			'pass'           => '',
			'host'           => '',
			'port'           => '',
			'authority'      => '',
			'path'           => '',
			'query'          => '',
			'fragment'       => ''
		);
		
		$values = $parsed + $defaults;
		
		$t->scheme         = $values['scheme'];
		$t->scheme_name    = $values['scheme_name'];
		$t->scheme_symbols = $values['scheme_symbols'];
		$t->user           = $values['user'];
		$t->pass           = $values['pass'];
		$t->host           = $values['host'];
		$t->port           = $values['port'];
		$t->path           = $values['path'];
		$t->query          = $values['query'];
		$t->fragment       = $values['fragment'];
		
		$t->gen_authority();
		return TRUE;
	}
	
	/**
	 * Helper function for parse(). Uses Regex instead of
	 * PHP's parse_url(). This makes the parsing much
	 * more accurate.
	 * 
	 * The regex used isn't perfect, but has a VERY LOW
	 * chance at incorrectly parsing a valid URI, and
	 * will correctly parse a wider range of URI's than
	 * parse_url(). This is because of how the URI
	 * specification allows special characters.
	 * 
	 * @param  string $uri                The string to be parsed
	 * @return false|array<string,string> The correctly parsed string as an array
	 */
	private function _parse($uri) {
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
		preg_match_all(self::PARSER_REGEX, $uri, $parsed, PREG_SET_ORDER);
		
		if (!isset($parsed[0][6])) {
			return FALSE;
		}
		
		// No empty slots please
		$parsed = (
			$parsed[0] +
			array('','','','','','','','','','','')
		);
		
		return array(
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
	 * Standard function to re-genrate $authority
	 * 
	 * @return boolean
	 */
	public function gen_authority() {
		if ($this->error) {
			return FALSE;
		}
		$str_arr = array($this->user);
		
		if (empty($this->user) == FALSE && empty($this->pass)) {
			$str_arr[] = '@';
		} elseif (!empty($this->user)) {
			$str_arr[] = ':'.$this->pass.'@';
		}
		$str_arr[] = $this->host;
		if (!empty($this->port)) {
			$str_arr[] = ':'.$this->port;
		}
		$this->authority = implode('', $str_arr);
		return TRUE;
	}
	
	/**
	 * Standard function to re-genrate $scheme
	 * 
	 * @return boolean
	 */
	public function gen_scheme() {
		if ($this->error) {
			return FALSE;
		}
		$this->scheme = $this->scheme_name.$this->scheme_symbols;
		return TRUE;
	}
	
	/**
	 * Returns the current URI as an associative
	 * array similar to parse_url(). However it always
	 * sets each key as an empty string by default.
	 * 
	 * @return false|array The URI as an array.
	 */
	public function arr() {
		if ($this->error) {
			return FALSE;
		}
		$this->gen_scheme();
		$this->gen_authority();
		$arr = array(
			'scheme'         => $this->scheme,
			'scheme_name'    => $this->scheme_name,
			'scheme_symbols' => $this->scheme_symbols,
			'user'           => $this->user,
			'pass'           => $this->pass,
			'host'           => $this->host,
			'port'           => $this->port,
			'authority'      => $this->authority,
			'path'           => $this->path,
			'query'          => $this->query,
			'fragment'       => $this->fragment
		);
		
		// create aliases correctly
		$arr['protocol'] = &$arr['scheme'];
		$arr['username'] = &$arr['user'];
		$arr['password'] = &$arr['pass'];
		$arr['domain']   = &$arr['host'];
		$arr['fqdn']     = &$arr['host'];
		
		ksort($arr);
		
		return $arr;
	}
	
	/**
	 * Alias of arr()
	 * @return false|array The URI as an array.
	 */
	public function to_array() {
		return $this->arr();
	}
	
	/**
	 * Returns the current URI as a string.
	 * 
	 * @return false|string The current URI.
	 */
	public function str() {
		if ($this->error) {
			return FALSE;
		}
		$this->gen_scheme();
		$this->gen_authority();
		$str_arr = array($this->scheme, $this->authority, $this->path);
		if (!empty($this->query)) {
			$str_arr[] = '?'.$this->query;
		}
		if (!empty($this->fragment)) {
			$str_arr[] = '#'.$this->fragment;
		}
		return implode('', $str_arr);
	}
	
	/**
	 * alias of str()
	 * @return false|string The current URI.
	 */
	public function to_string() {
		return $this->str();
	}
	
	/**
	 * Prints the current URI.
	 * 
	 * @return boolean
	 */
	public function p_str() {
		if ($this->error) {
			return FALSE;
		}
		echo $this->str();
		return TRUE;
	}
	
	/**
	 * Returns an associative array of various
	 * information about the $path.
	 * 
	 * Array Keys:
	 *   dirname, basename, extension, filename, array
	 * 
	 * @return false|array The $path's information
	 */
	public function path_info() {
		if ($this->error) {
			return FALSE;
		}
		
		$defaults = array(
			'dirname' => '',
			'basename' => '',
			'extension' => '',
			'filename' => '',
			'array' => array()
		);
		
		$info = pathinfo($this->path) + $defaults;
		$info['array'] = array_values(array_filter(explode('/',$this->path)));
		ksort($info);
		
		return $info;
	}
	
	/**
	 * Returns the query string parsed into an array
	 * 
	 * @return false|null|array $query as an array
	 */
	public function query_arr() {
		if ($this->error) {
			return FALSE;
		}
		parse_str($this->query, $return);
		return $return;
	}
	
	/**
	 * Appends $str to $section. By default it tries to
	 * autocorrect some errors.
	 * 
	 * @param  string  $section The section to append to.
	 * @param  string  $str     The string to append.
	 * @return false|string     The resulting URI.
	 */
	public function append($section, $str) {
		if ($this->error) {
			return FALSE;
		}
		$section = strtolower($section);
		if (!isset($this->$section)) {
			return FALSE;
		}
		$test = $this->$section.$str;
		$modifier = $this->_modifier($section, $test);
		if ($modifier != FALSE) {
			$this->$section = $modifier;
		} else {
			return FALSE;
		}
		
		return $this->str();
	}
	
	/**
	 * Prepends $str to $section. By default it tries to
	 * autocorrect some errors.
	 * 
	 * @param  string  $section The section to prepend to.
	 * @param  string  $str     The string to prepend.
	 * @return false|string     The resulting URI.
	 */
	public function prepend($section, $str) {
		if ($this->error) {
			return FALSE;
		}
		$section = strtolower($section);
		if (!isset($this->$section)) {
			return FALSE;
		}
		$test = $str.$this->$section;
		$modifier = $this->_modifier($section, $test);
		if ($modifier != FALSE) {
			$this->$section = $modifier;
		} else {
			return FALSE;
		}
		
		return $this->str();
	}
	
	/**
	 * Replaces $section with $str. By default it tries
	 * to autocorrect some errors.
	 * 
	 * @param  string  $section The section to replace.
	 * @param  string  $str     The string to replace $section with.
	 * @return false|string     The resulting URI.
	 */
	public function replace($section, $str) {
		if ($this->error) {
			return FALSE;
		}
		$section = strtolower($section);
		if (!isset($this->$section)) {
			return FALSE;
		}
		$modifier = $this->_modifier($section, $str);
		if ($modifier != FALSE) {
			$this->$section = $modifier;
		} else {
			return FALSE;
		}
		
		return $this->str();
	}
	
	/**
	 * Attempts to correct any errors in $str based on
	 * what $type is.
	 * 
	 * @param  string $type The type error correction to apply.
	 * @param  string $str  The string to attempt to correct.
	 * @return mixed        The resulting string, or FALSE on failure.
	 */
	private function _modifier($type, $str) {
		$type = strtoupper((string) $type);
		if ($type != 'QUERY') {
			$str = trim((string) $str);
		}
		$err = 0;
		switch ($type) {
			case 'SCHEME_NAME':
				if (!preg_match('/\A[a-z]{1,10}\Z/', $str)) {
					$err++;
				} elseif (empty($this->scheme_symbols)) {
					$this->scheme_symbols = '://';
				}
				break;
			
			case 'SCHEME_SYMBOLS':
				break;
			
			case 'SCHEME':
				if (empty($str)) {
					$this->scheme = $this->scheme_name = $this->scheme_symbols = '';
				} else {
					preg_match('/\A([a-z]{1,10})?((?:\:)?(?:\/{2,3})?)\Z/i', $str, $matches);
					if (empty($matches[1]) && empty($matches[2])) {
						$err++;
					} else {
						$matches              = $matches + array('', '', '');
						$this->scheme         = $matches[0];
						$this->scheme_name    = $matches[1];
						$this->scheme_symbols = $matches[2];
					}
				}
				
				break;
			
			case 'USER':
				$str = rawurlencode($str);
				break;
			
			case 'PASS':
				$str = rawurlencode($str);
				break;
			
			case 'HOST':
				$str = strtolower($str);
				if (
					(
						!preg_match('/\A(([a-z0-9_]([a-z0-9\-_]+)?)\.)+[a-z0-9]([a-z0-9\-]+)?\Z/', $str) // fqdn
						&&
						!preg_match('/\A([0-9]\.){3}[0-9]\Z/', $str) // ip
					)
					||
					strlen($str) > 255
				) {
					$err++;
				}
				break;
			
			case 'PORT':
				if ($str[0] == ':') {
					$str = substr($str, 1);
				}
				if (!preg_match('/\A[0-9]{0,5}\Z/', $str)) {
					$err++;
				}
				break;
			
			case 'PATH':
				$str = str_replace(array('//', '\\'), '/', $str); // common mistakes
				$path_arr = explode('/', $str);
				$safe_arr = array();
				foreach ($path_arr as $path_part) {
					$safe_arr[] = rawurlencode($path_part);
				}
				$str = implode('/', $safe_arr);
				break;
			
			case 'QUERY':
				if (is_array($str)) {
					$str = http_build_query($str, '', '&', PHP_QUERY_RFC3986);
				}
				if ($str[0] == '?') {
					$str = substr($str, 1);
				}
				$frag_loc = strpos($str, '#');
				if ($frag_loc) {
					$str = substr($str, 0, ($frag_loc - 1));
				} elseif ($str[0] == '#') {
					$str = '';
				}
				break;
			
			case 'FRAGMENT':
				if ($str[0] == '#') {
					unset($str[0]);
				}
				$str = urlencode($str);
				break;
			
			
			
			default:
				return FALSE;
		}
		
		if ($err) {
			return FALSE;
		}
		
		return $str;
	}
	
	/**
	 * Re-initializes the class with the original URI
	 * 
	 * @return void
	 */
	public function reset() {
		$this->__construct($this->input);
	}
}
