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
			->p_str();
		
		echo '?';
		
		// print query from chain
		$uri->chain()
			->query->add('1', 'one')
			->query->p_str();
		
		
		$this->expectOutputString('http://example.com?1=one');
	}
	
	/**
	 * @test
	 * @depends projectcleverweb\uri\QueryTest::Query_Reset
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
		
		// normal chain invoke
		$invoke = $uri->chain();
		$invoke()
			->query->add('a', 'b')
			->query->add('temp', 'temp')
			->query->add('other', '1');
		
		// invoking the query chain
		$invoke_query = $uri->chain()->query;
		$invoke_query()
			->query->replace('a', 'c')
			->query->remove('temp')
			->query->rename('a', 'b')
			->query->add('new var', 'new val');
		
		$this->assertEquals('example.com?other=1&b=c&new%20var=new%20val', $uri->str());
		
		// check changing build via chain.
		$uri->chain()
			->query->change_build('', '&', PHP_QUERY_RFC1738);
		
		$this->assertEquals('other=1&b=c&new+var=new+val', $uri->query->to_string());
		
		// check that reset works (does not effect build settings)
		$uri->chain()
			->query->reset()
			->query->add('name', 'test value');
		$this->assertEquals('name=test+value', $uri->query->str());
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
			->query_string()
			->query_array()
			->query_exists()
			->query_get()
			->make_clone()
			->query->str()
			->query->to_string()
			->query->arr()
			->query->to_array()
			->query->exists()
			->query->get()
			->query->make_clone();
		
		$this->assertSame(17, $uri->chain()->error_count);
		
		// invalid inputs (no error notices are produced)
		$uri->chain()
			->replace('SCHEME', '/invalid/')
			->prepend('SCHEME', '/invalid/')
			->append('SCHEME', '/invalid/')
			->query_rename('does_not_exist', 'nothing')
		;
		$this->assertSame(21, $uri->chain()->error_count);
		
		$this->assertEquals($uri->input, $uri->str());
	}
}
