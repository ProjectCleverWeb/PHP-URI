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
 * @package   projectcleverweb\uri\query
 * @license   http://opensource.org/licenses/MIT
 * @see       http://en.wikipedia.org/wiki/URI_scheme
 */

namespace projectcleverweb\uri;

/**
 * The Query Chaining Class
 * 
 * This class is like the query class except that only actionable methods can
 * be called. This mean they must either modify the query or print something.
 * It also means that the normal return of all methods is replaced with the
 * main chaining class in its current instance.
 */
class chain_query {
	
	/*** Variables ***/
	
	private $class;
	private $chain;
	public  $error_count;
	
	/*** Magic Methods ***/
	
	/**
	 * Simple method to init a chainable object for queries
	 * 
	 * @param query $class The current 'query' instance
	 * @param chain $chain The current 'chain' instance
	 */
	public function __construct(query $class, chain $chain) {
		$this->class       = &$class;
		$this->chain       = &$chain;
		$this->error_count = &$chain->error_count;
	}
	
	/**
	 * Return the main chaining instance when invoked
	 * 
	 * @return chain The main chaining class
	 */
	public function __invoke() {
		return $this->chain;
	}
	
	/*** Methods ***/
	
	/**
	 * Chainable alias to query::change_build() within the current instance
	 * 
	 * @param  string $prefix    The numeric prefix according to the PHP docs
	 * @param  string $seperator The seperator you want to use in you query string (default is '&')
	 * @param  int    $spec      The encoding to use (default is RFC3986)
	 * @return chain             The main chaining class
	 */
	public function change_build($prefix = '', $separator = '&', $spec = PHP_QUERY_RFC3986) {
		$this->class->change_build($prefix, $separator, $spec);
		return $this->chain;
	}
	
	/**
	 * Chainable alias to query::p_str() within the current instance
	 * 
	 * @param  string $prepend The string to prepend to the output
	 * @param  string $append  The string to append to the output
	 * @return chain           The main chaining class
	 */
	public function p_str($prepend = '', $append = '') {
		$this->class->p_str($prepend, $append);
		return $this->chain;
	}
	
	/**
	 * Chainable alias to query::add() within the current instance
	 * 
	 * @param string $key   The key to add data to
	 * @param mixed  $value The data to add
	 * @return chain        The main chaining class
	 */
	public function add($key, $value) {
		$this->class->add($key, $value);
		return $this->chain;
	}
	
	/**
	 * Chainable alias to query::replace() within the current instance
	 * 
	 * @param string $key   The key to replace (or add)
	 * @param mixed  $value The data to add
	 * @return chain        The main chaining class
	 */
	public function replace($key, $value) {
		$this->class->replace($key, $value);
		return $this->chain;
	}
	
	/**
	 * Chainable alias to query::remove() within the current instance
	 * 
	 * @param string $key The key to remove (if it exists)
	 * @return chain      The main chaining class
	 * @return void
	 */
	public function remove($key) {
		$this->class->remove($key);
		return $this->chain;
	}
	
	/**
	 * Chainable alias to query::rename() within the current instance
	 * 
	 * @param string $key The key to rename
	 * @param string $key The new name of $key
	 * @return chain      The main chaining class
	 */
	public function rename($key, $new_key) {
		$this->class->rename($key, $new_key);
		return $this->chain;
	}
	
	/**
	 * Chainable alias to query::reset() within the current instance
	 * 
	 * @return chain The main chaining class
	 */
	public function reset() {
		$this->class->reset();
		return $this->chain;
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
				'The method <code>%1$s::%2$s()</code> cannot be chained in <b>%3$s</b> on line <b>%4$s</b>. Error triggered',
				$trace[0]['class'],
				$trace[0]['function'],
				$trace[0]['file'],
				$trace[0]['line']
			),
			E_USER_NOTICE
		);
	}
	
	/**
	 * Invalid Chaining Method
	 */
	public function str() {
		$this->_err(debug_backtrace());
		return $this->chain;
	}
	
	/**
	 * Invalid Chaining Method
	 */
	public function to_string() {
		$this->_err(debug_backtrace());
		return $this->chain;
	}
	
	/**
	 * Invalid Chaining Method
	 */
	public function arr() {
		$this->_err(debug_backtrace());
		return $this->chain;
	}
	
	/**
	 * Invalid Chaining Method
	 */
	public function to_array() {
		$this->_err(debug_backtrace());
		return $this->chain;
	}
	
	/**
	 * Invalid Chaining Method
	 */
	public function exists($key) {
		$this->_err(debug_backtrace());
		return $this->chain;
	}
	
	/**
	 * Invalid Chaining Method
	 */
	public function get($key) {
		$this->_err(debug_backtrace());
		return $this->chain;
	}
	
	/**
	 * Invalid Chaining Method
	 */
	public function make_clone() {
		$this->_err(debug_backtrace());
		return $this->chain;
	}
}
