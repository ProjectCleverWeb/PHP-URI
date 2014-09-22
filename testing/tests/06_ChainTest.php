<?php

namespace projectcleverweb\uri;

class ChainTest extends URI_Testing_Config {
	
	/**
	 * @test
	 * @depends projectcleverweb\uri\GenerateTest::Reset
	 */
	public function Chain_String_Operations() {
		$uri = $this->uri->minimal;
		
		$uri->chain()
			->replace('HOST', 'google.co')
			->prepend('HOST', 'www.')
			->append('HOST', '.uk')
		;
		$this->assertEquals('www.google.co.uk', $uri->str());
		
		$uri->chain()
			->reset()
			->prepend('HOST', 'www.')
		;
		$this->assertEquals('www.example.com', $uri->str());
	}
	
	/**
	 * @test
	 * @depends projectcleverweb\uri\GenerateTest::Reset
	 */
	public function Chain_Print_Operations() {
		$uri = $this->uri->minimal;
		
		$uri->chain()
			->replace('SCHEME', 'http://')
			->p_str()
		;
		$this->expectOutputString('http://example.com');
	}
	
	/**
	 * @test
	 * @depends projectcleverweb\uri\GenerateTest::Reset
	 */
	public function Chain_Query_Operations() {
		$uri = $this->uri->minimal;
		
		$uri->chain()
			->query_add('a', 'b')
			->query_add('temp', 'temp')
			->query_add('other', '1')
			->query_replace('a', 'c')
			->query_remove('temp')
			->query_rename('a', 'b');
		
		$this->assertSame('example.com?other=1&b=c', $uri->str());
		
		$uri->reset();
		
		$invoke = $uri->chain();
		$invoke()
			->query->add('a', 'b')
			->query->add('temp', 'temp')
			->query->add('other', '1')
			->query->replace('a', 'c')
			->query->remove('temp')
			->query->rename('a', 'b');
		
		$this->assertSame('example.com?other=1&b=c', $uri->str());
	}
	
	/**
	 * @test
	 * @depends projectcleverweb\uri\GenerateTest::Reset
	 */
	public function Chain_Errors() {
		$uri = $this->uri->minimal;
		
		// none of these do anything, but they should all still return the chain class
		@$uri->chain()
			->str()
			->to_string()
			->arr()
			->to_array()
			->path_info()
			->query_array()
			->query_exists()
			->query_get()
			->query->to_array()
			->query->exists()
			->query->get()
			->make_clone();
		
		$this->assertSame(12, $uri->chain()->error_count);
		
		// invalid inputs (no notices)
		$uri->chain()
			->replace('SCHEME', '/invalid/')
			->prepend('SCHEME', '/invalid/')
			->append('SCHEME', '/invalid/')
			->query_rename('does_not_exist', 'nothing')
		;
		$this->assertSame(16, $uri->chain()->error_count);
		
		$this->assertEquals($uri->input, $uri->str());
	}
}
