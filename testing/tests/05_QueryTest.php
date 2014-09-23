<?php

namespace projectcleverweb\uri;

class QueryTest extends URI_Testing_Config {
	
	/**
	 * @test
	 * @depends projectcleverweb\uri\GenerateTest::Reset
	 */
	public function Query_Add() {
		// Test both when there is and isn't pre-existing data
		$uri1 = $this->uri->minimal;
		$uri2 = $this->uri->advanced1;
		
		$this->assertTrue($uri1->query->add('a', 'b'));
		$this->assertEquals('example.com?a=b', $uri1->str());
		$this->assertTrue($uri1->query->add('1', '2'));
		$this->assertEquals('example.com?a=b&1=2', $uri1->str());
		$this->assertTrue($uri1->query->add('empty', ''));
		$this->assertEquals('example.com?a=b&1=2&empty=', $uri1->str());
		$this->assertFalse($uri2->query->add('query', 'something else'));
		$this->assertEquals($uri2->input, $uri2->str());
	}
	
	/**
	 * @test
	 * @depends projectcleverweb\uri\GenerateTest::Reset
	 */
	public function Query_Replace() {
		// Test both when there is and isn't pre-existing data
		$uri1 = $this->uri->minimal;
		$uri2 = $this->uri->advanced1;
		
		$this->assertNull($uri1->query->replace('a', 'b'));
		$this->assertEquals('example.com?a=b', $uri1->str());
		$this->assertNull($uri1->query->replace('a', 'c'));
		$this->assertEquals('example.com?a=c', $uri1->str());
		$this->assertNull($uri2->query->replace('query', 'abc'));
		$this->assertEquals('https://user:pass@example.com:777/path/to/script.php?query=abc#fragment', $uri2->str());
	}
	
	/**
	 * @test
	 * @depends projectcleverweb\uri\GenerateTest::Reset
	 */
	public function Query_Remove() {
		// Test both when there is and isn't pre-existing data
		$uri1 = $this->uri->minimal;
		$uri2 = $this->uri->advanced1;
		
		$this->assertNull($uri1->query->remove('a'));
		$this->assertEquals($uri1->input, $uri1->str());
		$this->assertNull($uri2->query->remove('query'));
		$this->assertEquals('https://user:pass@example.com:777/path/to/script.php#fragment', $uri2->str());
	}
	
	/**
	 * @test
	 * @depends Query_Add
	 * @depends Query_Remove
	 * @depends Query_Replace
	 */
	public function Build_Query() {
		$uri = $this->uri->average1;
		
		$uri->query->replace('query', 'str');
		$this->assertEquals('query=str', $uri->query_string());
		
		$uri->query->remove('query');
		$uri->query->add(0, 123);
		$uri->query->add('testing', array('one', 'two', 'three'));
		$uri->query->change_build('pf_', '&amp;', PHP_QUERY_RFC1738);
		$this->assertEquals('pf_0=123&amp;testing%5B0%5D=one&amp;testing%5B1%5D=two&amp;testing%5B2%5D=three', $uri->query->str());
		
		// Check parsing after build settings have been changed
		$uri->query = $uri->query->str();
		$this->assertEquals(123, $uri->query->data['pf_0']); // Expected Behavior: Numeric array keys on root array now have a prefix
		$this->assertEquals(array('one', 'two', 'three'), $uri->query->data['testing']);
	}
	
	/**
	 * @test
	 * @depends projectcleverweb\uri\GenerateTest::Reset
	 */
	public function Query_Exists() {
		// Test both when there is and isn't pre-existing data
		$uri1 = $this->uri->minimal;
		$uri2 = $this->uri->advanced1;
		$uri3 = new uri('google.com?empty'); // make sure empty queries keys work
		
		$this->assertFalse($uri1->query->exists('a'));
		$this->assertTrue($uri2->query->exists('query'));
		$this->assertTrue($uri3->query_exists('empty')); // test the alias too
	}
	
	/**
	 * @test
	 * @depends projectcleverweb\uri\GenerateTest::Reset
	 */
	public function Query_Get() {
		// Test both when there is and isn't pre-existing data
		$uri1 = $this->uri->minimal;
		$uri2 = $this->uri->advanced1;
		$uri3 = new uri('google.com?empty'); // make sure empty queries keys work
		
		$this->assertNull($uri1->query->get('a'));
		$this->assertEquals('str', $uri2->query->get('query'));
		$this->assertSame('', $uri3->query_get('empty')); // test the alias too
	}
	
	/**
	 * @test
	 * @depends projectcleverweb\uri\GenerateTest::Reset
	 */
	public function Query_Rename() {
		// Test both when there is and isn't pre-existing data
		$uri1 = $this->uri->minimal;
		$uri2 = $this->uri->advanced1;
		$uri3 = new uri('google.com?empty'); // make sure empty queries keys work
		
		$this->assertFalse($uri1->query->rename('a', 'b'));
		$this->assertEquals($uri1->input, $uri1->str());
		$this->assertTrue($uri2->query->rename('query', 'test'));
		$this->assertEquals('https://user:pass@example.com:777/path/to/script.php?test=str#fragment', $uri2->str());
		$this->assertTrue($uri3->query->rename('empty', 'none'));
		$this->assertEquals('google.com?none=', $uri3->str());
	}
	
	/**
	 * @test
	 * @depends projectcleverweb\uri\GenerateTest::Reset
	 */
	public function Query_Array() {
		$query = $this->uri->advanced1->query;
		$this->assertSame($query->data, $query->arr());
		$this->assertSame($query->arr(), $query->to_array());
	}
	
	/**
	 * @test
	 * @depends Query_Add
	 */
	public function Query_Reset() {
		$query = $this->uri->advanced1->query;
		$temp  = $query->data;
		$query->add('test');
		$query->reset();
		$this->assertSame($temp, $query->data);
	}
	
	/**
	 * @test
	 * @depends projectcleverweb\uri\GenerateTest::Reset
	 */
	public function Invoke_Query() {
		$query = $this->uri->advanced1->query;
		$this->assertSame($query, $query());
	}
	
	/**
	 * @test
	 * @depends Query_Add
	 * @depends Query_Replace
	 */
	public function Clone_Query() {
		$query1 = $this->uri->advanced1->query;
		$query2 = clone $query1;
		$query3 = $query1->make_clone();
		
		$query2->replace('query', 'testValue');
		$query3->add('new', 'one');
		
		$this->assertSame(
			array('query' => 'str'),
			$query1->data
		);
		$this->assertSame(
			array('query' => 'testValue'),
			$query2->data
		);
		$this->assertSame(
			array('query' => 'str', 'new' => 'one'),
			$query3->data
		);
	}
}
