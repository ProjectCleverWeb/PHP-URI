<?php

namespace URI;

/**
 * @requires PHP 5.4
 */
class ParseTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 * @test
	 */
	public function Minimal_Parsing() {
		$uri1 = new \uri('example.com');
		
		// Check For Errors
		$this->assertEmpty($uri1->error);
		
		// Check Parsing
		$this->assertEquals('example.com', $uri1->host);
	}
	
	/**
	 * @test
	 * @depends Minimal_Parsing
	 */
	public function Simple_Parsing() {
		$uri1 = new \uri('http://example.com/sample');
		
		// Check For Errors
		$this->assertEmpty($uri1->error);
		
		// Check Parsing
		$this->assertEquals('http://', $uri1->scheme);
		$this->assertEquals('http', $uri1->scheme_name);
		$this->assertEquals('example.com', $uri1->host);
		$this->assertEquals('/sample', $uri1->path);
	}
	
	/**
	 * @test
	 * @depends Simple_Parsing
	 */
	public function Advanced_Parsing() {
		$uri1 = new \uri('https://user:pass@example.com:777/path/to/script.php?query=str#fragment');
		
		// Check For Errors
		$this->assertEmpty($uri1->error);
		
		// Check Parsing
		$this->assertEquals('https://', $uri1->scheme);
		$this->assertEquals('https', $uri1->scheme_name);
		$this->assertEquals('://', $uri1->scheme_symbols);
		$this->assertEquals('user', $uri1->user);
		$this->assertEquals('pass', $uri1->pass);
		$this->assertEquals('example.com', $uri1->host);
		$this->assertEquals('777', $uri1->port);
		$this->assertEquals('/path/to/script.php', $uri1->path);
		$this->assertEquals('query=str', $uri1->query);
		$this->assertEquals('fragment', $uri1->fragment);
	}
}
