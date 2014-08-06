<?php

namespace URI;

/**
 * @requires PHP 5.4
 */
class ModifyTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 * @test
	 * @depends URI\GenerateTest::Reset
	 */
	public function Modify_Scheme_Name() {
		// Test both when there is and isn't pre-existing data
		$uri1 = new \uri('example.com');
		$uri2 = new \uri('https://user:pass@example.com:777/path/to/script.php?query=str#fragment');
		
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
	 * @depends URI\GenerateTest::Reset
	 */
	public function Modify_Scheme_Symbols() {
		// Test both when there is and isn't pre-existing data
		$uri1 = new \uri('example.com');
		$uri2 = new \uri('https://user:pass@example.com:777/path/to/script.php?query=str#fragment');
		
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
	 * @depends URI\GenerateTest::Reset
	 */
	public function Modify_Scheme() {
		// Test both when there is and isn't pre-existing data
		$uri1 = new \uri('example.com');
		$uri2 = new \uri('https://user:pass@example.com:777/path/to/script.php?query=str#fragment');
		
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
		// Check Alias
		$this->assertEquals(
			'git:example.com',
			$uri1->replace('PROTOCOL', 'git:')
		);
	}
	
	/**
	 * @test
	 * @depends URI\GenerateTest::Reset
	 */
	public function Modify_User() {
		// Test both when there is and isn't pre-existing data
		$uri1 = new \uri('example.com');
		$uri2 = new \uri('https://user:pass@example.com:777/path/to/script.php?query=str#fragment');
		$uri3 = new \uri('user@gmail.com'); // user w/out pass
		
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
		// Check Alias
		$this->assertEquals(
			'dude@example.com',
			$uri1->replace('USERNAME', 'dude')
		);
	}
	
	/**
	 * @test
	 * @depends URI\GenerateTest::Reset
	 */
	public function Modify_Pass() {
		// Test both when there is and isn't pre-existing data
		$uri1 = new \uri('jdoe@example.com'); // MUST have a user to have a password
		$uri2 = new \uri('https://user:pass@example.com:777/path/to/script.php?query=str#fragment');
		
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
		// Check Alias
		$this->assertEquals(
			'jdoe:abc123@example.com',
			$uri1->replace('PASSWORD', 'abc123')
		);
	}
	
	/**
	 * @test
	 * @depends URI\GenerateTest::Reset
	 */
	public function Modify_Host() {
		// Test both when there is and isn't pre-existing data
		$uri1 = new \uri('example.com');
		$uri2 = new \uri('https://user:pass@example.com:777/path/to/script.php?query=str#fragment');
		
		// Check For Errors
		$this->assertEmpty($uri1->error);
		$this->assertEmpty($uri2->error);
		
		// Check Replace
		$this->assertEquals(
			'192.168.0.1',
			$uri1->replace('HOST', '192.168.0.1')
		);
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
		// Check Aliases
		$this->assertEquals(
			'facebook.com',
			$uri1->replace('DOMAIN', 'facebook.com')
		);
		$this->assertEquals(
			'example.com',
			$uri1->replace('FQDN', 'example.com')
		);
	}
	
	/**
	 * @test
	 * @depends URI\GenerateTest::Reset
	 */
	public function Modify_Port() {
		// Test both when there is and isn't pre-existing data
		$uri1 = new \uri('example.com');
		$uri2 = new \uri('https://user:pass@example.com:777/path/to/script.php?query=str#fragment');
		
		// Check For Errors
		$this->assertEmpty($uri1->error);
		$this->assertEmpty($uri2->error);
		
		// Check Replace
		$this->assertEquals(
			'example.com:999',
			$uri1->replace('PORT', ':999')
		);
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
	 * @depends URI\GenerateTest::Reset
	 */
	public function Modify_Path() {
		// Test both when there is and isn't pre-existing data
		$uri1 = new \uri('example.com');
		$uri2 = new \uri('https://user:pass@example.com:777/path/to/script.php?query=str#fragment');
		
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
	 * @depends URI\GenerateTest::Reset
	 */
	public function Modify_Query() {
		// Test both when there is and isn't pre-existing data
		$uri1 = new \uri('example.com');
		$uri2 = new \uri('https://user:pass@example.com:777/path/to/script.php?query=str#fragment');
		
		// Check For Errors
		$this->assertEmpty($uri1->error);
		$this->assertEmpty($uri2->error);
		
		// Check Replace
		$this->assertEquals(
			'example.com?my=query',
			$uri1->replace('QUERY', '?my=query')
		);
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
	 * @depends URI\GenerateTest::Reset
	 */
	public function Modify_Fragment() {
		// Test both when there is and isn't pre-existing data
		$uri1 = new \uri('example.com');
		$uri2 = new \uri('https://user:pass@example.com:777/path/to/script.php?query=str#fragment');
		
		// Check For Errors
		$this->assertEmpty($uri1->error);
		$this->assertEmpty($uri2->error);
		
		// Check Replace
		$this->assertEquals(
			'example.com#top',
			$uri1->replace('FRAGMENT', '#top')
		);
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
}
