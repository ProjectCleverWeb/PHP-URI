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
 * @package   projectcleverweb\uri\modify
 * @license   http://opensource.org/licenses/MIT
 * @see       http://en.wikipedia.org/wiki/URI_scheme
 */

namespace projectcleverweb\uri;

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
	public static function scheme_name(main &$main, &$object, $action, $str) {
		$org = $object->scheme_name;
		actions::callback($object, $action, __FUNCTION__, $str);
		if (!(preg_match('/\A[a-z]{1,10}\Z/', $object->scheme_name) || empty($str))) {
			$object->scheme_name = $org;
			return FALSE;
		} elseif (empty($object->scheme_symbols)) {
			$object->scheme_symbols = '://';
		}
		
		return generate::string($main, $object);
	}
	
	/**
	 * Modfies the Scheme Symbols
	 * 
	 * @param  object $object  The object to modify
	 * @param  string $action  The action to take
	 * @param  string $str     The modfication
	 * @return string|false    Returns the resulting URI on success, FALSE otherwise
	 */
	public static function scheme_symbols(main &$main, &$object, $action, $str) {
		$org = $object->scheme_symbols;
		actions::callback($object, $action, __FUNCTION__, $str);
		if (!(preg_match('/\A(:)?([\/]{2,3})?\Z/', $object->scheme_symbols) || empty($str))) {
			$object->scheme_symbols = $org;
			return FALSE;
		}
		
		return generate::string($main, $object);
	}
	
	/**
	 * Modfies the Scheme
	 * 
	 * @param  object $object  The object to modify
	 * @param  string $action  The action to take
	 * @param  string $str     The modfication
	 * @return string|false    Returns the resulting URI on success, FALSE otherwise
	 */
	public static function scheme(main &$main, &$object, $action, $str) {
		$org = array($object->scheme, $object->scheme_name, $object->scheme_symbols);
		actions::callback($object, $action, __FUNCTION__, $str);
		if (empty($object->scheme)) {
			$object->scheme = $object->scheme_name = $object->scheme_symbols = '';
		} elseif (preg_match('/\A([a-z]{1,10})?(\:|:\/\/|\/\/|:\/\/\/)\Z/i', $object->scheme, $matches)) {
			$matches                = $matches + array('', '', '');
			$object->scheme         = $matches[0];
			$object->scheme_name    = $matches[1];
			$object->scheme_symbols = $matches[2];
		} else {
			$object->scheme         = $org[0];
			$object->scheme_name    = $org[1];
			$object->scheme_symbols = $org[2];
			return FALSE;
		}
		return generate::string($main, $object);
	}
	
	/**
	 * Alias of scheme()
	 * 
	 * @param  object $object  The object to modify
	 * @param  string $action  The action to take
	 * @param  string $str     The modfication
	 * @return string|false    Returns the resulting URI on success, FALSE otherwise
	 */
	public static function protocol(main &$main, &$object, $action, $str) {
		return self::scheme($main, $object, $action, $str);
	}
	
	/**
	 * Modfies the Username
	 * 
	 * @param  object $object  The object to modify
	 * @param  string $action  The action to take
	 * @param  string $str     The modfication
	 * @return string          Returns the resulting URI on success, FALSE otherwise
	 */
	public static function user(main &$main, &$object, $action, $str) {
		$str = rawurlencode($str);
		
		actions::callback($object, $action, __FUNCTION__, $str);
		return generate::string($main, $object);
	}
	
	/**
	 * Alias of user()
	 * 
	 * @param  object $object  The object to modify
	 * @param  string $action  The action to take
	 * @param  string $str     The modfication
	 * @return string          Returns the resulting URI on success, FALSE otherwise
	 */
	public static function username(main &$main, &$object, $action, $str) {
		return self::user($main, $object, $action, $str);
	}
	
	/**
	 * Modfies the Password
	 * 
	 * @param  object $object  The object to modify
	 * @param  string $action  The action to take
	 * @param  string $str     The modfication
	 * @return string          Returns the resulting URI on success, FALSE otherwise
	 */
	public static function pass(main &$main, &$object, $action, $str) {
		$str = rawurlencode($str);
		
		actions::callback($object, $action, __FUNCTION__, $str);
		return generate::string($main, $object);
	}
	
	/**
	 * Alias of pass()
	 * 
	 * @param  object $object  The object to modify
	 * @param  string $action  The action to take
	 * @param  string $str     The modfication
	 * @return string          Returns the resulting URI on success, FALSE otherwise
	 */
	public static function password(main &$main, &$object, $action, $str) {
		return self::pass($main, $object, $action, $str);
	}
	
	/**
	 * Modfies the Host
	 * 
	 * @param  object $object  The object to modify
	 * @param  string $action  The action to take
	 * @param  string $str     The modfication
	 * @return string|false    Returns the resulting URI on success, FALSE otherwise
	 */
	public static function host(main &$main, &$object, $action, $str) {
		$org = $object->host;
		actions::callback($object, $action, __FUNCTION__, $str);
		if ((
			!preg_match('/\A(([a-z0-9_]([a-z0-9\-_]+)?)\.)+[a-z0-9]([a-z0-9\-]+)?\Z/i', $object->host) // fqdn
			&&
			!preg_match('/\A([0-9]\.){3}[0-9]\Z/i', $object->host) // ip
		)) {
			$object->host = $org;
			return FALSE;
		}
		
		return generate::string($main, $object);
	}
	
	/**
	 * Alias of host()
	 * 
	 * @param  object $object  The object to modify
	 * @param  string $action  The action to take
	 * @param  string $str     The modfication
	 * @return string|false    Returns the resulting URI on success, FALSE otherwise
	 */
	public static function domain(main &$main, &$object, $action, $str) {
		return self::host($main, $object, $action, $str);
	}
	
	/**
	 * Alias of host()
	 * 
	 * @param  object $object  The object to modify
	 * @param  string $action  The action to take
	 * @param  string $str     The modfication
	 * @return string|false    Returns the resulting URI on success, FALSE otherwise
	 */
	public static function fqdn(main &$main, &$object, $action, $str) {
		return self::host($main, $object, $action, $str);
	}
	
	/**
	 * Modfies the Port
	 * 
	 * @param  object $object  The object to modify
	 * @param  string $action  The action to take
	 * @param  string $str     The modfication
	 * @return string|false    Returns the resulting URI on success, FALSE otherwise
	 */
	public static function port(main &$main, &$object, $action, $str) {
		$org = $object->port;
		if (isset($str[0]) && $str[0] == ':') {
			$str = substr($str, 1);
		}
		actions::callback($object, $action, __FUNCTION__, $str);
		if (!preg_match('/\A[0-9]{0,5}\Z/', $object->port)) {
			$object->port = $org;
			return FALSE;
		}
		
		return generate::string($main, $object);
	}
	
	/**
	 * Modfies the Path
	 * 
	 * @param  object $object  The object to modify
	 * @param  string $action  The action to take
	 * @param  string $str     The modfication
	 * @return string          Returns the resulting URI on success, FALSE otherwise
	 */
	public static function path(main &$main, &$object, $action, $str) {
		actions::callback($object, $action, __FUNCTION__, $str);
		return generate::string($main, $object);
	}
	
	/**
	 * Modfies the Query
	 * 
	 * @param  main   $main    The main class
	 * @param  string $action  The action to take
	 * @param  string $str     The modfication
	 * @return string          Returns the resulting URI on success, FALSE otherwise
	 */
	public static function query(main &$main, &$object, $action, $str) {
		if (isset($str[0]) && $str[0] == '?') {
			$str = substr($str, 1);
		}
		
		actions::callback($object, $action, __FUNCTION__, $str);
		$main->query = new query($object->query);
		return generate::string($main, $object);
	}
	
	/**
	 * Modfies the Fragment
	 * 
	 * @param  object $object  The object to modify
	 * @param  string $action  The action to take
	 * @param  string $str     The modfication
	 * @return string          Returns the resulting URI on success, FALSE otherwise
	 */
	public static function fragment(main &$main, &$object, $action, $str) {
		if (isset($str[0]) && $str[0] == '#') {
			$str = substr($str, 1);
		}
		$str = urlencode($str);
		
		actions::callback($object, $action, __FUNCTION__, $str);
		return generate::string($main, $object);
	}
}
