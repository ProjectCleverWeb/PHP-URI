<?php

namespace URI;

/**
 * @requires PHP 5.4
 */
class ChainTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 * @test
	 * @depends URI\GenerateTest::Reset
	 */
	public function Chain_String_Operations() {
		$uri1 = new \uri('example.com');
		
		// Check For Errors
		$this->assertEmpty($uri1->error);
		
		$uri1->chain()
			->replace('HOST', 'google.co')
			->prepend('HOST', 'www.')
			->append('HOST', '.uk')
		;
		$this->assertEquals('www.google.co.uk', $uri1->str());
		
		$uri1->chain()
			->reset()
			->prepend('HOST', 'www.')
		;
		$this->assertEquals('www.example.com', $uri1->str());
	}
	
	/**
	 * @test
	 * @depends URI\GenerateTest::Reset
	 */
	public function Chain_Print_Operations() {
		$uri1 = new \uri('example.com');
		
		// Check For Errors
		$this->assertEmpty($uri1->error);
		
		$uri1->chain()
			->replace('SCHEME', 'http://')
			->p_str()
		;
		$this->expectOutputString('http://example.com');
	}
	
	/**
	 * @test
	 * @depends URI\GenerateTest::Reset
	 */
	public function Chain_Query_Operations() {
		$uri1 = new \uri('example.com');
		
		// Check For Errors
		$this->assertEmpty($uri1->error);
		
		$uri1->chain()
			->query_add('a', 'b')
			->query_add('temp', 'temp')
			->query_add('other', '1')
			->query_replace('a', 'c')
			->query_remove('temp')
			->query_rename('a', 'b')
		;
		$this->assertSame('example.com?other=1&b=c', $uri1->str());
	}
	
	/**
	 * @test
	 * @depends URI\GenerateTest::Reset
	 */
	public function Chain_Errors() {
		$uri1 = new \uri('example.com');
		
		// Check For Errors
		$this->assertEmpty($uri1->error);
		
		// none of these do anything, but they should all still return the chain class
		@$uri1->chain()
			->str()
			->to_string()
			->arr()
			->to_array()
			->path_info()
			->query_array()
			->query_exists()
			->query_get()
		;
		
		$this->assertSame(8, $uri1->chain()->error_count);
		
		// invalid inputs (no notices)
		$uri1->chain()
			->replace('SCHEME', '/invalid/')
			->prepend('SCHEME', '/invalid/')
			->append('SCHEME', '/invalid/')
			->query_rename('does_not_exist', 'nothing')
		;
		$this->assertSame(12, $uri1->chain()->error_count);
		
		$this->assertEquals($uri1->input, $uri1->str());
	}
}
