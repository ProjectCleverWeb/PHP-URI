<?php

namespace uri;

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
