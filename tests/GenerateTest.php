<?php

namespace URI;

/**
 * @requires PHP 5.3.7
 */
class GenerateTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 * @test
	 * @depends URI\ParseTest::Advanced_Parsing
	 */
	public function Simple_Output() {
		$input = 'https://user:pass@example.com:777/path/to/script.php?query=str#fragment';
		$uri1 = new \uri($input);
		
		// Check For Errors
		$this->assertEmpty($uri1->error);
		
		// Check Output
		$this->assertEquals($input, $uri1->input);
		$this->assertEquals($input, $uri1->str());
		$this->expectOutputString($input);
		$uri1->p_str();
	}
	
	/**
	 * @test
	 * @depends URI\ParseTest::Advanced_Parsing
	 */
	public function Aliases() {
		$uri1 = new \uri('https://user:pass@example.com:777/path/to/script.php?query=str#fragment');
		
		// Check For Errors
		$this->assertEmpty($uri1->error);
		
		// Check Var Aliases
		$this->assertSame($uri1->scheme, $uri1->protocol);
		$this->assertSame($uri1->user, $uri1->username);
		$this->assertSame($uri1->pass, $uri1->password);
		$this->assertSame($uri1->host, $uri1->fqdn);
		$this->assertSame($uri1->host, $uri1->domain);
		
		// Check Method Aliases
		$this->assertSame($uri1->arr(), $uri1->to_array());
		$this->assertSame($uri1->str(), $uri1->to_string());
		$this->assertSame($uri1->str(), $uri1->__toString());
	}
	
	/**
	 * @test
	 * @depends URI\ParseTest::Advanced_Parsing
	 */
	public function Simple_Replace() {
		$uri1 = new \uri('example.com/original/path');
		
		// Check For Errors
		$this->assertEmpty($uri1->error);
		
		// Check Replace
		$this->assertEquals('example.com/alternative/path', $uri1->replace('PATH', '/alternative/path'));
	}
	
	/**
	 * @test
	 * @depends URI\ParseTest::Advanced_Parsing
	 * @depends Simple_Replace
	 * @depends Simple_Output
	 * @depends Aliases
	 * @depends URI\ErrorsTest::Parse_Errors
	 * @depends URI\ErrorsTest::Invalid_Section_Errors
	 */
	public function Reset() {
		$uri1 = new \uri('example.com/original/path');
		
		// Check For Errors
		$this->assertEmpty($uri1->error);
		
		// Check Replace
		$this->assertEquals('example.com/alternative/path', $uri1->replace('PATH', '/alternative/path'));
		$uri1->reset();
		$this->assertEquals('example.com/original/path', $uri1->str());
	}
	
	/**
	 * @test
	 * @depends Reset
	 */
	public function To_Array() {
		// Test both when there is and isn't pre-existing data
		$uri1 = new \uri('example.com');
		$uri2 = new \uri('https://user:pass@example.com:777/path/to/script.php?query=str#fragment');
		
		// Check For Errors
		$this->assertEmpty($uri1->error);
		$this->assertEmpty($uri2->error);
		
		// Setup
		$arr1 = $uri1->arr();
		$arr2 = $uri2->arr();
		
		// Check that all keys are set (even if empty)
		$this->assertArrayHasKey('authority', $arr1);
		$this->assertArrayHasKey('domain', $arr1);
		$this->assertArrayHasKey('fqdn', $arr1);
		$this->assertArrayHasKey('fragment', $arr1);
		$this->assertArrayHasKey('host', $arr1);
		$this->assertArrayHasKey('pass', $arr1);
		$this->assertArrayHasKey('path', $arr1);
		$this->assertArrayHasKey('port', $arr1);
		$this->assertArrayHasKey('protocol', $arr1);
		$this->assertArrayHasKey('query', $arr1);
		$this->assertArrayHasKey('scheme', $arr1);
		$this->assertArrayHasKey('scheme_name', $arr1);
		$this->assertArrayHasKey('scheme_symbols', $arr1);
		$this->assertArrayHasKey('user', $arr1);
		$this->assertArrayHasKey('username', $arr1);
		
		// Check Key Aliases
		$this->assertSame($arr2['scheme'], $arr2['protocol']);
		$this->assertSame($arr2['user'], $arr2['username']);
		$this->assertSame($arr2['pass'], $arr2['password']);
		$this->assertSame($arr2['host'], $arr2['domain']);
		$this->assertSame($arr2['host'], $arr2['fqdn']);
		
		// Check Values
		$this->assertEquals('user:pass@example.com:777', $arr2['authority']);
		$this->assertEquals('fragment', $arr2['fragment']);
		$this->assertEquals('example.com', $arr2['host']);
		$this->assertEquals('pass', $arr2['pass']);
		$this->assertEquals('/path/to/script.php', $arr2['path']);
		$this->assertEquals('777', $arr2['port']);
		$this->assertEquals('query=str', $arr2['query']);
		$this->assertEquals('https://', $arr2['scheme']);
		$this->assertEquals('https', $arr2['scheme_name']);
		$this->assertEquals('://', $arr2['scheme_symbols']);
		$this->assertEquals('user', $arr2['user']);
	}
	
	/**
	 * @test
	 * @depends Reset
	 */
	public function Path_Info() {
		// Test both when there is and isn't pre-existing data
		$uri1 = new \uri('example.com');
		$uri2 = new \uri('https://user:pass@example.com:777/path/to/script.php?query=str#fragment');
		
		// Check For Errors
		$this->assertEmpty($uri1->error);
		$this->assertEmpty($uri2->error);
		
		// Setup
		$arr1 = $uri1->path_info();
		$arr2 = $uri2->path_info();
		
		// Check that all keys are set (even if empty)
		$this->assertArrayHasKey('array', $arr1);
		$this->assertArrayHasKey('basename', $arr1);
		$this->assertArrayHasKey('dirname', $arr1);
		$this->assertArrayHasKey('extension', $arr1);
		$this->assertArrayHasKey('filename', $arr1);
		
		// Check Values
		$this->assertEquals('path', $arr2['array'][0]);
		$this->assertEquals('to', $arr2['array'][1]);
		$this->assertEquals('script.php', $arr2['array'][2]);
		$this->assertEquals('script.php', $arr2['basename']);
		$this->assertEquals('/path/to', $arr2['dirname']);
		$this->assertEquals('php', $arr2['extension']);
		$this->assertEquals('script', $arr2['filename']);
	}
	
	/**
	 * @test
	 * @depends Reset
	 */
	public function Queries() {
		// Test both when there is and isn't pre-existing data
		$uri1 = new \uri('example.com');
		$uri2 = new \uri('https://user:pass@example.com:777/path/to/script.php?query=str#fragment');
		
		// Check For Errors
		$this->assertEmpty($uri1->error);
		$this->assertEmpty($uri2->error);
		
		$this->assertSame(array(), $uri1->query_arr());
		$this->assertSame(array('query' => 'str'), $uri2->query_arr());
	}
	
	/**
	 * @test
	 * @depends Reset
	 */
	public function Invoke() {
		// Test both when there is and isn't pre-existing data
		$uri1 = new \uri('example.com');
		
		// Check For Errors
		$this->assertEmpty($uri1->error);
		
		$this->assertEquals('example.com', $uri1());
	}
}
