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
 * @package   projectcleverweb\uri\generate
 * @license   http://opensource.org/licenses/MIT
 * @see       http://en.wikipedia.org/wiki/URI_scheme
 */

namespace projectcleverweb\uri;

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
	public static function string(main &$main, &$object) {
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
	 * Generate a the full URI as a string, from the current object
	 * 
	 * @param  object $object The object to use
	 * @return array          The current URI as an array
	 */
	public static function to_array(main &$main, &$object) {
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
	
	/**
	 * Encodes a PHP array into a query string
	 * 
	 * This is the only method that requires PHP 5.4 or later. The rest are 5.3
	 * compatible.
	 * 
	 * @see    http://php.net/manual/en/function.http-build-query.php
	 * @param  array  $data_array The array to make into a query string
	 * @param  string $prefix     The numeric prefix according to the PHP docs
	 * @param  string $seperator  The seperator you want to use in you query string (default is '&')
	 * @param  int    $spec       The encoding to use (default is RFC3986)
	 * @return string             The resulting query string
	 */
	public static function query_str($data_array, $prefix = '', $seperator = '&', $spec = PHP_QUERY_RFC3986) {
		return http_build_query($data_array, $prefix, $seperator, $spec);
	}
}
