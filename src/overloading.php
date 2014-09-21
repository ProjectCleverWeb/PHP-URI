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
 * @package   projectcleverweb\uri\overloading
 * @license   http://opensource.org/licenses/MIT
 * @see       http://en.wikipedia.org/wiki/URI_scheme
 */

namespace projectcleverweb\uri;

/**
 * The Overloading Magic Methods Class
 * 
 * This class handles all the overloading methods as defined by PHP. At the
 * time of this comment, they are __get(), __set(), __isset(), __unset,
 * __call(), and __callStatic().
 * 
 * @see http://php.net/manual/en/language.oop5.overloading.php
 * @property object $object The data object from the main class
 */
abstract class overloading {
	
	/**
	 * Allows access to the different parts of the URI to be synchronized. This
	 * means that what is returned should always be accurate. Triggers a notice
	 * if the variable cannot be accessed.
	 * 
	 * @param  string $name The requested variable
	 * @return string|null  The value of the variable, or NULL if it can't be accessed
	 */
	public function __get($name) {
		if (isset($this->object->$name) && $name == 'query') {
			return $this->query;
		} elseif (isset($this->object->$name)) {
			generate::scheme($this->object);
			generate::authority($this->object);
			return $this->object->$name;
		} else {
			$this->_err('UNDEFINED', debug_backtrace(), $name);
			return NULL;
		}
	}
	
	/**
	 * Allows access to the different parts of the URI to be synchronized. This
	 * means that what is returned should always be accurate. Triggers a notice
	 * if the variable cannot be accessed.
	 * 
	 * @param  string $name  The requested variable
	 * @param  string $value The new value for the variable
	 * @return string|null   The new value of the variable, or NULL if it can't be accessed
	 */
	public function __set($name, $value) {
		if (isset($this->object->$name) && $name == 'query') {
			$this->query = new query($value);
			return $this->query;
		} elseif (isset($this->object->$name) && $name != 'authority') {
			$this->replace($name, $value);
			return $value;
		} else {
			$this->_err('FORBIDDEN', debug_backtrace(), $name);
			return NULL;
		}
	}
	
	/**
	 * Allows access to the different parts of the URI to be synchronized. This
	 * means that what is returned should always be accurate.
	 * 
	 * @param  string  $name The requested variable
	 * @return boolean       Returns TRUE if the variable is not empty, FALSE otherwise
	 */
	public function __isset($name) {
		if ($name == 'query') {
			return !empty($this->query->data);
		}
		generate::scheme($this->object);
		generate::authority($this->object);
		if (isset($this->object->$name)) {
			return !empty($this->object->$name);
		}
		return FALSE;
	}
	
	/**
	 * Allows access to the different parts of the URI to be synchronized. This
	 * means that what is returned should always be accurate. Triggers a notice
	 * if the variable cannot be accessed.
	 * 
	 * @param  string $name The requested variable
	 * @return boolean      Returns TRUE if the varaible was successfully emptied, FALSE otherwise.
	 */
	public function __unset($name) {
		if (isset($this->object->$name) && $name == 'query') {
			$this->query = new query;
			return TRUE;
		} elseif (isset($this->object->$name) && $name != 'host' && $name != 'authority') {
			$this->replace($name, '');
			return TRUE;
		} else {
			$this->_err('FORBIDDEN', debug_backtrace(), $name);
			return FALSE;
		}
	}
	
	/**
	 * A unknown/forbidden property has been called. trigger an error.
	 * 
	 * @param  string $type  Type of error
	 * @param  array  $trace The output from debug_backtrace()
	 * @param  string $name  Property name
	 * @return void
	 */
	private function _err($type, $trace, $name) {
		$fmt = 'Undifined property via <code>%1$s::%2$s()</code>: Property <code>%3$s</code> cannot be unset in <b>%4$s</b> on line <b>%5$s</b>. Error triggered';
		if ($type == 'FORBIDDEN') {
			$fmt = 'Forbidden property via <code>%1$s::%2$s()</code>: Property <code>%3$s</code> cannot be unset in <b>%4$s</b> on line <b>%5$s</b>. Error triggered';
		}
		
		trigger_error(sprintf($fmt, $trace[0]['class'], $trace[0]['function'], $name, $trace[0]['file'], $trace[0]['line']), E_USER_NOTICE);
	}
}
