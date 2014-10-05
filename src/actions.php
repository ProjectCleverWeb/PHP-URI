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
 * @package   projectcleverweb\uri\actions
 * @license   http://opensource.org/licenses/MIT
 * @see       http://en.wikipedia.org/wiki/URI_scheme
 */

namespace projectcleverweb\uri;

/**
 * The Actions Class
 * 
 * This class handlles the available actions
 */
class actions {
	
	/**
	 * Acts as universal alias to the modify class, ensuring the call is viable
	 * 
	 * @param  main        $main    The main class
	 * @param  data_object $object  The primary data object
	 * @param  string      $action  The action to take
	 * @param  string      $section The section of the object to modify
	 * @param  string      $str     The modfication
	 * @return string|false         Returns the resulting URI on success, FALSE otherwise
	 */
	public static function modify(main &$main, data_object &$object, $action, $section, $str) {
		settype($section, 'string');
		settype($str, 'string');
		$section = strtolower($section);
		
		if (is_callable(array(__NAMESPACE__.'\\modify', $section))) {
			return call_user_func_array(array(__NAMESPACE__.'\\modify', $section), array(&$main, &$object, $action, $str));
		} else {
			return FALSE;
		}
	}
	
	/**
	 * Handles which action is taken; since there are only 3 very simple actions
	 * that are used several times, it makes sense to put them all in 1 method.
	 * 
	 * @param  data_object $object  The primary data object
	 * @param  string      $action  The action to take
	 * @param  string      $section The section of the object to modify
	 * @param  string      $str     The modfication
	 * @return void
	 */
	public static function callback(data_object &$object, $action, $section, $str) {
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
