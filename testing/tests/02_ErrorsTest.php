<?php

namespace URI;

/**
 * @requires PHP 5.4
 */
class ErrorsTest extends \PHPUnit_Framework_TestCase {
	/**
	 * @test
	 * @depends URI\ParseTest::Advanced_Parsing
	 */
	public function Parse_Errors() {
		// Invalid
		$uri1 = new \uri('');
		$uri2 = new \uri(array());
		$uri3 = new \uri((object) array());
		$uri4 = new \uri(1);
		
		// Invalid Input
		$this->assertEquals(FALSE, empty($uri1->error));
		$this->assertEquals(FALSE, empty($uri2->error));
		$this->assertEquals(FALSE, empty($uri3->error));
		$this->assertEquals(FALSE, empty($uri4->error));
	}
	
	/**
	 * @test
	 * @depends URI\ParseTest::Advanced_Parsing
	 */
	public function Invalid_Section_Errors() {
		$uri1 = new \uri('https://user:pass@example.com:777/path/to/script.php?query=str#fragment');
		
		// Invalid section to modify
		$this->assertEquals(FALSE, $uri1->append('invalid', ''));
		$this->assertEquals(FALSE, $uri1->prepend('invalid', ''));
		$this->assertEquals(FALSE, $uri1->replace('invalid', ''));
	}
	
	/**
	 * @test
	 * @depends URI\ParseTest::Advanced_Parsing
	 */
	public function Invalid_Scheme() {
		$uri1 = new \uri('https://user:pass@example.com:777/path/to/script.php?query=str#fragment');
		
		// Invalid section to modify;
		$this->assertEquals(FALSE, $uri1->replace('SCHEME_NAME', 'invalid/'));
		$this->assertEquals(FALSE, $uri1->replace('SCHEME_SYMBOLS', 'invalid'));
		$this->assertEquals(FALSE, $uri1->replace('SCHEME', 'invalid_scheme'));
	}
	
	/**
	 * @test
	 * @depends URI\ParseTest::Advanced_Parsing
	 */
	public function Invalid_Host() {
		$uri1 = new \uri('https://user:pass@example.com:777/path/to/script.php?query=str#fragment');
		
		// Invalid section to modify;
		$this->assertEquals(FALSE, $uri1->replace('HOST', 'invalid'));
	}
	
	/**
	 * @test
	 * @depends URI\ParseTest::Advanced_Parsing
	 */
	public function Invalid_Port() {
		$uri1 = new \uri('https://user:pass@example.com:777/path/to/script.php?query=str#fragment');
		
		// Invalid section to modify;
		$this->assertEquals(FALSE, $uri1->replace('PORT', 'invalid'));
	}
}
