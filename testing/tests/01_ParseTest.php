<?php

namespace projectcleverweb\uri;

class ParseTest extends URI_Testing_Config {
	
	/**
	 * @test
	 * @depends projectcleverweb\uri\ConfigTest::General_Parse_Errors
	 */
	public function Minimal_Parsing() {
		$this->assertEquals('example.com', $this->uri->minimal->host);
	}
	
	/**
	 * @test
	 * @depends Minimal_Parsing
	 */
	public function Simple_Parsing() {
		$uri = $this->uri->simple;
		$this->assertEquals('http://', $uri->scheme);
		$this->assertEquals('http', $uri->scheme_name);
		$this->assertEquals('://', $uri->scheme_symbols);
		$this->assertEquals('example.com', $uri->host);
		$this->assertEquals('/sample', $uri->path);
	}
	
	/**
	 * @test
	 * @depends Simple_Parsing
	 */
	public function Advanced_Parsing() {
		$uri = $this->uri->advanced1;
		$this->assertEquals('https://', $uri->scheme);
		$this->assertEquals('https', $uri->scheme_name);
		$this->assertEquals('://', $uri->scheme_symbols);
		$this->assertEquals('user', $uri->user);
		$this->assertEquals('pass', $uri->pass);
		$this->assertEquals('example.com', $uri->host);
		$this->assertEquals('777', $uri->port);
		$this->assertEquals('/path/to/script.php', $uri->path);
		$this->assertEquals('query=str', $uri->query);
		$this->assertEquals('fragment', $uri->fragment);
	}
	
	/**
	 * Extended parsing is only for edge-cases. This checks uncommon URI types,
	 * such as FTP, SSH, and email URIs. Most other tests should only require
	 * advanced parsing, unless the test is for something specific to the
	 * edge-case URIs.
	 * 
	 * @test
	 * @depends Advanced_Parsing
	 */
	public function Extended_Parsing() {
		$uri1 = $this->uri->advanced2; // ftp
		$uri2 = $this->uri->advanced3; // skype
		$uri3 = $this->uri->advanced4; // email
		$uri4 = $this->uri->advanced5; // git (ssh)
		
		// ftp
		$this->assertEquals('ftp://', $uri1->scheme);
		$this->assertEquals('ftp', $uri1->scheme_name);
		$this->assertEquals('://', $uri1->scheme_symbols);
		$this->assertEquals('jdoe', $uri1->user);
		$this->assertEquals('Pass123', $uri1->pass);
		$this->assertEquals('example.com', $uri1->host);
		$this->assertEquals('21', $uri1->port);
		$this->assertEquals('/home', $uri1->path);
		// skype
		$this->assertEquals('skype:', $uri2->scheme);
		$this->assertEquals('skype', $uri2->scheme_name);
		$this->assertEquals(':', $uri2->scheme_symbols);
		$this->assertEquals('user123', $uri2->host);
		$this->assertEquals('call=', $uri2->query->str());
		// email
		$this->assertEquals('jdoe', $uri3->user);
		$this->assertEquals('google.com', $uri3->host);
		// git (ssh)
		$this->assertEquals('git', $uri4->user);
		$this->assertEquals('github.com', $uri4->host);
		$this->assertEquals(':ProjectCleverWeb/PHP-URI.git', $uri4->path); // known bug
	}
}
