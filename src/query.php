<?php

namespace uri;

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
