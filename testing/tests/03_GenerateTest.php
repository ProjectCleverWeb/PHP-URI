<?php

namespace projectcleverweb\uri;

class GenerateTest extends URI_Testing_Config {
	
	/**
	 * @test
	 * @depends projectcleverweb\uri\ParseTest::Advanced_Parsing
	 */
	public function Simple_Output() {
		$uri = $this->uri->advanced1;
		$this->assertEquals($this->input->advanced1, $uri->str());
		$this->expectOutputString($this->input->advanced1);
		$uri->p_str();
	}
	
	/**
	 * @test
	 * @depends projectcleverweb\uri\ParseTest::Advanced_Parsing
	 */
	public function Aliases() {
		$uri = $this->uri->advanced1;
		
		// Check Var Aliases
		$this->assertSame($uri->scheme, $uri->protocol);
		$this->assertSame($uri->user, $uri->username);
		$this->assertSame($uri->pass, $uri->password);
		$this->assertSame($uri->host, $uri->fqdn);
		$this->assertSame($uri->host, $uri->domain);
		
		// Check Method Aliases
		$this->assertSame($uri->arr(), $uri->to_array());
		$this->assertSame($uri->str(), $uri->to_string());
		$this->assertSame($uri->str(), $uri->__toString());
	}
	
	/**
	 * @test
	 * @depends projectcleverweb\uri\ParseTest::Advanced_Parsing
	 */
	public function Simple_Replace() {
		$this->assertEquals('http://example.com/alternative', $this->uri->simple->replace('PATH', '/alternative'));
	}
	
	/**
	 * @test
	 * @depends projectcleverweb\uri\ParseTest::Advanced_Parsing
	 * @depends Simple_Replace
	 * @depends Simple_Output
	 * @depends Aliases
	 * @depends projectcleverweb\uri\ErrorsTest::Invalid_Section_Errors
	 */
	public function Reset() {
		$uri = $this->uri->average1;
		$this->assertEquals('http://example.com/alt/path/to/file.ext', $uri->replace('PATH', '/alt/path/to/file.ext'));
		$uri->reset();
		$this->assertEquals('http://example.com/path/to/file.ext', $uri->str());
	}
	
	/**
	 * @test
	 * @depends Reset
	 */
	public function Cloning() {
		$uri = $this->uri->average1;
		
		// Make sure that clone is still NOT working as the user expects (reference still intact)
		$clone = @clone $uri;
		$clone->host = 'google.com';
		$this->assertSame($uri->host, $clone->host);
		
		$uri->reset();
		
		// Make sure that standardized cloning method works
		$uri->host = 'facebook.com';
		$new_clone = $uri->make_clone();
		$new_clone->host = 'stackoverflow.com';
		$this->assertSame('stackoverflow.com', $new_clone->host);
		$this->assertSame('facebook.com', $uri->host);
		$this->assertSame($uri->input, $new_clone->input);
	}
	
	/**
	 * @test
	 * @depends Reset
	 */
	public function To_Array() {
		// Test both when there is and isn't pre-existing data
		$uri1 = $this->uri->minimal;
		$uri2 = $this->uri->advanced1;
		
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
		
		// https://user:pass@example.com:777/path/to/script.php?query=str#fragment
		
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
		$uri1 = $this->uri->minimal;
		$uri2 = $this->uri->advanced1;
		
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
		$uri1 = $this->uri->minimal;
		$uri2 = $this->uri->advanced1;
		
		$this->assertSame(array(), $uri1->query_arr());
		$this->assertSame(array('query' => 'str'), $uri2->query_arr());
	}
	
	/**
	 * @test
	 * @depends Reset
	 */
	public function Invoke() {
		$uri = $this->uri->minimal;
		$this->assertSame($uri, $uri());
	}
	
	/**
	 * @test
	 */
	public function Issue() {
		// $uri = $this->uri->minimal;
		// $uri->replace('user', 'john');
		// $uri->replace('user', 'jdoe');
		// print_r($uri);
		// $this->expectOutputString('dada');
		// $this->assertEquals()
	}
	
	/**
	 * @test
	 * @depends Reset
	 */
	public function Sudo_Magic_Constants() {
		// Test both when there is and isn't pre-existing data
		$uri1 = $this->uri->advanced1;
		$uri2 = $this->uri->minimal;
		
		// __get() (parser checks normal __get())
		$this->assertNull(@$uri1->does_not_exist);
		
		// __set()
		$uri2->scheme_name = 'http';
		$this->assertEquals('http://example.com', $uri2->str());
		$uri2->scheme_symbols = ':';
		$this->assertEquals('http:example.com', $uri2->str());
		$uri2->scheme = 'https://';
		$this->assertEquals('https://example.com', $uri2->str());
		$uri2->user = 'username';
		$this->assertEquals('https://username@example.com', $uri2->str());
		$uri2->pass = 'password';
		$this->assertEquals('https://username:password@example.com', $uri2->str());
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
		$this->assertNull(@$uri1->does_not_exist);
		
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
		$this->assertSame('', $uri1->query->str());
		$uri1->reset();
		unset($uri1->fragment);
		$this->assertSame('', $uri1->fragment);
		$uri1->reset();
		
		error_reporting(0);
		unset($uri1->does_not_exist); // Language Construct
		error_reporting(-1);
		$this->assertNull(@$uri1->does_not_exist);
	}
}
