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
 * @package   projectcleverweb\uri\generate
 * @license   http://opensource.org/licenses/MIT
 * @see       http://en.wikipedia.org/wiki/URI_scheme
 */

namespace projectcleverweb\uri;

/**
 * The Generator Class
 * 
 * This class makes sure everything stays in sync and is produced correctly.
 * Unlike the the modify class, this class only changes information to keep
 * things syncronized. It's primary purpose is to use the information in
 * $object to create some type of returnable value.
 */
class generate {
	
	/*** Methods ***/
	
	/**
	 * Generate the scheme. This method exists to make changing how the scheme
	 * is generated easier; and will likely help prevent redundant code in the
	 * future
	 * 
	 * @param  data_object $object The primary data object
	 * @return void
	 */
	public static function scheme(data_object &$object) {
		$object->scheme = $object->scheme_name.$object->scheme_symbols;
	}
	
	/**
	 * Regenerates the Authority string
	 * 
	 * @param  data_object $object The primary data object
	 * @return void
	 */
	public static function authority(data_object &$object) {
		$str_arr = array($object->user);
		if (!empty($object->pass)) { // pass can only be parsed if user exists as well
			$str_arr[] = ':'.$object->pass.'@';
		} elseif (!empty($object->user)) {
			$str_arr[] = '@';
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
	 * @param  main        $main   The main class
	 * @param  data_object $object The primary data object
	 * @return string              The current URI string
	 */
	public static function string(main &$main, data_object &$object) {
		self::scheme($object);
		self::authority($object);
		$str_arr = array($object->scheme, $object->authority, $object->path);
		$query   = $main->query->str();
		if (!empty($query)) {
			$str_arr[] = '?'.$query;
		}
		if (!empty($object->fragment)) {
			$str_arr[] = '#'.$object->fragment;
		}
		return implode('', $str_arr);
	}
	
	/**
	 * Generate a the full URI as an array, from the current object
	 * 
	 * @param  main        $main   The main class
	 * @param  data_object $object The primary data object
	 * @return array               The current URI as an array
	 */
	public static function to_array(main &$main, data_object &$object) {
		$keys            = array('authority', 'fragment', 'host', 'pass', 'path', 'port', 'query', 'scheme', 'scheme_name', 'scheme_symbols', 'user');
		$values          = array($object->authority, $object->fragment, $object->host, $object->pass, $object->path, $object->port, $main->query->str(), $object->scheme, $object->scheme_name, $object->scheme_symbols, $object->user);
		$arr             = array_combine($keys, $values);
		$arr['domain']   = &$arr['host'];
		$arr['fqdn']     = &$arr['host'];
		$arr['password'] = &$arr['pass'];
		$arr['protocol'] = &$arr['scheme'];
		$arr['username'] = &$arr['user'];
		
		ksort($arr);
		return $arr;
	}
	
	/**
	 * Returns various information about the current $path as an array
	 * 
	 * @param  data_object $object The primary data object
	 * @return array               Associative array of information about the current $path
	 */
	public static function path_info(data_object &$object) {
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
	 * Encodes a PHP array into a query string
	 * 
	 * This is the only method that requires PHP 5.4 or later. The rest are 5.3
	 * compatible.
	 * 
	 * @see    http://php.net/manual/en/function.http-build-query.php
	 * @param  array  $data_array The array to make into a query string
	 * @param  string $prefix     The numeric prefix according to the PHP docs
	 * @param  string $separator  The separator you want to use in you query string (default is '&')
	 * @param  int    $enc        The encoding to use (default is RFC3986)
	 * @return string             The resulting query string
	 */
	public static function query_str($data_array, $prefix = '', $separator = '&', $enc = PHP_QUERY_RFC3986) {
		return http_build_query($data_array, $prefix, $separator, $enc);
	}
}
