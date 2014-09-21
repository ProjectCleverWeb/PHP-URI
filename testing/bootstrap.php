<?php
/**
 * Bootstrap
 * =========
 * This file creates each of the instances used across all of the tests. It
 * also calls the autoload script and creates setUp() and tearDown() methods
 * for each of the test classes.
 */

namespace projectcleverweb\uri;

// Call the autoload script
require_once realpath(__DIR__.'/../autoload.php');

// The main class MUST be extended (it is abstract)
class uri extends main {}

// Inputs
$inputs = (object) array(
	'minimal'   => 'example.com',
	'simple'    => 'http://example.com/sample',
	'average1'  => 'http://example.com/path/to/file.ext',
	'average2'  => 'https://google.com/path/to/file.ext?a=1&b=2&c=3',
	'average3'  => 'https://facebook.co.uk/file.ext?a=1&b=2&c=3',
	'advanced1' => 'https://user:pass@example.com:777/path/to/script.php?query=str#fragment',
	'advanced2' => 'ftp://jdoe:Pass123@example.com:21/home',
	'advanced3' => 'skype:user123?call',
	'advanced4' => 'jdoe@google.com',
	'advanced5' => 'git@github.com:ProjectCleverWeb/PHP-URI.git',
	'advanced6' => 'google.com?empty',
	'error1'    => '',
	'error2'    => array(),
	'error3'    => (object) array(),
	'error4'    => 1,
	'error5'    => FALSE
);

// Instances
$instances = (object) array(
	'minimal'   => new uri($inputs->minimal),
	'simple'    => new uri($inputs->simple),
	'average1'  => new uri($inputs->average1),
	'average2'  => new uri($inputs->average2),
	'average3'  => new uri($inputs->average3),
	'advanced1' => new uri($inputs->advanced1),
	'advanced2' => new uri($inputs->advanced2),
	'advanced3' => new uri($inputs->advanced3),
	'advanced4' => new uri($inputs->advanced4),
	'advanced5' => new uri($inputs->advanced5),
	'advanced6' => new uri($inputs->advanced6),
	'error1'    => new uri($inputs->error1),
	'error2'    => new uri($inputs->error2),
	'error3'    => new uri($inputs->error3),
	'error4'    => new uri($inputs->error4),
	'error5'    => new uri($inputs->error5)
);

/**
 * Since all the setUp() and tearDown() methods are the same, we just extend
 * this class in each of the testing classes. (just make sure this class
 * always extends the PHPUnit TestCase class AND is abstract)
 */
abstract class URI_Testing_Config extends \PHPUnit_Framework_TestCase {
	
	public $uri;
	public $input;
	
	/**
	 * Give access to the uri instances
	 */
	public function setUp() {
		if (!isset($this->uri) || !isset($this->input)) {
			global $instances;
			global $inputs;
			$this->uri   = &$instances;
			$this->input = &$inputs;
		}
	}
	
	/**
	 * Reset all the instances
	 */
	public function tearDown() {
		global $instances;
		foreach ($instances as $instance) {
			$instance->reset();
		}
	}
}
