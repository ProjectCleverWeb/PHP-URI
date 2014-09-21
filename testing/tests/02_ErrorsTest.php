<?php

namespace projectcleverweb\uri;

class ErrorsTest extends URI_Testing_Config {
	
	/**
	 * @test
	 * @depends projectcleverweb\uri\ParseTest::Advanced_Parsing
	 */
	public function Invalid_Section_Errors() {
		$uri = $this->uri->advanced1;
		$this->assertFalse($uri->append('invalid', ''));
		$this->assertFalse($uri->prepend('invalid', ''));
		$this->assertFalse($uri->replace('invalid', ''));
	}
	
	/**
	 * @test
	 * @depends projectcleverweb\uri\ParseTest::Advanced_Parsing
	 */
	public function Invalid_Scheme() {
		$uri = $this->uri->advanced1;
		$this->assertFalse($uri->replace('SCHEME_NAME', 'invalid/'));
		$this->assertFalse($uri->replace('SCHEME_SYMBOLS', 'invalid'));
		$this->assertFalse($uri->replace('SCHEME', 'invalid_scheme'));
	}
	
	/**
	 * @test
	 * @depends projectcleverweb\uri\ParseTest::Advanced_Parsing
	 */
	public function Invalid_Host() {
		$this->assertFalse($this->uri->advanced1->replace('HOST', 'invalid'));
	}
	
	/**
	 * @test
	 * @depends projectcleverweb\uri\ParseTest::Advanced_Parsing
	 */
	public function Invalid_Port() {
		$this->assertFalse($this->uri->advanced1->replace('PORT', 'invalid'));
	}
}
