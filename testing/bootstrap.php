<?php


spl_autoload_register(function ($class) {
	$prefix = 'projectcleverweb\\uri';
	$base_dir = realpath(__DIR__.'/../src');
	
	$prefix_len = strlen($prefix);
	if(strncmp($prefix, $class, $prefix_len) !== 0) {
		return;
	}
	
	$file = $base_dir.str_replace('\\', DIRECTORY_SEPARATOR, substr($class, $prefix_len)).'.php';
	
	if(file_exists($file) && is_file($file)) {
		require_once $file;
	}
});


class uri extends \projectcleverweb\uri\main {}
