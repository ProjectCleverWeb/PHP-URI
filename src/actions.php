<?php

namespace uri;

/**
 * The Actions Class
 * 
 * This class handlles the available actions
 */
class actions {
	
	/**
	 * Acts as universal alias to the modify class, ensuring the call is viable
	 * 
	 * @param  object $object  The object to modify
	 * @param  string $action  The action to take
	 * @param  string $section The section of the object to modify
	 * @param  string $str     The modfication
	 * @return string|false    Returns the resulting URI on success, FALSE otherwise
	 */
	public static function modify(&$object, $action, $section, $str) {
		settype($section, 'string');
		settype($str, 'string');
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
	public static function callback(&$object, $action, $section, $str) {
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
