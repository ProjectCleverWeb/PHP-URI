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
 * @package   projectcleverweb\uri\chain
 * @license   http://opensource.org/licenses/MIT
 * @see       http://en.wikipedia.org/wiki/URI_scheme
 */

namespace projectcleverweb\uri;

/**
 * The Chaining Class
 * 
 * This class is like \projectcleverweb\uri\main except that only actionable
 * methods can be called. This mean they must either modify $object or print
 * something. It also means that the normal return of all methods is replaced
 * with this class in its' current instance.
 */
class chain {
	
	/*** Variables ***/
	private $class;
	public  $error_count;
	
	/*** Magic Methods ***/
	
	/**
	 * Simple method to init a chainable object
	 * 
	 * @param object $class The current instance of \projectcleverweb\uri\main
	 */
	public function __construct(&$class) {
		$this->class       = &$class;
		$this->error_count = 0;
		return $this;
	}
	
	/*** Methods ***/
	
	/**
	 * Chainable alias to \projectcleverweb\uri\main::replace() within the
	 * current instance
	 * 
	 * @return object This instance
	 */
	public function p_str($prepend = '', $append = '') {
		$this->class->p_str($prepend, $append);
		return $this;
	}
	
	/**
	 * Chainable alias to \projectcleverweb\uri\main::replace() within the
	 * current instance
	 * 
	 * @param  string $section The section to replace
	 * @param  string $str     The string to replace the section with
	 * @return object          This instance
	 */
	public function replace($section, $str) {
		if ($this->class->replace($section, $str) === FALSE) {
			$this->error_count++;
		}
		return $this;
	}
	
	/**
	 * Chainable alias to \projectcleverweb\uri\main::prepend() within the
	 * current instance
	 * 
	 * @param  string $section The section to prepend
	 * @param  string $str     The string to prepend the section with
	 * @return object          This instance
	 */
	public function prepend($section, $str) {
		if ($this->class->prepend($section, $str) === FALSE) {
			$this->error_count++;
		}
		return $this;
	}
	
	/**
	 * Chainable alias to \projectcleverweb\uri\main::append() within the
	 * current instance
	 * 
	 * @param  string $section The section to append
	 * @param  string $str     The string to append the section with
	 * @return object          This instance
	 */
	public function append($section, $str) {
		if ($this->class->append($section, $str) === FALSE) {
			$this->error_count++;
		}
		return $this;
	}
	
	/**
	 * Chainable alias to \projectcleverweb\uri\main::query_add() within the
	 * current instance
	 * 
	 * @param  string $key   The key to add
	 * @param  mixed  $value The value of $key
	 * @return object        This instance
	 */
	public function query_add($key, $value) {
		$this->class->query_add($key, $value);
		return $this;
	}
	
	/**
	 * Chainable alias to \projectcleverweb\uri\main::query_replace() within the
	 * current instance
	 * 
	 * @param  string $key   The key to replace
	 * @param  mixed  $value The value of $key
	 * @return object        This instance
	 */
	public function query_replace($key, $value) {
		$this->class->query_replace($key, $value);
		return $this;
	}
	
	/**
	 * Chainable alias to \projectcleverweb\uri\main::query_remove() within the
	 * current instance
	 * 
	 * @param  string $key The key to remove
	 * @return object      This instance
	 */
	public function query_remove($key) {
		$this->class->query_remove($key);
		return $this;
	}
	
	/**
	 * Chainable alias to \projectcleverweb\uri\main::query_rename() within the
	 * current instance
	 * 
	 * @param  string $key     The key to rename
	 * @param  string $new_key The new name of $key
	 * @return object          This instance
	 */
	public function query_rename($key, $new_key) {
		if ($this->class->query_rename($key, $new_key) === FALSE) {
			$this->error_count++;
		}
		return $this;
	}
	
	/**
	 * Chainable alias to \projectcleverweb\uri\main::reset() within the
	 * current instance
	 * 
	 * @return object This instance
	 */
	public function reset() {
		$this->class->reset();
		return $this;
	}
	
	/**
	 * Provides a simple error handle for invalid chainable methods
	 * 
	 * @param  array $trace Debug Backtrace
	 * @return void
	 */
	private function _err($trace) {
		$this->error_count++;
		trigger_error(
			sprintf(
				'The method <code>%1$s()</code> cannot be chained in <b>%2$s</b> on line <b>%3$s</b>. Error triggered',
				$trace[0]['function'],
				$trace[0]['file'],
				$trace[0]['line']
			),
			E_USER_NOTICE
		);
	}
	
	/*** Invalid Chaining Methods ***/
	
	/**
	 * Invalid Chaining Method
	 */
	public function str() {
		$this->_err(debug_backtrace());
		return $this;
	}
	
	/**
	 * Invalid Chaining Method
	 */
	public function to_string() {
		$this->_err(debug_backtrace());
		return $this;
	}
	
	/**
	 * Invalid Chaining Method
	 */
	public function arr() {
		$this->_err(debug_backtrace());
		return $this;
	}
	
	/**
	 * Invalid Chaining Method
	 */
	public function to_array() {
		$this->_err(debug_backtrace());
		return $this;
	}
	
	/**
	 * Invalid Chaining Method
	 */
	public function path_info() {
		$this->_err(debug_backtrace());
		return $this;
	}
	
	/**
	 * Invalid Chaining Method
	 */
	public function query_array() {
		$this->_err(debug_backtrace());
		return $this;
	}
	
	/**
	 * Invalid Chaining Method
	 */
	public function query_exists() {
		$this->_err(debug_backtrace());
		return $this;
	}
	
	/**
	 * Invalid Chaining Method
	 */
	public function query_get() {
		$this->_err(debug_backtrace());
		return $this;
	}
}
