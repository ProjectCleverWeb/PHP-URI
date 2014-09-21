<?php
/**
 * Autoloader for PHP URI Library
 * 
 * Licensed under WTFPL, so have at it.
 * 
 * @author    Nicholas Jordon
 * @link      https://github.com/ProjectCleverWeb
 * @copyright 2014 Nicholas Jordon - All Rights Reserved
 * @version   1.0.0
 * @license   http://www.wtfpl.net/
 */

/**
 * Simple PSR-4 autoloader
 * 
 * This is ignored by code coverage because the autoload is not considered a
 * part of the actual library, and is not required for the library to work in
 * some cases. (such as when loaded by composer)
 * 
 * @codeCoverageIgnore
 */
spl_autoload_register(function ($class) {
	$prefix = 'projectcleverweb\\uri';
	
	$prefix_len = strlen($prefix);
	if(strncmp($prefix, $class, $prefix_len) !== 0) {
		return;
	}
	
	$file = __DIR__.'/src'.str_replace('\\', DIRECTORY_SEPARATOR, substr($class, $prefix_len)).'.php';
	
	if(file_exists($file) && is_file($file)) {
		require_once $file;
	}
});
