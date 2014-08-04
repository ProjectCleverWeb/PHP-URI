<?php

namespace URI;

/**
 * @requires PHP 5.3.7
 */
class ModifyQueryTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 * @test
	 * @depends URI\GenerateTest::Reset
	 */
	public function Modify_Scheme_Name() {
		// Test both when there is and isn't pre-existing data
		$uri1 = new \uri('example.com');
		$uri2 = new \uri('https://user:pass@example.com:777/path/to/script.php?query=str#fragment');
		
	}
	
}
