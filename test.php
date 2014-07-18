<?php

require_once __DIR__.'/uri.lib.php';

/**
 * @requires PHP 5.3.7
 */
class URITest extends PHPUnit_Framework_TestCase {
	
	/**
	 * @test
	 */
	public function Minimal_Parsing() {
		$uri1 = new uri('example.com');
		
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
		$uri1 = new uri('http://example.com/sample');
		
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
		$uri1 = new uri('https://user:pass@example.com:777/path/to/script.php?query=str#fragment');
		
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
	
	
	/**
	 * @test
	 * @depends Advanced_Parsing
	 */
	public function Error_Catching() {
		// Invalid
		$uri1 = new uri('');
		$uri2 = new uri(array());
		$uri3 = new uri((object) array());
		$uri4 = new uri(1);
		// Valid
		$uri5 = new uri('https://user:pass@example.com:777/path/to/script.php?query=str#fragment');
		
		
		
		// Invalid Input
		$this->assertEquals(FALSE, empty($uri1->error));
		$this->assertEquals(FALSE, empty($uri2->error));
		$this->assertEquals(FALSE, empty($uri3->error));
		$this->assertEquals(FALSE, empty($uri4->error));
		
		// Invalid section to modify
		$this->assertEquals(FALSE, $uri5->append('invalid', ''));
		$this->assertEquals(FALSE, $uri5->prepend('invalid', ''));
		$this->assertEquals(FALSE, $uri5->replace('invalid', ''));
	}
	
	/**
	 * @test
	 * @depends Advanced_Parsing
	 */
	public function Simple_Output() {
		$input = 'https://user:pass@example.com:777/path/to/script.php?query=str#fragment';
		$uri1 = new uri($input);
		
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
	 * @depends Advanced_Parsing
	 */
	public function Aliases() {
		$uri1 = new uri('https://user:pass@example.com:777/path/to/script.php?query=str#fragment');
		
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
	 * @depends Advanced_Parsing
	 */
	public function Simple_Replace() {
		$uri1 = new uri('example.com/original/path');
		
		// Check For Errors
		$this->assertEmpty($uri1->error);
		
		// Check Replace
		$this->assertEquals('example.com/alternative/path', $uri1->replace('PATH', '/alternative/path'));
	}
	
	/**
	 * @test
	 * @depends Advanced_Parsing
	 * @depends Simple_Replace
	 * @depends Simple_Output
	 * @depends Aliases
	 * @depends Error_Catching
	 */
	public function Reset() {
		$uri1 = new uri('example.com/original/path');
		
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
	public function Modify_Scheme_Name() {
		// Test both when there is and isn't pre-existing data
		$uri1 = new uri('example.com');
		$uri2 = new uri('https://user:pass@example.com:777/path/to/script.php?query=str#fragment');
		
		// Check For Errors
		$this->assertEmpty($uri1->error);
		$this->assertEmpty($uri2->error);
		
		// Check Replace
		$this->assertEquals(
			'ftp://example.com',
			$uri1->replace('SCHEME_NAME', 'ftp')
		);
		$this->assertEquals(
			'ftp://user:pass@example.com:777/path/to/script.php?query=str#fragment',
			$uri2->replace('SCHEME_NAME', 'ftp')
		);
		// Check Prepend
		$this->assertEquals(
			'sftp://example.com',
			$uri1->prepend('SCHEME_NAME', 's')
		);
		// Check Append
		$this->assertEquals(
			'ftpes://user:pass@example.com:777/path/to/script.php?query=str#fragment',
			$uri2->append('SCHEME_NAME', 'es')
		);
	}
	
	/**
	 * @test
	 * @depends Reset
	 */
	public function Modify_Scheme_Symbols() {
		// Test both when there is and isn't pre-existing data
		$uri1 = new uri('example.com');
		$uri2 = new uri('https://user:pass@example.com:777/path/to/script.php?query=str#fragment');
		
		// Check For Errors
		$this->assertEmpty($uri1->error);
		$this->assertEmpty($uri2->error);
		
		// Check Replace
		$this->assertEquals(
			'//example.com',
			$uri1->replace('SCHEME_SYMBOLS', '//')
		);
		$this->assertEquals(
			'https:user:pass@example.com:777/path/to/script.php?query=str#fragment',
			$uri2->replace('SCHEME_SYMBOLS', ':')
		);
		// Check Prepend
		$this->assertEquals(
			'://example.com',
			$uri1->prepend('SCHEME_SYMBOLS', ':')
		);
		// Check Append
		$this->assertEquals(
			'https://user:pass@example.com:777/path/to/script.php?query=str#fragment',
			$uri2->append('SCHEME_SYMBOLS', '//')
		);
	}
	
	/**
	 * @test
	 * @depends Reset
	 */
	public function Modify_Scheme() {
		// Test both when there is and isn't pre-existing data
		$uri1 = new uri('example.com');
		$uri2 = new uri('https://user:pass@example.com:777/path/to/script.php?query=str#fragment');
		
		// Check For Errors
		$this->assertEmpty($uri1->error);
		$this->assertEmpty($uri2->error);
		
		// Check Replace
		$this->assertEquals(
			'//example.com',
			$uri1->replace('SCHEME', '//')
		);
		$this->assertEquals(
			'http:user:pass@example.com:777/path/to/script.php?query=str#fragment',
			$uri2->replace('SCHEME', 'http:')
		);
		// Check Prepend
		$this->assertEquals(
			'https://example.com',
			$uri1->prepend('SCHEME', 'https:')
		);
		// Check Append
		$this->assertEquals(
			'http://user:pass@example.com:777/path/to/script.php?query=str#fragment',
			$uri2->append('SCHEME', '//')
		);
	}
	
	/**
	 * @test
	 * @depends Reset
	 */
	public function Modify_User() {
		// Test both when there is and isn't pre-existing data
		$uri1 = new uri('example.com');
		$uri2 = new uri('https://user:pass@example.com:777/path/to/script.php?query=str#fragment');
		$uri3 = new uri('user@gmail.com'); // user w/out pass
		
		// Check For Errors
		$this->assertEmpty($uri1->error);
		$this->assertEmpty($uri2->error);
		
		// Check Replace
		$this->assertEquals(
			'doe@example.com',
			$uri1->replace('USER', 'doe')
		);
		$this->assertEquals(
			'https://john:pass@example.com:777/path/to/script.php?query=str#fragment',
			$uri2->replace('USER', 'john')
		);
		$uri3->user = 'jane';
		$this->assertEquals(
			'jane@gmail.com',
			$uri3->str()
		);
		// Check Prepend
		$this->assertEquals(
			'jdoe@example.com',
			$uri1->prepend('USER', 'j')
		);
		// Check Append
		$this->assertEquals(
			'https://johnd:pass@example.com:777/path/to/script.php?query=str#fragment',
			$uri2->append('USER', 'd')
		);
	}
	
	/**
	 * @test
	 * @depends Reset
	 */
	public function Modify_Pass() {
		// Test both when there is and isn't pre-existing data
		$uri1 = new uri('jdoe@example.com'); // MUST have a user to have a password
		$uri2 = new uri('https://user:pass@example.com:777/path/to/script.php?query=str#fragment');
		
		// Check For Errors
		$this->assertEmpty($uri1->error);
		$this->assertEmpty($uri2->error);
		
		// Check Replace
		$this->assertEquals(
			'jdoe:1234@example.com',
			$uri1->replace('PASS', '1234')
		);
		$this->assertEquals(
			'https://user:1234@example.com:777/path/to/script.php?query=str#fragment',
			$uri2->replace('PASS', '1234')
		);
		// Check Prepend
		$this->assertEquals(
			'jdoe:01234@example.com',
			$uri1->prepend('PASS', '0')
		);
		// Check Append
		$this->assertEquals(
			'https://user:12345@example.com:777/path/to/script.php?query=str#fragment',
			$uri2->append('PASS', '5')
		);
	}
	
	/**
	 * @test
	 * @depends Reset
	 */
	public function Modify_Host() {
		// Test both when there is and isn't pre-existing data
		$uri1 = new uri('example.com');
		$uri2 = new uri('https://user:pass@example.com:777/path/to/script.php?query=str#fragment');
		
		// Check For Errors
		$this->assertEmpty($uri1->error);
		$this->assertEmpty($uri2->error);
		
		// Check Replace
		$this->assertEquals(
			'google.com',
			$uri1->replace('HOST', 'google.com')
		);
		$this->assertEquals(
			'https://user:pass@sample.co:777/path/to/script.php?query=str#fragment',
			$uri2->replace('HOST', 'sample.co')
		);
		// Check Prepend
		$this->assertEquals(
			'www.google.com',
			$uri1->prepend('HOST', 'www.')
		);
		// Check Append
		$this->assertEquals(
			'https://user:pass@sample.co.uk:777/path/to/script.php?query=str#fragment',
			$uri2->append('HOST', '.uk')
		);
	}
	
	/**
	 * @test
	 * @depends Reset
	 */
	public function Modify_Port() {
		// Test both when there is and isn't pre-existing data
		$uri1 = new uri('example.com');
		$uri2 = new uri('https://user:pass@example.com:777/path/to/script.php?query=str#fragment');
		
		// Check For Errors
		$this->assertEmpty($uri1->error);
		$this->assertEmpty($uri2->error);
		
		// Check Replace
		$this->assertEquals(
			'example.com:1234',
			$uri1->replace('PORT', '1234')
		);
		$this->assertEquals(
			'https://user:pass@example.com:1234/path/to/script.php?query=str#fragment',
			$uri2->replace('PORT', '1234')
		);
		// Check Prepend
		$this->assertEquals(
			'example.com:01234',
			$uri1->prepend('PORT', '0')
		);
		// Check Append
		$this->assertEquals(
			'https://user:pass@example.com:12345/path/to/script.php?query=str#fragment',
			$uri2->append('PORT', '5')
		);
	}
	
	/**
	 * @test
	 * @depends Reset
	 */
	public function Modify_Path() {
		// Test both when there is and isn't pre-existing data
		$uri1 = new uri('example.com');
		$uri2 = new uri('https://user:pass@example.com:777/path/to/script.php?query=str#fragment');
		
		// Check For Errors
		$this->assertEmpty($uri1->error);
		$this->assertEmpty($uri2->error);
		
		// Check Replace
		$this->assertEquals(
			'example.com/path',
			$uri1->replace('PATH', '/path')
		);
		$this->assertEquals(
			'https://user:pass@example.com:777/path?query=str#fragment',
			$uri2->replace('PATH', '/path')
		);
		// Check Prepend
		$this->assertEquals(
			'example.com/sample/path',
			$uri1->prepend('PATH', '/sample')
		);
		// Check Append
		$this->assertEquals(
			'https://user:pass@example.com:777/path/to/some/random/file.txt?query=str#fragment',
			$uri2->append('PATH', '/to/some/random/file.txt')
		);
	}
	
	/**
	 * @test
	 * @depends Reset
	 */
	public function Modify_Query() {
		// Test both when there is and isn't pre-existing data
		$uri1 = new uri('example.com');
		$uri2 = new uri('https://user:pass@example.com:777/path/to/script.php?query=str#fragment');
		
		// Check For Errors
		$this->assertEmpty($uri1->error);
		$this->assertEmpty($uri2->error);
		
		// Check Replace
		$this->assertEquals(
			'example.com?r=1',
			$uri1->replace('QUERY', 'r=1')
		);
		$this->assertEquals(
			'https://user:pass@example.com:777/path/to/script.php?l=2#fragment',
			$uri2->replace('QUERY', 'l=2')
		);
		// Check Prepend
		$this->assertEquals(
			'example.com?q=s&r=1',
			$uri1->prepend('QUERY', 'q=s&')
		);
		// Check Append
		$this->assertEquals(
			'https://user:pass@example.com:777/path/to/script.php?l=2&p=z#fragment',
			$uri2->append('QUERY', '&p=z')
		);
	}
	
	/**
	 * @test
	 * @depends Reset
	 */
	public function Modify_Fragment() {
		// Test both when there is and isn't pre-existing data
		$uri1 = new uri('example.com');
		$uri2 = new uri('https://user:pass@example.com:777/path/to/script.php?query=str#fragment');
		
		// Check For Errors
		$this->assertEmpty($uri1->error);
		$this->assertEmpty($uri2->error);
		
		// Check Replace
		$this->assertEquals(
			'example.com#header',
			$uri1->replace('FRAGMENT', 'header')
		);
		$this->assertEquals(
			'https://user:pass@example.com:777/path/to/script.php?query=str#footer',
			$uri2->replace('FRAGMENT', 'footer')
		);
		// Check Prepend
		$this->assertEquals(
			'example.com#custom-header',
			$uri1->prepend('FRAGMENT', 'custom-')
		);
		// Check Append
		$this->assertEquals(
			'https://user:pass@example.com:777/path/to/script.php?query=str#footer-end',
			$uri2->append('FRAGMENT', '-end')
		);
	}
	
	/**
	 * @test
	 * @depends Reset
	 */
	public function To_Array() {
		// Test both when there is and isn't pre-existing data
		$uri1 = new uri('example.com');
		$uri2 = new uri('https://user:pass@example.com:777/path/to/script.php?query=str#fragment');
		
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
		$uri1 = new uri('example.com');
		$uri2 = new uri('https://user:pass@example.com:777/path/to/script.php?query=str#fragment');
		
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
		$uri1 = new uri('example.com');
		$uri2 = new uri('https://user:pass@example.com:777/path/to/script.php?query=str#fragment');
		
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
		$uri1 = new uri('example.com');
		
		// Check For Errors
		$this->assertEmpty($uri1->error);
		
		$this->assertEquals('example.com', $uri1());
	}
	
	
	
}


