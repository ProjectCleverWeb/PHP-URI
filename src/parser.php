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
 * @package   projectcleverweb\uri\parser
 * @license   http://opensource.org/licenses/MIT
 * @see       http://en.wikipedia.org/wiki/URI_scheme
 */

namespace projectcleverweb\uri;

/**
 * The Parser Class
 * 
 * This class controls how the initial input is parsed. This class is
 * designed to be easily upgraded to use different types of parsing. should
 * it be desired.
 */
class parser {
	/*** Constants ***/
	
	// This regex is broken down to be readable in regex_parse_uri()
	const URI_REGEX = '/^(([a-z]+)?(\:\/\/|\:|\/\/))?(?:([a-z0-9$_\.\+!\*\'\(\),;&=\-]+)(?:\:([a-z0-9$_\.\+!\*\'\(\),;&=\-]*))?@)?((?:\d{3}.\d{3}.\d{3}.\d{3})|(?:[a-z0-9\-_]+(?:\.[a-z0-9\-_]+)*))(?:\:([0-9]+))?((?:\:|\/)[a-z0-9\-_\/\.]+)?(?:\?([a-z0-9$_\.\+!\*\'\(\),;:@&=\-%]*))?(?:#([a-z0-9\-_]*))?/i';
	
	/*** Methods ***/
	
	/**
	 * Wrapper function for parsing a string into a URI object
	 * 
	 * @param  string $uri  The input to be parsed as a URI
	 * @return object       If the input can be correctly parsed, then it returns an object with at least the 'host' populated
	 */
	public static function parse_uri($uri) {
		$parsed = self::regex_parse_uri($uri);
		
		// Could not be parsed correctly
		if (empty($parsed)) {
			$parsed = array_fill(0, 10, '');
		}
		
		$keys = array('scheme', 'scheme_name', 'scheme_symbols', 'user', 'pass', 'host', 'port', 'path', 'query', 'fragment');
		return (object) array_combine($keys, $parsed);
	}
	
	/**
	 * Parses a URI string into a usable array.
	 * 
	 * The RegEx Broken Down:
	 *   $regex = (
	 *     '/'.
	 *     '^(([a-z]+)?(\:\/\/|\:|\/\/))?'.              // Scheme, Scheme Name, & Scheme Symbols
	 *     '(?:'.                                        // Auth Start
	 *       '([a-z0-9$_\.\+!\*\'\(\),;&=\-]+)'.         // Username
	 *       '(?:\:([a-z0-9$_\.\+!\*\'\(\),;&=\-]*))?'.  // Password
	 *     '@)?'.                                        // Auth End
	 *     '('.                                          // Host Start
	 *       '(?:\d{3}.\d{3}.\d{3}.\d{3})'.              // IP Address
	 *       '|'.                                        // -OR-
	 *       '(?:[a-z0-9\-_]+(?:\.[a-z0-9\-_]+)*)'.      // Domain Name
	 *     ')'.                                          // Host End
	 *     '(?:\:([0-9]+))?'.                            // Port
	 *     '((?:\:|\/)[a-z0-9\-_\/\.]+)?'.               // Path
	 *     '(?:\?([a-z0-9$_\.\+!\*\'\(\),;:@&=\-%]*))?'. // Query
	 *     '(?:#([a-z0-9\-_]*))?'.                       // Fragment
	 *     '/i'
	 *   );
	 * 
	 * @param  string $uri The URI to be parsed
	 * @return array|false Returns an array if the sting could be correctly parsed, FALSE otherwise
	 */
	private static function regex_parse_uri($uri) {
		preg_match_all(self::URI_REGEX, $uri, $parsed, PREG_SET_ORDER);
		
		// Host is required
		if (!isset($parsed[0][5])) {
			return FALSE;
		}
		
		// Return what was parsed, but make sure that each offset is set regardless
		array_shift($parsed[0]); // we don't need the whole string
		return $parsed[0] + array_fill(0, 10, '');
	}
	
	/**
	 * Parses $query_str according to PHPs parse_str()
	 * 
	 * @param  string $query_str The string to parse
	 * @return array             The resulting array
	 */
	public static function parse_query($query_str = '') {
		$return = array();
		if (!empty($query_str)) {
			parse_str($query_str, $return);
		}
		return $return;
	}
}
