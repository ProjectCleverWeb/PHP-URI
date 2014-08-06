<?php

namespace URI;

/**
 * @requires PHP 5.4
 */
class QueryTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 * @test
	 * @depends URI\GenerateTest::Reset
	 */
	public function Query_Add() {
		// Test both when there is and isn't pre-existing data
		$uri1 = new \uri('example.com');
		$uri2 = new \uri('https://user:pass@example.com:777/path/to/script.php?query=str#fragment');
		
		// Check For Errors
		$this->assertEmpty($uri1->error);
		$this->assertEmpty($uri2->error);
		
		$this->assertTrue($uri1->query_add('a', 'b'));
		$this->assertEquals('example.com?a=b', $uri1->str());
		$this->assertTrue($uri1->query_add('1', '2'));
		$this->assertEquals('example.com?a=b&1=2', $uri1->str());
		$this->assertTrue($uri1->query_add('empty', ''));
		$this->assertEquals('example.com?a=b&1=2&empty=', $uri1->str());
		$this->assertFalse($uri2->query_add('query', 'something else'));
		$this->assertEquals($uri2->input, $uri2->str());
	}
	
	/**
	 * @test
	 * @depends URI\GenerateTest::Reset
	 */
	public function Query_Replace() {
		// Test both when there is and isn't pre-existing data
		$uri1 = new \uri('example.com');
		$uri2 = new \uri('https://user:pass@example.com:777/path/to/script.php?query=str#fragment');
		
		// Check For Errors
		$this->assertEmpty($uri1->error);
		$this->assertEmpty($uri2->error);
		
		$this->assertNull($uri1->query_replace('a', 'b'));
		$this->assertEquals('example.com?a=b', $uri1->str());
		$this->assertNull($uri1->query_replace('a', 'c'));
		$this->assertEquals('example.com?a=c', $uri1->str());
		$this->assertNull($uri2->query_replace('query', 'abc'));
		$this->assertEquals('https://user:pass@example.com:777/path/to/script.php?query=abc#fragment', $uri2->str());
	}
	
	/**
	 * @test
	 * @depends URI\GenerateTest::Reset
	 */
	public function Query_Remove() {
		// Test both when there is and isn't pre-existing data
		$uri1 = new \uri('example.com');
		$uri2 = new \uri('https://user:pass@example.com:777/path/to/script.php?query=str&test=true#fragment');
		
		// Check For Errors
		$this->assertEmpty($uri1->error);
		$this->assertEmpty($uri2->error);
		
		$this->assertNull($uri1->query_remove('a'));
		$this->assertEquals($uri1->input, $uri1->str());
		$this->assertNull($uri2->query_remove('query'));
		$this->assertEquals('https://user:pass@example.com:777/path/to/script.php?test=true#fragment', $uri2->str());
		$this->assertNull($uri2->query_remove('test'));
		$this->assertEquals('https://user:pass@example.com:777/path/to/script.php#fragment', $uri2->str());
	}
	
	/**
	 * @test
	 * @depends URI\GenerateTest::Reset
	 */
	public function Query_Exists() {
		// Test both when there is and isn't pre-existing data
		$uri1 = new \uri('example.com');
		$uri2 = new \uri('https://user:pass@example.com:777/path/to/script.php?query=str#fragment');
		$uri3 = new \uri('google.com?empty');
		
		// Check For Errors
		$this->assertEmpty($uri1->error);
		$this->assertEmpty($uri2->error);
		$this->assertEmpty($uri3->error);
		
		$this->assertFalse($uri1->query_exists('a'));
		$this->assertTrue($uri2->query_exists('query'));
		$this->assertTrue($uri3->query_exists('empty'));
	}
	
	/**
	 * @test
	 * @depends URI\GenerateTest::Reset
	 */
	public function Query_Get() {
		// Test both when there is and isn't pre-existing data
		$uri1 = new \uri('example.com');
		$uri2 = new \uri('https://user:pass@example.com:777/path/to/script.php?query=str#fragment');
		
		// Check For Errors
		$this->assertEmpty($uri1->error);
		$this->assertEmpty($uri2->error);
		
		$this->assertNull($uri1->query_get('a'));
		$this->assertEquals('str', $uri2->query_get('query'));
	}
	
	/**
	 * @test
	 * @depends URI\GenerateTest::Reset
	 */
	public function Query_Rename() {
		// Test both when there is and isn't pre-existing data
		$uri1 = new \uri('example.com');
		$uri2 = new \uri('https://user:pass@example.com:777/path/to/script.php?query=str#fragment');
		$uri3 = new \uri('google.com?empty');
		
		// Check For Errors
		$this->assertEmpty($uri1->error);
		$this->assertEmpty($uri2->error);
		
		$this->assertFalse($uri1->query_rename('a', 'b'));
		$this->assertEquals($uri1->input, $uri1->str());
		$this->assertTrue($uri2->query_rename('query', 'test'));
		$this->assertEquals('https://user:pass@example.com:777/path/to/script.php?test=str#fragment', $uri2->str());
		$this->assertTrue($uri3->query_rename('empty', 'none'));
		$this->assertEquals('google.com?none=', $uri3->str());
	}
}
