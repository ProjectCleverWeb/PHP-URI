<?php

namespace projectcleverweb\uri;

/**
 * @requires PHP 5.4
 * @requires PHPUnit 4
 */
class ConfigTest extends URI_Testing_Config {
	
	/**
	 * @test
	 */
	public function General_Parse_Errors() {
		foreach ($this->input as $input => $value) {
			$uri = $this->uri->$input;
			if (stripos($input, 'error') === 0) {
				$this->assertNotEmpty($uri->error, $input);
			} else {
				$this->assertEmpty($uri->error, $input);
			}
		}
	}
}
