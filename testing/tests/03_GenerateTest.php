<?php

namespace URI;

/**
 * @requires PHP 5.4
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
	public function Cloning() {
		$uri1 = new \uri('example.com/original/path');
		
		// Check For Errors
		$this->assertEmpty($uri1->error);
		
		// Ensure that clone is still NOT working as the user expects (reference still intact)
		$clone = @clone $uri1;
		$clone->host = 'google.com';
		$this->assertSame($uri1->host, $clone->host);
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
	
	/**
	 * @test
	 * @depends Reset
	 */
	public function Sudo_Magic_Constants() {
		// Test both when there is and isn't pre-existing data
		$uri1 = new \uri('https://user:pass@example.com:777/path/to/script.php?query=str#fragment');
		$uri2 = new \uri('google.com');
		
		// Check For Errors
		$this->assertEmpty($uri1->error);
		$this->assertEmpty($uri2->error);
		
		// __get() (parser checks normal __get())
		$this->assertSame(NULL, @$uri1->does_not_exist);
		
		// __set()
		$uri2->scheme_name = 'http';
		$this->assertEquals('http://google.com', $uri2->str());
		$uri2->scheme_symbols = ':';
		$this->assertEquals('http:google.com', $uri2->str());
		$uri2->scheme = 'https://';
		$this->assertEquals('https://google.com', $uri2->str());
		$uri2->user = 'username';
		$this->assertEquals('https://username@google.com', $uri2->str());
		$uri2->pass = 'password';
		$this->assertEquals('https://username:password@google.com', $uri2->str());
		$uri2->host = 'facebook.com';
		$this->assertEquals('https://username:password@facebook.com', $uri2->str());
		$uri2->port = '8181';
		$this->assertEquals('https://username:password@facebook.com:8181', $uri2->str());
		$uri2->path = '/index.php';
		$this->assertEquals('https://username:password@facebook.com:8181/index.php', $uri2->str());
		$uri2->query = 'q=1';
		$this->assertEquals('https://username:password@facebook.com:8181/index.php?q=1', $uri2->str());
		$uri2->fragment = 'frag';
		$this->assertEquals('https://username:password@facebook.com:8181/index.php?q=1#frag', $uri2->str());
		
		$uri2->reset();
		
		@$uri1->does_not_exist = 'nothing';
		$this->assertSame(NULL, @$uri1->does_not_exist);
		
		// __isset()
		$this->assertTrue(isset($uri1->scheme_name));
		$this->assertFalse(isset($uri2->scheme_name));
		$this->assertTrue(isset($uri1->scheme_symbols));
		$this->assertFalse(isset($uri2->scheme_symbols));
		$this->assertTrue(isset($uri1->scheme));
		$this->assertFalse(isset($uri2->scheme));
		$this->assertTrue(isset($uri1->user));
		$this->assertFalse(isset($uri2->user));
		$this->assertTrue(isset($uri1->pass));
		$this->assertFalse(isset($uri2->pass));
		$this->assertTrue(isset($uri1->host));
		$this->assertTrue(isset($uri2->host));
		$this->assertTrue(isset($uri1->port));
		$this->assertFalse(isset($uri2->port));
		$this->assertTrue(isset($uri1->path));
		$this->assertFalse(isset($uri2->path));
		$this->assertTrue(isset($uri1->query));
		$this->assertFalse(isset($uri2->query));
		$this->assertTrue(isset($uri1->fragment));
		$this->assertFalse(isset($uri2->fragment));
		
		$this->assertFalse(isset($uri1->does_not_exist));
		
		// __unset()
		unset($uri1->scheme_name);
		$this->assertSame('', $uri1->scheme_name);
		$uri1->reset();
		unset($uri1->scheme_symbols);
		$this->assertSame('', $uri1->scheme_symbols);
		$uri1->reset();
		unset($uri1->scheme);
		$this->assertSame('', $uri1->scheme);
		$uri1->reset();
		unset($uri1->user);
		$this->assertSame('', $uri1->user);
		$uri1->reset();
		unset($uri1->pass);
		$this->assertSame('', $uri1->pass);
		$uri1->reset();
		error_reporting(0);
		unset($uri1->host); // Language Construct
		error_reporting(-1);
		$this->assertEquals('example.com', $uri1->host);
		unset($uri1->port);
		$this->assertSame('', $uri1->port);
		$uri1->reset();
		unset($uri1->path);
		$this->assertSame('', $uri1->path);
		$uri1->reset();
		unset($uri1->query);
		$this->assertSame('', $uri1->query);
		$uri1->reset();
		unset($uri1->fragment);
		$this->assertSame('', $uri1->fragment);
		$uri1->reset();
		
		error_reporting(0);
		unset($uri1->does_not_exist); // Language Construct
		error_reporting(-1);
		$this->assertSame(NULL, @$uri1->does_not_exist);
	}
}
